<?php
/**
 * Cortex base class
 *
 * @package		Cortex
 * @copyright	2006-2008 Coronis - http://www.coronis.nl
 * @license		http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author		Vic D'Elfant
 *
 * @version		$Id$
 */

/**
 * cortex_base class
 *
 * @package		Cortex
 */
class cortex_base
{
	/**
	 * This file's revision number
	 *
	 * @access	public
	 * @var		string
	 */
	const revision = '$Rev$';

	/**
	 * Getter, will attempt to call $this->__get_<property>()
	 *
	 * @access	public
	 * @param	string		Property name
	 * @return	mixed		Property value
	 */
	public function __get($property_name)
	{
		$method_name = '__get_' . $property_name;
		if (method_exists($this, $method_name))
		{
			return $this->$method_name();
		}

		if (!property_exists($this, $property_name))
		{
			throw new exception('Trying to get non-existing property ' . htmlspecialchar(get_class($this)) . '->' . htmlspecialchar($property_name));
		}

		return $this->$property_name;
	}

	/**
	 * Setter, will attempt to call $this->__set_<property>()
	 *
	 * @access	public
	 * @param	string		Property name
	 * @param	mixed		Property value
	 * @return	void
	 */
	public function __set($property_name, $property_value)
	{
		$method_name = '__set_' . $property_name;
		if (method_exists($this, $method_name))
		{
			$this->$method_name($property_value);
		}
		else
		{
			$this->$property_name = $property_value;
		}
	}
}
?>
