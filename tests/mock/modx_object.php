<?php
/**
*
* @package testing
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_mock_modx_object
{
	private $xpath;

	private $data_ary;

	public function get_xpath($foo, $bar = true)
	{
		return $this->xpath;
	}

	public function set_xpath($foo)
	{
		$this->xpath = $foo;
	}

	public function get_by_name($name, $return = true)
	{
		return (isset($this->data_ary[$name])) ? $this->data_ary[$name] : false;
	}

	public function set_by_name($name, $value)
	{
		$this->data_ary[$name] = $value;
	}
}
