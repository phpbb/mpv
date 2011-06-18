<?php
/**
* Tests for testing/auditing the code of the MOD
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
class mpv_tests_code
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
		$test_methods = get_class_methods($this);

		if (!is_array($this->validator->package_files) || !sizeof($this->validator->package_files))
		{
			return;
		}

		foreach ($this->validator->package_files as $package_file)
		{
			$this->file_name = $package_file;
			
			if (mpv::check_unwanted($package_file))
			{
				continue;
			}			

			// Only test PHP files
			// We also check files that should be binary, but arent.
			// Maybe there is php in that?
			if (in_array(substr($package_file, -3), array('txt', 'tml', 'htm', 'tpl', 'xsl', 'xml', 'css', '.js', 'sql', 'ess')) || $this->check_binary($this->validator->temp_dir . $package_file))

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
	 * Add a note if a file isnt a binary, but extension is binary.
	 * Allowed non binary extension:
	 * txt, php, html, htm, tpl, xml, xsl, css
	 */
	private function check_binary($filename)
	{
		$base = basename($filename);
		$ext = substr($base, -3);

		if (in_array($ext, array('txt', 'php', 'tml', 'htm', 'tpl', 'xsl', 'xml', 'css', '.js', 'sql', 'ess')))
		{
			return false;
		}
/**
 * Perl version:
 *
            # Is this a binary file?  Let's look at the first few
            # lines to figure it out:
            for line in lines[:5]:
                for c in line.rstrip():
                    if c.isspace():
                        continue
                    if c < ' ' or c > chr(127):
                        lines = BINARY_EXPLANATION_LINES[:]
                        break
 *
 */
		$file = file($filename);

		for ($i = 0, $count = sizeof($file); $i < $count; $i++)
		{
			if ($i > 5)
			{
				break;
			}
			for ($j = 0, $count2 = strlen($file[$i]); $j < $count2; $j++)
			{
				if ($file[$i][$j] > chr(127))
				{
					unset($file);
					return true;
				}
			}
		}
		unset($file);

		$this->push_error(MPV::ERROR_FAIL, 'FILE_NON_BINARY');

		return false;

	}

	/**
	 * Enter description here...
	 *
	 * @access private
	 * @return bool
	 */
	function test_empty()
	{
		if (strlen(trim($this->file_contents)) == 0)
		{
			$this->push_error(MPV::ERROR_WARNING, 'FILE_EMPTY');

			$this->terminate = true;
			return false;
		}
		return true;
	}

	/**
	 * Test for the use of improper line endings
	 *
	 * @access	private
	 * @return	bool
	 */
	private function test_unix_endings()
	{
		if (strpos($this->file_contents, "\r") !== false)
		{
			$this->push_error(mpv::ERROR_WARNING, 'NO_UNIX_ENDINGS');
			return false;
		}

		return true;
	}

	/**
	 * Test for the use of short tags (<?)
	 *
	 * @access	private
	 * @return	bool
	 */
	private function test_short_tags()
	{
		$strpos = '';
		if (strpos($this->file_contents, "<?=") !== false)
		{
			$strpos = '<?=';
		}
		else if (strpos($this->file_contents, '<? ') !== false)
		{
			$strpos = '<? ';
		}
		else if (strpos($this->file_contents, "<?\r") !== false)
		{
			$strpos = "<?\r";
		}
		else if (strpos($this->file_contents, "<?\n") !== false)
		{
			$strpos = "<?\n";
		}

		if ($strpos)
		{
			return $this->display_line_code(mpv::ERROR_FAIL, 'SHORT_TAGS', $strpos);
		}

		return true;
	}
	/**
	 * Tests to see if IN_PHPBB is defined.
	 *
	 * @access private
	 * @return bool
	 */
	private function test_in_phpbb()
	{
		if (preg_match("#(a|u|m)cp/info/(a|u|m)cp_(.?)#i", $this->file_name))
		{
			// Ignore info files
			return true;
		}
		if (preg_match("#define([ ]+){0,1}\(([ ]+){0,1}'IN_PHPBB'#", $this->file_contents))
		{
			return true;
		}
		else if (preg_match("#defined([ ]+){0,1}\(([ ]+){0,1}'IN_PHPBB'#", $this->file_contents))
		{
			return true;
		}
		$this->push_error(MPV::ERROR_FAIL, 'NO_IN_PHPBB');
		return false;
	}

	/**
	 * Test for mysql_* (DBAL) functions
	 *
	 * @access	private
	 * @return	bool
	 */
	private function test_dbal()
	{
		$return = true;

		$functions = array(
			'mysql_',
			'mysqli_',
			'oci_',
			'sqlite_',
			'pg_',
			'mssql_',
			'odbc_',
			'sqlsrv_',
			'ibase_',
			'db2_',
		);

		foreach ($functions as $function)
		{
			if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}([a-zA-Z0-9_]+){1,}\s*\({1}#si", $this->file_contents))
			{
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}([a-zA-Z0-9_]+){1,}\s*\({1}#si", array('new', 'function'));
			}
		}
		return $return;
	}

	/**
	 * Test for some basic disallowed functions
	 *
	 * @access	private
	 * @return	bool
	 */
	private function test_code()
	{
		$return = true;

		$functions = array(
			'eval',
			'exec',
			'sytem',
			'passthru',
			'getenv',
			'die',
			'sha1',
			'addslashes',
			'stripslashes',
		);

		$functions_none = array(
			'`'
		);

		$functions_notice = array(
			'include_once',
			'require_once',
		);

		$functions_warning = array(
			'md5',
		);

		foreach ($functions as $function)
		{
			if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({1}#si", $this->file_contents))
			{
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si");
			}
		}

		foreach ($functions_none as $function)
		{
			if (preg_match("#" . preg_quote($function, '#') . "#si", $this->file_contents) && strpos($this->file_name, '/language/') == 0)
			{
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#" . preg_quote($function) . "#si");
			}
		}

		foreach ($functions_notice as $function)
		{
			if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({1}#si", $this->file_contents))
			{
				$return = $this->display_line_code(mpv::ERROR_NOTICE, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si");
			}
		}

		foreach ($functions_warning as $function)
		{
			if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({1}#si", $this->file_contents))
			{
				$return = $this->display_line_code(mpv::ERROR_WARNING, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si");
			}
		}

		return $return;
	}

	/**
	 * Test for print/echo functions
	 *
	 * @access 	private
	 * @return	bool
	 */
	private function test_echo()
	{
		$return = true;

		$functions = array(
			'print_r',
			'var_dump',
			'printf',
		);

		$functions_none = array(
			'print',
			'echo',
		);

		foreach ($functions as $function)
		{
		 	if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({1}#si", $this->file_contents))
			{
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si");
			}
		}

		foreach ($functions_none as $function)
		{
		 	if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({1}#si", $this->file_contents))
			{
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si");
			}
		}
		return $return;
	}

	/**
	 * Test for $_*[]
	 *
	 * @access	private
	 * @return	bool
	 */
	private function test_globals()
	{
		$return = true;

		$fail_functions = array(
			'$GLOBALS',
			'$HTTP_POST_VARS',
			'$HTTP_GET_VARS',
			'$HTTP_SERVER_VARS',
			'$HTTP_ENV_VARS',
			'$HTTP_COOKIE_VARS',
			'$HTTP_POST_FILES',
			'$HTTP_SESSION_VARS',
			'$_FILES',
			);

		$warning_functions = array(
			'$_SESSION',
			'$_SERVER',
			'$_ENV',
			'$_REQUEST',
		);

		$isset_functions = array(
			'$_POST',
			'$_GET',
			'$_COOKIE',
		);

		foreach ($warning_functions as $function)
		{
			if (strpos($this->file_contents, $function) !== false)
			{
				$return = $this->display_line_code(mpv::ERROR_WARNING, 'USAGE_' . strtoupper(str_replace(array('_' , '$'), '', $function)), $function);
			}
		}

		foreach ($fail_functions as $function)
		{
			if (strpos($this->file_contents, $function) !== false)
			{
				$lower = strtoupper(str_replace(array('_' , '$'), '', $function));
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . $lower, $function);
			}
		}

		foreach ($isset_functions as $function)
		{
			if (strpos($this->file_contents, $function) !== false)
			{
				$lower = strtoupper(str_replace(array('_' , '$'), '', $function));
				$return = $this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . $lower, $function, false, array('isset'));
			}
		}

		return $return;
	}

	/**
	 * Checks for the request_var usage with integers. Should match on "0" and '0'
	 *
	 * @access	private
	 * @return	bool
	 */
	private function test_request_var()
	{
		//
		if (preg_match("#request_var\((['|\"]+)(.*)(['|\"]+), (['|\"]+)([0-9]+)(['|\"]+)#si", $this->file_contents))
		{
			$this->display_line_code(mpv::ERROR_FAIL, 'USAGE_REQUEST_VAR_INT', false, "#request_var\((['|\"]+)(.*)(['|\"]+), (['|\"]+)([0-9]+)(['|\"]+)#si");
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	private function test_include()
	{
		$return = true;

		$in_comment = false;
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

			if (preg_match("#^(include_once|require_once|include|require)(\s'|\s\"|\s\$|\s\(|\()#", $content_new))
			{
				if (strpos($content_new, '$phpbb_root_path') === false && strpos($content_new, '$phpbb_admin_path') === false)
				{
					$return = false;

					$this->push_error(mpv::ERROR_WARNING, 'INCLUDE_NO_ROOT', array((string)($line + 1), '[code]' . trim($content) . '[/code]'));
				}
				if (strpos($content_new, '.php') !== false && strpos($content_new, '$phpEx') === false)
				{
					$return = false;

					$this->push_error(mpv::ERROR_WARNING, 'INCLUDE_NO_PHP', array((string)($line + 1), '[code]' . trim($content) . '[/code]'));
				}
			}
		}
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
