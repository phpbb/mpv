<?php
/**
* Tests to check if there are any parse errors in the file.
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
class mpv_tests_execution extends test_base
{
	/**
	 * Run the tests in this collection
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function run()
	{
		// Make sure these tests are enabled in MPV.
		// These tests are for security reasons disabled by default.
		// Only enable these tests if you know what you do and
		// if this MPV install isnt public usable.
		// These tests are NOT enabled at the online MPV at phpBB.com
		// if you want to use these tests you need to install MPV
		// locally.
		// If you use these tests is it at your own risk.
		
		if (mpv::$exec_php !== mpv::EXEC_PHP)
		{
			return;
		}
		
		$test_methods = get_class_methods($this);

		if (!is_array($this->validator->package_files) || !sizeof($this->validator->package_files))
		{
			return;
		}

		foreach ($this->validator->package_files as $package_file)
		{
			if (mpv::check_unwanted($package_file))
			{
				continue;
			}		
		
			$this->file_name = $package_file;

			// Only test PHP files
			if (substr($package_file, -3) !== 'php')

			{
				continue;
			}

			$this->failed_tests = array();

			$this->file_contents = file_get_contents($this->validator->temp_dir . $package_file);

			$this->file_contents_file = file($this->validator->temp_dir . $package_file);

			unset($content);

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
	 * Tests if there are any syntax errors by default.
	 */
	private function test_php()
	{
	  
		$file = tempnam(sys_get_temp_dir(), 'mpv');
		$file2 = tempnam(sys_get_temp_dir(), 'mpv');
		$open = @fopen($file, 'wb');

		if (!$open)
		{
			$this->push_error(mpv::ERROR_NOTICE, 'UNABLE_OPEN', $file);
			return false;
		}
		foreach(file($this->validator->temp_dir . $this->file_name) as $line)
		{
			$result = @fwrite($open, "$line\n");

			if (!$result)
			{
				fclose($open);
				$this->push_error(mpv::ERROR_NOTICE, 'UNABLE_WRITE', $file);
	
				return;
			}
		}
		@fclose($file);
		$result = array();
		$data = @exec('php -l ' . escapeshellarg($file) . " 2>&1 >/dev/null", $result);
		@unlink($file);

		if (sizeof($result))
		{
			// looks like we have a problem.
			$ct = sizeof($result) ;
			for ($i = 0; $i < $ct; $i++)
			{
				$error = str_replace($file, $this->file_name, $result[$i]);
				$this->push_error(mpv::ERROR_WARNING, 'PHP_ERROR', htmlspecialchars($error));
			}
		}

		return true;
	}
}
