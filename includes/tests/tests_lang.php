<?php
/**
* Tests for checking the language files of a MOD
*
* @package mpv
* @version $Id$
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

/**
 * Collection of tests which are ran on the MOD's language files
 *
 * @package		mpv
 * @subpackage	tests
 */
class mpv_tests_lang extends test_base
{
	/**
	 * BOM (utf-8 byte order mark) in string form for comparison
	 *
	 * @access	private
	 * @var		string
	 */
	private $bom_char = "\xef\xbb\xbf";

	/**
	 * Run the tests in this collection
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function run()
	{
		$test_methods = get_class_methods($this);

		if (!is_array($this->validator->package_files) || !sizeof($this->validator->package_files))
		{
			return;
		}

		foreach ($this->validator->package_files as $package_file)
		{
			// only test language files
			/*if (!preg_match($package_file, '#language/.+\.php#'))
			{
				continue;
			}*/
			
			if (mpv::check_unwanted($package_file))
			{
				continue;
			}			

			$this->failed_tests = array();

			$this->file_name = $package_file;
			$this->file_contents = file_get_contents($this->validator->temp_dir . $package_file);
			$this->file_contents_file = file($this->validator->temp_dir . $package_file);

			foreach ($test_methods as $method)
			{
				if (substr($method, 0, 5) == 'test_')
				{
					if (!$this->$method() || $this->terminate)
					{
						$this->failed_tests[] = substr($method, 5);
					}

					if ($this->terminate)
					{
						unset($this->file_contents, $this->file_contents_file);

						return;
					}
				}
			}

			unset($this->file_contents, $this->file_contents_file);
		}
	}

	/**
	 * Test for the use of an UTF-8 byte-order-mark (BOM)
	 *
	 * @access	private
	 * @return	bool
	 */
	protected function test_bom()
	{
		if (substr($this->file_contents, 0, 3) === $this->bom_char)
		{
			$this->push_error(mpv::ERROR_WARNING, 'USAGE_BOM');
			return false;
		}

		return true;
	}
}
