<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_in_phpbb_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(/*
			array('testcode/short1', 'SHORT_TAGS', false),
			array('testcode/short2', 'SHORT_TAGS', false),
			array('testcode/short3', 'SHORT_TAGS', false),
			array('testcode/short4', 'SHORT_TAGS', false),*/
			array('mcp/info/mcp_test.php', false, true),			
			array('acp/info/acp_test.php', false, true),			
			array('ucp/info/ucp_test.php', false, true),
			array('testcode/inphpbb.php', false, true),
			array('testcode/inphpbb3.php', false, true),
			array('testcode/phpbb.php', false, true),
			array('testcode/noinphpbb.php', 'NO_IN_PHPBB', false),
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
	public function test_in_phpbb($test, $expected_error, $expected_result)
	{
		global $user;
		$this->test->setFilename('tests/code/' . $test);

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$result = $this->test->unittest('test_in_phpbb', array());

		$this->assertEquals($expected_result, $result);
	}
}

