<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_include_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(
			array('testcode/include/include1', 'INCLUDE_NO_ROOT', false),
			array('testcode/include/includeonce1', 'INCLUDE_NO_ROOT', false),
			array('testcode/include/require1', 'INCLUDE_NO_ROOT', false),
			array('testcode/include/requireonce1', 'INCLUDE_NO_ROOT', false),
			
			array('testcode/include/include2', 'INCLUDE_NO_PHP', false),
			array('testcode/include/includeonce2', 'INCLUDE_NO_PHP', false),
			array('testcode/include/require2', 'INCLUDE_NO_PHP', false),
			array('testcode/include/requireonce2', 'INCLUDE_NO_PHP', false),	
			
			array('testcode/include/include3', false, true),		
			array('testcode/include/include4', 'INCLUDE_NO_ROOT', false),	
			array('testcode/include/include5', 'INCLUDE_NO_PHP', false),	
			array('testcode/include/include6', false, true),	
			array('testcode/include/include7', false, true),	
			
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
	public function test_include($test, $expected_error, $expected_result)
	{
		global $user;
		$this->test->setFilename('tests/code/' . $test);

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$result = $this->test->unittest('test_include', array());

		$this->assertEquals($expected_result, $result);
	}
}

