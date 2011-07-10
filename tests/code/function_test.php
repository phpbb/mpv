<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_function_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(
			array('testcode/dbal/mysql', false, true),
			array('testcode/functions/eval', 'USAGE_EVAL', false),
			array('testcode/functions/exec', 'USAGE_EXEC', false),
			array('testcode/functions/system', 'USAGE_SYSTEM', false),
			array('testcode/functions/passthru', 'USAGE_PASSTHRU', false),
			array('testcode/functions/getenv', 'USAGE_GETENV', false),
			array('testcode/functions/die', 'USAGE_DIE', false),
			array('testcode/functions/sha1', 'USAGE_SHA1', false),
			array('testcode/functions/addslashes', 'USAGE_ADDSLASHES', false),
			array('testcode/functions/htmlspecialchars', 'USAGE_HTMLSPECIALCHARS', false),
			array('testcode/functions/stripslashes', 'USAGE_STRIPSLASHES', false),
			
			array('testcode/functions/backticks', 'USAGE_`', false),

			array('testcode/functions/include_once', 'USAGE_INCLUDEONCE', false),
			array('testcode/functions/include_once2', 'USAGE_INCLUDEONCE', false),
			array('testcode/functions/require_once', 'USAGE_REQUIREONCE', false),
			array('testcode/functions/require_once2', 'USAGE_REQUIREONCE', false),
			array('testcode/functions/md5', 'USAGE_MD5', false),
			array('testcode/functions/md52', false, true),
			array('testcode/short2', false, true),			
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
	public function test_function($test, $expected_error, $expected_result)
	{
		global $user;
		$this->test->setFilename('tests/code/' . $test);

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$result = $this->test->unittest('test_code', array());

		if ($expected_error === false)
		$this->assertEquals($expected_result, $result);
	}
}

