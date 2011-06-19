<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_echo_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(
	
			
			array("<?php\nprint_r(\$_SERVER);\n?>", 'USAGE_PRINTR', false),
			array("<?php\n//print_r(\$_SERVER);\n?>", false, true),
			array("<?php\n/*\n\nprint_r(\$_SERVER);\n\n*/\n?>", false, true),
			
			array("<?php\nvar_Dump(\$_SERVER);\n?>", 'USAGE_VARDUMP', false),
			array("<?php\nvar_dump(\$_SERVER);\n?>", 'USAGE_VARDUMP', false),
			array("<?php\n//var_dump(\$_SERVER);\n?>", false, true),
			array("<?php\n/*\n\nvar_dump(\$_SERVER);\n\n*/\n?>", false, true),			
			
			array("<?php\nprintf(\$_SERVER);\n?>", 'USAGE_PRINTF', false),
			array("<?php\n//printf(\$_SERVER);\n?>", false, true),
			array("<?php\n/*\n\nprintf(\$_SERVER);\n\n*/\n?>", false, true),			
								
								
			array("<?php\necho(\$_SERVER);\n?>", 'USAGE_ECHO', false),
			array("<?php\n//echo(\$_SERVER);\n?>", false, true),
			array("<?php\n/*\n\necho(\$_SERVER);\n\n*/\n?>", false, true),					
			array("<?php\necho \$_SERVER;\n?>", 'USAGE_ECHO', false),
			array("<?php\n//echo \$_SERVER;\n?>", false, true),
			array("<?php\n/*\n\necho \$_SERVER;\n\n*/\n?>", false, true),								
			
			array("<?php\nprint(\$_SERVER);\n?>", 'USAGE_PRINT', false),
			array("<?php\n//print(\$_SERVER);\n?>", false, true),
			array("<?php\n/*\n\nprint(\$_SERVER);\n\n*/\n?>", false, true),											
			array("<?php\nprint \$_SERVER ;\n?>", 'USAGE_PRINT', false),
			array("<?php\n//print \$_SERVER ;\n?>", false, true),
			array("<?php\n/*\n\nprint \$_SERVER ;\n\n*/\n?>", false, true),					
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

		$result = $this->test->unittest('test_echo', array());

		if ($expected_error === false)
		$this->assertEquals($expected_result, $result);
	}
}


