<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_global_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(
			array("<?php\n\$GLOBALS['test'] = 'test'\n?>", 'USAGE_GLOBALS', false),
			array("<?php\nif (\$HTTP_POST_VARS['test'])\necho 'test';\n?>", 'USAGE_HTTPPOSTVARS', false),
			array("<?php\nif (isset(\$HTTP_POST_VARS['test']))\necho 'test';\n?>", 'USAGE_HTTPPOSTVARS', false),

			array("<?php\nif (\$HTTP_GET_VARS['test'])\necho 'test';\n?>", 'USAGE_HTTPGETVARS', false),
			array("<?php\nif (isset(\$HTTP_GET_VARS['test']))\necho 'test';\n?>", 'USAGE_HTTPGETVARS', false),
			
			array("<?php\nif (\$HTTP_SERVER_VARS['test'])\necho 'test';\n?>", 'USAGE_HTTPSERVERVARS', false),
			array("<?php\nif (isset(\$HTTP_SERVER_VARS['test']))\necho 'test';\n?>", 'USAGE_HTTPSERVERVARS', false),			
			
			array("<?php\nif (\$HTTP_ENV_VARS['test'])\necho 'test';\n?>", 'USAGE_HTTPENVVARS', false),
			array("<?php\nif (isset(\$HTTP_ENV_VARS['test']))\necho 'test';\n?>", 'USAGE_HTTPENVVARS', false),			
			
			array("<?php\nif (\$HTTP_COOKIE_VARS['test'])\necho 'test';\n?>", 'USAGE_HTTPCOOKIEVARS', false),
			array("<?php\nif (isset(\$HTTP_COOKIE_VARS['test']))\necho 'test';\n?>", 'USAGE_HTTPCOOKIEVARS', false),			
			
			array("<?php\nif (\$HTTP_POST_FILES['test'])\necho 'test';\n?>", 'USAGE_HTTPPOSTFILES', false),
			array("<?php\nif (isset(\$HTTP_POST_FILES['test']))\necho 'test';\n?>", 'USAGE_HTTPPOSTFILES', false),			
			
			array("<?php\nif (\$HTTP_SESSION_VARS['test'])\necho 'test';\n?>", 'USAGE_HTTPSESSIONVARS', false),
			array("<?php\nif (isset(\$HTTP_SESSION_VARS['test']))\necho 'test';\n?>", 'USAGE_HTTPSESSIONVARS', false),			
			
			array("<?php\nif (\$_FILES['test'])\necho 'test';\n?>", 'USAGE_FILES', false),
			array("<?php\nif (isset(\$_FILES['test']))\necho 'test';\n?>", 'USAGE_FILES', false),			
			
			array("<?php\nif (\$_SESSION['test'])\necho 'test';\n?>", 'USAGE_SESSION', false),
			array("<?php\nif (isset(\$_SESSION['test']))\necho 'test';\n?>", false, true),				
			
			array("<?php\nif (\$_SERVER['test'])\necho 'test';\n?>", 'USAGE_SERVER', false),
			array("<?php\nif (isset(\$_SERVER['test']))\necho 'test';\n?>", false, true),					
			
			array("<?php\nif (\$_ENV['test'])\necho 'test';\n?>", 'USAGE_ENV', false),
			array("<?php\nif (isset(\$_ENV['test']))\necho 'test';\n?>", false, true),					
			
			array("<?php\nif (\$_REQUEST['test'])\necho 'test';\n?>", 'USAGE_REQUEST', false),
			array("<?php\nif (isset(\$_REQUEST['test']))\necho 'test';\n?>", false, true),					
			
			array("<?php\nif (\$_POST['test'])\necho 'test';\n?>", 'USAGE_POST', false),
			array("<?php\nif (isset(\$_POST['test']))\necho 'test';\n?>", false, true),					
			
			array("<?php\nif (\$_GET['test'])\necho 'test';\n?>", 'USAGE_GET', false),
			array("<?php\nif (isset(\$_GET['test']))\necho 'test';\n?>", false, true),					
			
			array("<?php\nif (\$_COOKIE['test'])\necho 'test';\n?>", 'USAGE_COOKIE', false),
			array("<?php\n//if (\$_COOKIE['test'])\necho 'test';\n?>", false, true),
			array("<?php\nif (isset(\$_COOKIE['test']))\necho 'test';\n?>", false, true),					
			
			array("<?php\nif (\$test)\necho 'test';\n?>", false, true),
			array("<?php\nif (isset(\$test))\necho 'test';\n?>", false, true),								
								
		);
	}
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->test = new mpv_tests_code(new mpv);
	}

	/**
	* @dataProvider provider
	*/
	public function test_global($test, $expected_error, $expected_result)
	{
		global $user;
		$this->test->setCode('tests/code/' . $test);

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$result = $this->test->unittest('test_globals', array());

		if ($expected_error === false)
		$this->assertEquals($expected_result, $result);
	}
}

