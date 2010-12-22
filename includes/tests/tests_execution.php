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
class mpv_tests_execution
{
	/**
	 * mpv (validator) object
	 *
	 * @access	private
	 * @var		object
	 */
	private $validator;

	/**
	 * Terminate the testing process
	 *
	 * @access	private
	 * @var		bool
	 */
	private $terminate;

	/**
	 * Array containing the names of the tests which failed
	 *
	 * @access	private
	 * @var		array
	 */
	private $failed_tests;

	/**
	 * The filename of the file we're currently testing
	 *
	 * @access	private
	 * @var		string
	 */
	private $file_name;

	/**
	 * The contents of the file we're currently testing
	 *
	 * @access	private
	 * @var		string
	 */
	private $file_contents;

	/**
	 * The content of the file we're testing, but as array
	 *
	 * @access private
	 * @var array
	 */
	private $file_contents_file;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	object		mpv object
	 * @return	void
	 */
	public function __construct(mpv $validator)
	{
		$this->validator = $validator;
		$this->terminate = false;
		$this->failed_tests = array();
	}

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
	
	/**
	 * Wrapper around $this->validator->push_error
	 *
	 * @access	private
	 * @param	int			Error type
	 * @param	string		Message
	 * @param	mixed		Optional array of sprintf() values, or a non-array for passing one single value
	 * @return	void
	 */
	private function push_error($type, $message, $sprintf_args = null)
	{
		$this->validator->push_error($type, $message, $this->file_name, $sprintf_args);
	}

	/**
	 * Displays a inline code block with the line of wrong code.
	 *
	 * @access	private
	 * @param	int		Error type
	 * @param	string	Message
	 * @param	string	String to use in strpos
	 * @param	string	String to use in preg_match
	 * @param array  array that contains words that should not be in the line
	 * @return	bool
	 */
	private function display_line_code($type, $message, $strpos, $preg_match = false, $ignore_in_line = array())
	{
		$found = false;

		$in_comment = false;

		if (!is_array($ignore_in_line))
		{
		  if (is_string($ignore_in_line))
		  {
		    $ignore_in_line = array(0 => $ignore_in_line);
			}
			else
			{
			  $ignore_in_line = array();
			}
		}

		foreach ($this->file_contents_file as $line => $content)
		{
			$content_new = $content;
			$loc = strpos($content, '*/');

			if ($in_comment && $loc === false)
			{
				$content_new = '';
			}
			else if ($in_comment && $loc !== false)
			{
				// Need to replace everything till */
				$total = strlen($content_new);
				$negative = $total - $loc;
				$total = $total - $negative;

				$content_new = substr($content_new, ($loc + 2));
				$in_comment = false;
			}
			else if(!$in_comment && strpos($content, '/*') !== false)
			{
				if ($loc !== false) // Used as inline
				{
					$content_new = preg_replace('#/\*(.*)\*/#si', '', $content_new);
				}
				else
				{
					$in_comment = true;

					$content_new = substr($content_new, 0, strpos($content, '/*'));
				}
			}
			$loc = strpos($content_new, '//');

			if ($loc !== false)
			{
				$content_new = substr($content_new, 0, $loc + 2);
			}

			foreach ($ignore_in_line as $value)
			{
			  if (strpos($content_new, $value) !== false)
			  {
			    $content_new = '';
				}
			}

			if (empty($content_new))
			{
			  continue;
			}

			// Yes, $content_new in if, and $content in code.
			// This is because we want comments still being displayed ;)
			if (!$preg_match && strpos($content_new, $strpos) !== false)
			{
				$this->push_error($type, $message, array((string) ($line + 1) , '[code]' . trim($content) . '[/code]'));
				$found = true;
			}
			else if ($preg_match && preg_match($preg_match, $content))
			{
				$this->push_error($type, $message, array((string) ($line + 1) , '[code]' . trim($content) . '[/code]'));
				$found = true;
			}
		}

		return $found;
	}

	/**
	 * Terminate all further testing; used if failing one test could result in other tests malfunctioning
	 *
	 * @access	private
	 * @return	bool
	 */
	private function terminate()
	{
		$this->terminate = true;
		$this->push_error(mpv::ERROR_WARNING, 'TESTING_TERMINATED');

		return false;
	}

	/**
	 * Check whether a given test failed
	 *
	 * @access	private
	 * @param	string		Test name
	 * @return	bool
	 */
	private function failed_test($test_name)
	{
		return in_array($test_name, $this->failed_tests);
	}

	/**
	 * Returns a array with failed tests
	 *
	 * @access	public
	 * @return	array
	 */
	 public function return_failed_tests()
	 {
		return $this->failed_tests;
	 }
}
