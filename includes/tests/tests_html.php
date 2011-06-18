<?php
/**
* Tests for testing/auditing the html code of the MOD
*
* @package mpv
* @version $Id$
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

/**
 * Collection of tests which are ran on the MOD's code
 *
 * @package		mpv
 * @subpackage	tests
 */
class mpv_tests_html extends test_base
{
	/**
	 * Run the tests in this collection
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function run()
	{
		$test_methods = get_class_methods($this);

		$files = array();

		if (is_array($this->validator->package_files))
		{
			$files = array_merge($files, $this->validator->package_files);
		}

		if (is_array($this->validator->modx_files))
		{
			$files = array_merge($files, $this->validator->modx_files);
		}

		foreach ($files as $package_file)
		{
			if (mpv::check_unwanted($package_file))
			{
				continue;
			}		
		
			// Only test html, php, xml files
			if (!in_array(strrchr($package_file, '.'), array('.php', '.xml', '.html')))
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
	 * Test for non closed BR tags
	 *
	 * @return bool
	 */
	private function test_br()
	{
		$return = true;

		if (preg_match('#<br(\s+)?>#', $this->file_contents))
		{
			$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_BR_NON_CLOSED', false, '#<br(\s+)?>#');
		}

		return $return;
	}
	/**
	 * Test for non closed IMG tags
	 *
	 * @return bool
	 */
	private function test_img()
	{
		$return = true;
		/*
		 * Disable this check for now, it doesnt detect it correctly atm.
		if (preg_match('#<img\s.+(?<!/)>#', $this->file_contents))
		{
			$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_IMG_NON_CLOSED', false, '#<img\s.+(?<!/)>#');
		}
		*/

		return $return;
	}
}
