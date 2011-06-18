<?php
/**
* Base class for tests
* Contains functions that are used in all tests.
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
abstract class test_base
{
	/**
	 * mpv (validator) object
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $validator;

	/**
	 * Terminate the testing process
	 *
	 * @access	protected
	 * @var		bool
	 */
	protected $terminate;

	/**
	 * Array containing the names of the tests which failed
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $failed_tests;

	/**
	 * The filename of the file we're currently testing
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $file_name;

	/**
	 * The contents of the file we're currently testing
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $file_contents;

	/**
	 * The content of the file we're testing, but as array
	 *
	 * @access protected
	 * @var array
	 */
	protected $file_contents_file;
	
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
	public abstract function run();
	
	/**
	 * Wrapper around $this->validator->push_error
	 *
	 * @access	protected
	 * @param	int			Error type
	 * @param	string		Message
	 * @param	mixed		Optional array of sprintf() values, or a non-array for passing one single value
	 * @return	void
	 */
	protected function push_error($type, $message, $sprintf_args = null)
	{
		$this->validator->push_error($type, $message, $this->file_name, $sprintf_args);
	}

	/**
	 * Displays a inline code block with the line of wrong code.
	 *
	 * @access	protected
	 * @param	int		Error type
	 * @param	string	Message
	 * @param	string	String to use in strpos
	 * @param	string	String to use in preg_match
	 * @param array  array that contains words that should not be in the line
	 * @return	bool
	 */
	protected function display_line_code($type, $message, $strpos, $preg_match = false, $ignore_in_line = array())
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
	 * @access	protected
	 * @return	bool
	 */
	protected function terminate()
	{
		$this->terminate = true;
		$this->push_error(mpv::ERROR_WARNING, 'TESTING_TERMINATED');

		return false;
	}

	/**
	 * Check whether a given test failed
	 *
	 * @access	protected
	 * @param	string		Test name
	 * @return	bool
	 */
	protected function failed_test($test_name)
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
	 
	/**
	 * Extracs a dir from a filename
	 *
	 * @param	string $file Filename
	 * @return	string
	 */
	protected function extract_dir($file)
	{
		return substr($file, 0, -strlen(basename($file)));
	}	
	
	/**
	 * Test a function from phpunit that is private.
	 * @param string $function Functioname
	 * @param array $parameters Function parameters
	 **/
	public function unittest($function, $parameters)
	{
		return call_user_func_array(array($this, $function), $parameters);
	}

	public function setFilename($file)
	{
		$this->file_name = $file;
		$this->file_contents = @file_get_contents($file);
		$this->file_contents_file = @file($file);
	}	 
}
 ?>
