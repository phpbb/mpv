<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_request_var_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(
			array('testcode/requestVar1', false, true),
			array('testcode/requestVar2', 'USAGE_REQUEST_VAR_INT', false),
			array('testcode/requestVar3', false, true),
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
	public function test_request_var($test, $expected_error, $expected_result)
	{
		global $user;
		$this->test->setFilename('tests/code/' . $test);

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$result = $this->test->unittest('test_request_var', array());

		$this->assertEquals($expected_result, $result);
	}
}

