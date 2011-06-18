<?php
/**
*
* @package testing
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_code.php';

class phpbb_mysql_test extends phpbb_test_case
{
	private $test;
	public static function provider()
	{
		// array(Input -> redirect(), expected triggered error (else false), expected returned result url (else false))
		return array(
			array('testcode/dbal/mysql', 'USAGE_MYSQL', false),
			array('testcode/dbal/mysqli', 'USAGE_MYSQLI', false),
			array('testcode/dbal/oci', 'USAGE_OCI', false),
			array('testcode/dbal/sqlite', 'USAGE_SQLITE', false),
			array('testcode/dbal/pg', 'USAGE_PG', false),
			array('testcode/dbal/mssql', 'USAGE_MSSQL', false),
			array('testcode/dbal/odbc', 'USAGE_ODBC', false),
			array('testcode/dbal/sqlsrv', 'USAGE_SQLSRV', false),
			array('testcode/dbal/ibase', 'USAGE_IBASE', false),
			array('testcode/dbal/db2', 'USAGE_DB2', false),
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
	public function test_mysql($test, $expected_error, $expected_result)
	{
		global $user;
		$this->test->setFilename('tests/code/' . $test);

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$result = $this->test->unittest('test_dbal', array());

		$this->assertEquals($expected_result, $result);
		
	}
}

