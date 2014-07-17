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
class mpv_tests_code extends test_base
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
			if (in_array(substr($package_file, -3), array('.md', 'txt', 'tml', 'htm', 'tpl', 'xsl', 'xml', 'css', '.js', 'sql', 'ess')) || $this->check_binary($this->validator->temp_dir . $package_file))

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
	protected function check_binary($filename)
	{
		$base = basename($filename);
		$ext = substr($base, -3);

		if (in_array($ext, array('.md', 'txt', 'php', 'tml', 'htm', 'tpl', 'xsl', 'xml', 'css', '.js', 'sql', 'ess')))
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
	protected function test_empty()
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
	protected function test_unix_endings()
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
	protected function test_short_tags()
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
	protected function test_in_phpbb()
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
	protected function test_dbal()
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
				if ($this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}([a-zA-Z0-9_]+){1,}\s*\({1}#si", array('new', 'function')))
				{
					$return = false;	
				}
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
	protected function test_code()
	{
		$return = true;

		$functions = array(
			'eval' 			=> mpv::ERROR_FAIL,
			'exec' 			=> mpv::ERROR_FAIL,
			'system' 		=> mpv::ERROR_FAIL,
			'passthru' 		=> mpv::ERROR_FAIL,
			'getenv'		=> mpv::ERROR_FAIL,
			'die'			=> mpv::ERROR_FAIL,
			'addslashes'	=> mpv::ERROR_FAIL,
			'stripslashes'	=> mpv::ERROR_FAIL,
			'htmlspecialchars'	=> mpv::ERROR_FAIL,
			'include_once'	=> mpv::ERROR_NOTICE,
			'require_once' 	=> mpv::ERROR_NOTICE,			
			'md5'			=> mpv::ERROR_WARNING,
			'sha1'			=> mpv::ERROR_WARNING,
		);

		$functions_none = array(
			'`'
		);
		
		$functions_without = array(
			'include_once'	=> mpv::ERROR_NOTICE,
			'require_once' 	=> mpv::ERROR_NOTICE,					
		);

		foreach ($functions as $function => $code)
		{
			if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({1}#si", $this->file_contents))
			{
				if ($this->display_line_code($code, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si"))
				{
					$return = false;	
				}
			}
		}
		
		foreach ($functions_without as $function => $code)
		{
			if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({0}#si", $this->file_contents))
			{
				if ($this->display_line_code($code, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si"))
				{
					$return = false;	
				}
			}
		}		

		foreach ($functions_none as $function)
		{
			if (preg_match("#" . preg_quote($function, '#') . "#si", $this->file_contents) && strpos($this->file_name, '/language/') == 0)
			{
				if ($this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#" . preg_quote($function) . "#si"))
				{
					$return = false;	
				}
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
	protected function test_echo()
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
				if ($this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si"))
				{
					$return = false;	
				}
			}
		}

		foreach ($functions_none as $function)
		{
		 	if (preg_match("#(^\s*|[^a-z0-9_])" . preg_quote($function, '#') . "{1}\s*\({0,1}#si", $this->file_contents))
			{
				if ($this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . strtoupper(str_replace(array('_', '$', '('), '', $function)), false, "#(^\s*|[^a-z0-9_])" . preg_quote($function) . "([ \(|\(| ]+)#si", array('fread')))
				{
					$return = false;	
				}
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
	protected function test_globals()
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
				if ($this->display_line_code(mpv::ERROR_WARNING, 'USAGE_' . strtoupper(str_replace(array('_' , '$'), '', $function)), $function, false, array('isset', 'empty')))
				{
					$return = false;	
				}
			}
		}

		foreach ($fail_functions as $function)
		{
			if (strpos($this->file_contents, $function) !== false)
			{
				$lower = strtoupper(str_replace(array('_' , '$'), '', $function));
				if ($this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . $lower, $function, false))
				{
					$return = false;	
				}
			}
		}

		foreach ($isset_functions as $function)
		{
			if (strpos($this->file_contents, $function) !== false)
			{
				$lower = strtoupper(str_replace(array('_' , '$'), '', $function));
				if ($this->display_line_code(mpv::ERROR_FAIL, 'USAGE_' . $lower, $function, false, array('isset', 'empty')))
				{
					$return = false;	
				}
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
	protected function test_request_var()
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
	protected function test_include()
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

			if (preg_match("#^(include_once|require_once|include|require)(\s'|\s\"|\s\$|\s\(|\()#", trim($content_new)))
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
		return $return;
	}
}
