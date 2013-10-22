<?php
/**
*
* @package testing
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../includes/tests/tests_modx.php';
require_once dirname(__FILE__) . '/../mock/modx_object.php';
require_once dirname(__FILE__) . '/../mock/modx_license.php';
require_once dirname(__FILE__) . '/../mock/modx_version.php';

class phpbb_modx_test extends phpbb_test_case
{
	private $test;

	public static function provider()
	{
		return array(
			array('1.0.0', false, true),
			array('1.0.0-pl1', false, true),
			array('1.0.0-b1', 'MAJOR_VERSION_UNSTABLE', false),
			array('0.1.0', 'MAJOR_VERSION_UNSTABLE', false),
			array('1.0.0 update to 1.0.1', 'MAJOR_VERSION_UNSTABLE', false),
			array('1.0.0a1', 'MAJOR_VERSION_UNSTABLE', false),
			array('1.0.0foobar', 'MAJOR_VERSION_UNSTABLE', false),
			array('foobar', 'INVALID_VERSION_FORMAT', false),
			array('2.15.3', false, true),
			array('19.1.2', false, true),
			array('1.2.15', false, true),
		);
	}

	protected function setUp()
	{
		parent::setUp();

		$this->test = new mpv_tests_modx(new mpv());
		$this->test->modx_object = new phpbb_mock_modx_object();
		$this->version = new phpbb_mock_modx_version();
	}

	/**
	* @dataProvider provider
	*/
	public function test_version($test, $expected_error, $expected_result)
	{
		global $user;

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		$this->version->value = $test;
		$this->test->modx_object->set_xpath($this->version);
		$result = $this->test->unittest('test_version', array());

		$this->assertEquals($expected_result, $result);
	}

	public function test_version_no_object()
	{
		$this->setExpectedTriggerError(E_USER_ERROR, 'VERSION_FIELD_MISSING');
		$this->version = '1.0.0';
		$this->test->modx_object->set_xpath($this->version);
		$result = $this->test->unittest('test_version', array());
		$this->assertEquals(false, $result);
	}

	public static function data_modx_license()
	{
		return array(
			array('http://opensource.org/licenses/gpl-license.php GNU General Public License v2', false, true),
			array('http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2', false, true),
			array('http://opensource.org/licenses/MIT', 'LICENSE_NOT_GPL2', false),
			array(null, 'LICENSE_FIELD_MISSING', false),
		);
	}

	/**
	* @dataProvider data_modx_license
	*/
	public function test_modx_license($test, $expected_error, $expected_result)
	{
		global $user;

		if ($expected_error !== false)
		{
			$this->setExpectedTriggerError(E_USER_ERROR, $expected_error);
		}

		if (isset($test))
		{
			$this->license = new phpbb_mock_modx_license();
			$this->license->value = $test;
		}
		else
		{
			$this->license = $test;
		}
		$this->test->modx_object->set_by_name('license', $this->license);
		$result = $this->test->unittest('test_license', array());

		$this->assertEquals($expected_result, $result);
	}
}
