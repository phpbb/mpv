<?php
/**
* Main file
*
* @package mpv
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

require($root_path . 'includes/lib/cortex_base.' . $phpEx);
require($root_path . 'includes/lib/cortex_xml.' . $phpEx);

/**
 * Main MPV validation class
 *
 * @package		mpv
 */
class mpv
{
	/**
	 * Constant for "fail" error
	 */
	const ERROR_FAIL = 1;

	/**
	 * Constant for notices
	 */
	const ERROR_NOTICE = 2;

	/**
	 * Constant for warnings
	 */
	const ERROR_WARNING = 3;

	/**
	 * Constant for information notices
	 */
	const ERROR_INFO = 4;

	/**
	* Output validation report as plain text
	*/
	const OUTPUT_TEXT = 0;

	/*
	 * Output validation report as BBcode
	 */
	const OUTPUT_BBCODE = 1;

	/**
	 * Output validation report as HTML
	 */
	const OUTPUT_HTML = 2;

	/**
	 * use "unzip" unix command for unzipping
	 *
	 */
	const UNZIP_EXEC = 1;

	/**
	 * use phpbb's zip compression class
	 *
	 */
	const UNZIP_PHPBB = 2;

	/**
	 * use the Zip php extension
	 */
	const UNZIP_PHP = 3;

	/**
	 * Decide on if exec is enabled to use exec (And Zip exists) or phpBB's zip handler.
	 *
	 */
	const UNZIP_PREFERENCE = 4;

	/**
	 * Enable the execution of PHP for included files.
	 * Before you enable this you should read tests/test_execution.php and understand the
	 * written text.
	 */
	const EXEC_PHP = 1;

	/**
	 * Disable the execution of php.
	 * this is the default setting for security reasons.
	 */
	const DONT_EXEC_PHP = 2;

	/**
	 * MPV directory
	 *
	 * @access 	public
	 * @var		string
	 */
	public static $dir = './';

	/**
	 * Our collection of tests class objects
	 *
	 * @access	private
	 * @var		array
	 */
	private $test_collections;

	/**
	 * Errors which were encountered during testing
	 *
	 * @access	private
	 * @var		array
	 */
	private $errors;

	/**
	 * Our pre-formatted PM content
	 *
	 * @access	private
	 * @var		string
	 */
	private $message;

	/**
	 * Array of "unwanted files/directories", all strtolowered
	 *
	 * @access	private
	 * @var		array
	 */
	public static $unwanted_files;

	/**
	 * Output type; either OUTPUT_BBCODE or OUTPUT_HTML
	 *
	 * @access	public
	 * @var		int
	 */
	public $output_type;

	/**
	 * Unzip type.
	 *
	 * @var		int
	 */
	public $unzip_type;

	/**
	 * Path to our local, decompressed version of the MOD package we are validating
	 *
	 * @access	public
	 * @var		string
	 */
	public $temp_dir;

	/**
	 * Associative array containing the cortex_xml objects of each MODX file
	 * found in the MOD package, structured as array(relative_path => $object);
	 *
	 * @access	public
	 * @var		array
	 */
	public $modx_files;

	/**
	 * Array containing the (relative) path to each file found in the MOD package
	 *
	 * @access	public
	 * @var		array
	 */
	public $package_files;
	
	
	/**
	 * Array containing all directories in a MOD package
	 * 
	 * @access	public
	 * @var		array
	 */
	public static $package_directories = array();

	/**
	 * Array containing all XSL files
	 * @access	public
	 * @var 	array
	 */
	public $xsl_files;

	/**
	 * Directory name that is in the zip for this MOD
	 * @access	public
	 * @var		string
	 **/
	static public $mod_dir;

	/**
	 * Array with errors
	 *
	 * @access	public
	 * @var		array
	 */
	public $error = array();

	/**
	 * Server signature, printed at report (mpvXXX.mpvserver etc).
	 *
	 * @var		string
	 * @access 	public
	 */
	public $server_signature = false;

	/**
	 * Set if php is executed or not.
	 * Value can be mpv::EXEC_PHP or mpv::DONT_EXEC_PHP.
	 * See above comments by the definitions,
	 * and in tests/tests_execution.php
	 * before enabling!
	 */
	public static $exec_php = false;

	/**
	 * Tests are failed or not.
	 *
	 * @access	private
	 * @var		bool
	 */
	private $tests_failed = false;

	/**
	 * Name of zip file
	 *
	 * @var 	string
	 * @access	public
	 */
	public $zip_file;

	/**
	* Name of the original zip file
	*
	* @var		string
	* @access	public
	*/
	public $orig_package_name = 'zip';

	/**
	 * Constructor
	 *
	 * @param string	$dir		Directory with data ($root_path)
	 * @param int		$unzip_type	Unzip type, see constants
	 * @param bool		$remove_zip Remove zip after using it.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct($dir = null, $unzip_type = self::UNZIP_PREFERENCE, $remove_zip = true, $mpv_lang = false)
	{
		global $phpEx, $lang;

		if (is_null($dir))
		{
			global $root_path;
			$dir = $root_path;
		}
		
		/**
		 * Language is already included, iam not going to overwrite it
		 * If a user wants to include a non english language, starting from
		 * September 4, 2010 he should not include his own language file.
		 * MPV will include the requested language, to set the language,
		 * you need to set it as default parameter for the contructor,
		 * or define it as MPV_LANG.
		 * Note that the parameter given to this function is used
		 * if favour of MPV_LANG.
		 */
		if (!is_array($lang))
		{
			$lang = '';
			if (!file_exists($dir . '/includes/languages/'))
			{
				// Language dir does not exists.
				if (file_exists($dir . 'lang.' . $phpEx))
				{
					$lang = 'lang.' . $phpEx;
				}
				else
				{
					die('Language directory not found');
				}
			}
			else if($mpv_lang && file_exists($dir . '/includes/languages/' . $mpv_lang . '/lang.' . $phpEx))
			{
				$lang = $dir . '/includes/languages/' . $mpv_lang . '/lang.' . $phpEx;
			}
			else if(MPV_LANG && file_exists($dir . '/includes/languages/' . MPV_LANG . '/lang.' . $phpEx))
			{
				$lang = $dir . '/includes/languages/' . MPV_LANG . '/lang.' . $phpEx;
			}
			else
			{
				// no language defined, use en
				$lang = $dir . '/includes/languages/en/lang.' . $phpEx;
			}
			
			include($lang);
		}
		

		set_error_handler(array($this, 'error_handler'));

		if ($unzip_type == self::UNZIP_PREFERENCE)
		{
			$unzip_type = self::UNZIP_PHPBB;
			$exec_check = false;
			if (function_exists('exec') && stristr(php_uname(), 'Windows') === false)
			{
				$check = false;
				@exec("whereis unzip", $check);

				if ($check[0] != "unzip:") // If unzip doesnt exists whereis returns under linux unzip: (6 chars).
				{
					$exec_check = true;
				}
			}

			if ($exec_check)
			{
				$unzip_type = self::UNZIP_EXEC;
			}
			else if (extension_loaded('zip'))
			{
				$unzip_type = self::UNZIP_PHP;
			}
			unset($exec_check);
		}

		if (defined('UNWANTED') && UNWANTED)
		{
			self::$unwanted_files = explode('|', UNWANTED);
		}

		$this->unzip_type = $unzip_type;
		$this->remove_zip = $remove_zip;
		$this->exec_php   = self::DONT_EXEC_PHP;

		$this->dir = $dir;

		$this->errors = array();
		$this->message = '';

		$this->xsl_files			= array();
		$this->modx_files			= array();
		$this->package_files		= array();
		$this->package_directories	= array();
		$this->test_collections		= array();

		$this->output_type = self::OUTPUT_BBCODE;
		
		require($this->dir . 'includes/tests/test_base.php');

		// Get the available test collections
		if ($opendir = opendir($this->dir . 'includes/tests/'))
		{
			while (false !== ($file = readdir($opendir)))
			{
				if (preg_match('#tests_([a-z]+)\.' . preg_quote($phpEx, '#') . '$#i', $file))
				{
					$php_file = $this->dir . 'includes/tests/' . $file;

					require($php_file);

					$class_name = 'mpv_tests_' . substr(basename($php_file), 6, -4);
					if (class_exists($class_name))
					{
						$this->test_collections[$class_name] = new $class_name($this);
					}
				}
			}
			closedir($opendir);
		}
	}

	/**
	 * Check if the file is unwanted or not.
	 * 
	 * @param string $directory
	 * @return boolean
	 */
	public static function check_unwanted($directory)
	{
		$regexp = '#(^|.*/)(' . implode('|', array_map('preg_quote', self::$unwanted_files)) . ')(?:/|$)#i';
		
		return preg_match($regexp, $directory) ? true : false;
	}


	/**
	 * error handler
	 *
	 * @param int $error_no
	 * @param string $msg_text
	 * @param string $error_file
	 * @param string $error_line
	 */
	public function error_handler($error_no, $msg_text, $error_file, $error_line)
	{
		global $lang;

		$error_file = basename($error_file);

		// Do not display notices if we suppress them via @
		if (error_reporting() == 0)
		{
			return;
		}

		if ($error_no == E_STRICT)
		{
			return;
		}
		else if ($error_no == E_NOTICE)
		{
			print sprintf($lang['MPV_NOTICE'], $error_file, $error_line, $msg_text);
			return;
		}
		else if ($error_no == E_WARNING)
		{
			print sprintf($lang['MPV_WARNING'], $error_file, $error_line, $msg_text);
			return;
		}
		else if ($error_no == E_USER_NOTICE)
		{
			print sprintf($lang['MPV_USER_NOTICE'], $error_file, $error_line, $msg_text);
			return;
		}

		die(sprintf($lang['MPV_GENERAL_ERROR'], $error_file, $error_line, $msg_text));
	}

	/**
	 * Send an error to our list of errors
	 *
	 * @access	public
	 * @param	int			Error type
	 * @param	string		Message
	 * @param	string		Filename of the file causing the error
	 * @param	mixed		Optional array of sprintf() values, or a non-array for passing one single value
	 * @return	void
	 */
	public function push_error($type, $message, $filename = null, $sprintf_args = null)
	{
		global $lang;

		// Mold $sprintf_args into something usable
		if (is_null($sprintf_args))
		{
			$sprintf_args = array();
		}
		else if (!is_array($sprintf_args))
		{
			$sprintf_args = array($sprintf_args);
		}

		// Quick and dirty, but it works well for development
		if (!isset($lang[$message]))
		{
			$lang[$message] = $message;

			if (sizeof($sprintf_args) > 0)
			{
				$lang[$message] .= str_repeat(' %s ', sizeof($sprintf_args));
			}
		}

		// Compose the message
		$message = @vsprintf($lang[$message], $sprintf_args);
		if (!is_null($filename))
		{
			global $root_path;
			$filename = str_replace(array(phpbb_realpath($root_path), '\\'), array('', '/'), $filename);
			$message = $filename . ': ' . $message;
		}

		// Update the validation message
		switch ($type)
		{
			case self::ERROR_FAIL:
				$this->message .= '[color=red][ [b]' . $lang['MPV_FAIL_RESULT'] . '[/b] ][/color] ' . $message . "\n";
			break;

			case self::ERROR_NOTICE:
				$this->message .= '[color=blue][ [b]' . $lang['MPV_NOTICE_RESULT'] . '[/b] ][/color] ' . $message . "\n";
			break;

			case self::ERROR_WARNING:
				$this->message .= '[color=orange][ [b]' . $lang['MPV_WARNING_RESULT'] . '[/b] ][/color] ' . $message . "\n";
			break;

			case self::ERROR_INFO:
				$this->message .= '[color=purple][ [b]' . $lang['MPV_INFO_RESULT'] . '[/b] ][/color] ' . $message . "\n";
			break;

			default:
				$this->message .= '[color=orange][ [b]' . $lang['MPV_WARNING_RESULT'] . '[/b] ][/color] [b]' . $lang['INVALID_TYPE'] . "\n";
				$this->message .= '[color=purple][ [b]' . $lang['MPV_INFO_RESULT'] . '[/b] ][/color] ' . $message . "\n";
		}

		// Store the raw log in $this->errors
		$this->errors[$type][] = array(
			'message'	=> $message,
			'filename'	=> $filename,
			'arguments'	=> $sprintf_args
		);
	}

	/**
	 * Validate a given package
	 *
	 * @access	public
	 * @param	string		Path to MOD package (.zip)
	 * @return	void
	 */
	public function validate($package)
	{
		global $root_path, $phpEx, $lang;

		// Clear some data
		$this->errors = array();
		$this->message = '';

		$this->zip_file = $package;

		$this->message .= sprintf($lang['VALIDATING_ZIP'], $this->orig_package_name) . "\n\n";

		$this->push_error(self::ERROR_NOTICE, 'GENERAL_NOTICE'); // Add a general notice about possible wrong fails.

		// First see if the package actually exists
		if (!file_exists($package))
		{
			$this->push_error(self::ERROR_FAIL, 'PACKAGE_NOT_EXISTS', __FILE__, $package);
			$this->cleanup();

			return;
		}

		$this->temp_dir = $root_path . 'store/temp/mpv_' . md5(uniqid(time())) . '/';
		mkdir($this->temp_dir, 0777, true);

		if (defined('MPV_DEBUG') && MPV_DEBUG)
		{
			$type = '';
			switch ($this->unzip_type)
			{
				case self::UNZIP_EXEC:
					$type = $lang['TYPE_EXEC'];
				break;
				case self::UNZIP_PHP:
					$type = $lang['TYPE_PHP'];
				break;
				case self::UNZIP_PHPBB:
					$type = $lang['TYPE_PHPBB'];
				break;
				default:
					$this->push_error(self::ERROR_FAIL, 'INVALID_ZIP_METHOD', __FILE__, $this->zip_type);
					$this->cleanup();
					return;

			}
			$this->push_error(self::ERROR_INFO, 'ZIP_METHOD', __FILE__, $this->unzip_type, $type);
		}

		if ($this->unzip_type == self::UNZIP_EXEC)
		{
			$basename = basename($package);

			copy($package, $this->temp_dir . $basename);

			// Unzip it.
			exec('cd ' . escapeshellarg($this->temp_dir) . ' && unzip ' . escapeshellarg($basename));
		}
		else if ($this->unzip_type == self::UNZIP_PHP)
		{
			$zip = new ZipArchive();
			if ($zip->open($package) === true)
			{
				if (!$zip->extractTo($this->temp_dir))
				{
					$zip->close();
					$this->push_error(self::ERROR_FAIL, 'UNABLE_EXTRACT_PHP', __FILE__, $package);
					$this->cleanup();

					return;
				}

				$zip->close();
			}
			else
			{
				$this->push_error(self::ERROR_FAIL, 'UNABLE_OPEN_PHP', __FILE__, $package);
				$this->cleanup();
				return;
			}
		}
		else if ($this->unzip_type == self::UNZIP_PHPBB)
		{
			if (!class_exists('compress_zip'))
			{
				include($root_path . 'includes/functions_compress.' . $phpEx);
			}

			// Next, try to unzip it
			$compress = new compress_zip('r', $package);
			$compress->extract($this->temp_dir);
		}
		else
		{
			$this->push_error(self::ERROR_FAIL, 'INVALID_ZIP_METHOD', __FILE__, $this->unzip_type);
			$this->cleanup();
			return;
		}

		$file_exists = 0;
		$dir_exists = 0;
		if ($dh = opendir($this->temp_dir))
		{
			while (false !== ($file = readdir($dh)))
			{
				if ($file == '.' || $file == '..')
				{
					continue;
				}

				if (is_dir($this->temp_dir . $file))
				{
					$dir_exists++;
					$dir = $file;
				}
				if (is_file($this->temp_dir . $file) && $file != $package)
				{
					$file_exists++;
				}
			}
		}

		if (!$file_exists && $dir_exists)
		{
			self::$mod_dir = $dir;
		}

		foreach (self::dir_files($this->temp_dir, '', true) as $file)
		{
			$this->package_files[] = $file;

			$ext = substr(strrchr($file, '.'), 1);

			if ($ext == 'xsl')
			{
				$this->xsl_files[] = $file;
			}
			else if ($ext == 'xml')
			{
				$raw_xml = file_get_contents($this->temp_dir . $file);

				if (strpos($raw_xml, '<mod xmlns:xsi=') !== false || strpos($raw_xml, '<mod xmlns=') !== false)
				{
					$modx_file = $file;

					$this->error = array();

					set_error_handler(array($this, 'internal_error'));

					$tmp = cortex_xml::load_file($this->temp_dir . $file);

					restore_error_handler();

					if ($this->error || !$tmp)
					{
						foreach ($this->error as $error)
						{
							$this->push_error(self::ERROR_FAIL, 'MPV_XML_ERROR', $file, $error);
						}
					}
					else
					{
						$this->modx_files[$modx_file] = $tmp;
					}
				}

				unset($raw_xml);
			}
		}

		// Run the tests
		foreach ($this->test_collections as $collection)
		{
			$collection->run();
		}

		// Now get rid of $this->temp_dir
		$this->cleanup();
	}

	/**
	 * Error handler, used in some cases when php throws stuff around.
	 *
	 * @param int	 $error_no Error type/number
	 * @param string $msg_text Error text
	 * @param string $error_file Filename
	 * @param int	 $error_line linenumber
	 * @return void
	 */
	function internal_error($error_no, $msg_text, $error_file, $error_line)
	{
		$this->error[] = $msg_text;
	}

	/**
	 * Clean up any leftovers from our validation
	 *
	 * @access	private
	 * @return	void
	 */
	private function cleanup()
	{
		self::rmdir($this->temp_dir);

		if ($this->remove_zip)
		{
			@unlink($this->zip_file);
		}
	}

	/**
	 * Recursive rmdir
	 *
	 * @access	private
	 * @param	string		Path to directory (with appended slash)
	 * @return	void
	 */
	public static function rmdir($directory)
	{
		if ($dh = opendir($directory))
		{
			while (false !== ($file = readdir($dh)))
			{
				if ($file == '.' || $file == '..')
				{
					continue;
				}

				if (is_dir($directory . $file))
				{
					self::rmdir($directory . $file . '/');
				}
				else if (is_file($directory . $file))
				{
					unlink($directory . $file);
				}
			}

			closedir($dh);

			rmdir($directory);
		}
	}

	/**
	 * Get a list of all files in a directory and its subdirectories
	 *
	 * @access	private
	 * @param	string		Root, not included in result
	 * @param	string		Path to directory
	 * @return	array
	 */
	public static function dir_files($root, $dir = '')
	{
		$filelist = array();

		if ($dir && substr($dir, -1) != '/')
		{
			$dir .= '/';
		}
		
		self::$package_directories[] = $dir;

		if ($dh = opendir($root . $dir))
		{
			while ($file = @readdir($dh))
			{
				if ($file == '.' || $file == '..')
				{
					continue;
				}

				if (is_dir($root . $dir . $file))
				{
					$filelist = array_merge($filelist, self::dir_files($root, $dir . $file . '/'));
				}
				else
				{
					$filelist[] = $dir . $file;
				}
			}
			closedir($dh);
		}


		return $filelist;
	}

	/**
	 * Return the results of the test as a pre-formatted PM
	 *
	 * @access	public
	 * @return	string
	 */
	public function __toString()
	{
		global $lang;

		$fail		= (isset($this->errors[mpv::ERROR_FAIL])) ? sizeof($this->errors[mpv::ERROR_FAIL]) : 0;
		$warning 	= (isset($this->errors[mpv::ERROR_WARNING])) ? sizeof($this->errors[mpv::ERROR_WARNING]) : 0;

		if ($fail == 0 && $warning == 0)
		{
			$this->message .= $lang['NO_PRE_VAL_ERRORS'];
		}

		$this->message .= "\n" . $lang['REPORT_BY'] . " " . $this->mpv_version();

		if ($this->server_signature !== false)
		{
			$this->message .= " || " . $lang['MPV_SERVER'] . ": " . $this->server_signature ;
		}

		$this->message .= "\n";

		switch ($this->output_type)
		{
			case self::OUTPUT_BBCODE:
				return $this->message;

			case self::OUTPUT_HTML:
				$text = htmlspecialchars($this->message);
				return generate_text_for_html_display($text);

			case self::OUTPUT_TEXT:
				$text = htmlspecialchars($this->message);
				$text = generate_text_for_html_display($text);
				$text = htmlspecialchars_decode(strip_tags(str_replace('<br />', "\n", $text)));
				$text = str_replace("\n\n", "\n", $text);
				$text = str_replace("\n", PHP_EOL, $text);
				return $text;

			default:
				throw new Exception($lang['UNKNOWN_OUTPUT'] . ' "' . (int)$this->output_type . '"');
		}
	}
	
	/**
	* get current version of MPV
	* returns current git revision
	*/
	public function mpv_version()
	{
		global $root_path;
		$version_file = $root_path . '.git/refs/heads/master';
		return file_exists($version_file) ? trim(file_get_contents($version_file)) : '';
	}
	
	/**
	* Return the current phpBB3 version from phpBB.com's updatecheck directory
	*/
	public static function get_current_version($type)
	{
		global $lang;

		//If we don't want to go out to the Internet we set these
		if (LOCAL_ONLY)
		{
			switch ($type)
			{
				case 'phpbb':
					return PHPBB_VERSION;
					break;

				case 'modx':
					return LATEST_MODX;
					break;
				case 'umil':
					return LATEST_UMIL;
					break;
				default:
					return false;
					break;
			}
		}

		$errstr = '';
		$errno = 0;
		$host = 'version.phpbb.com';
		$port = 80;
		$timeout = 10;
		$directory = '';
		switch ($type)
		{
			case 'phpbb':
				$filename = 'phpbb/30x.txt';
			break;
			case 'modx':
				$filename = 'modx/modx_1x.txt';
			break;
			case 'umil':
				$filename = 'umil/umil.txt';
			break;
			default:
				return false;
		}

		$file_info = '';
		$get_info = false;

		if (file_exists(self::$dir . 'store/data/' . $filename))
		{
			//Get from cache if it's been less than a day since the last update
			if ((time() - filemtime(self::$dir . 'store/data/' . $filename)) <= 86400)
			{
				$file_info = file_get_contents(self::$dir . 'store/data/' . $filename);
			}
		}

		//Only do this if we couldn't get the cache data
		if (empty($file_info))
		{
			if ($fsock = @fsockopen($host, $port, $errno, $errstr, $timeout))
			{
				@fputs($fsock, "GET $directory/$filename HTTP/1.1\r\n");
				@fputs($fsock, "HOST: $host\r\n");
				@fputs($fsock, "Connection: close\r\n\r\n");

				while (!@feof($fsock))
				{
					if ($get_info)
					{
						$file_info .= @fread($fsock, 1024);
					}
					else
					{
						$line = @fgets($fsock, 1024);
						if ($line == "\r\n")
						{
							$get_info = true;
						}
						else if (stripos($line, '404 not found') !== false)
						{
							$errstr = $lang['FILE_NOT_FOUND'] . ': ' . $filename;
							return false;
						}
					}
				}
				//Cache the update file
				$cache = @fopen($root_dir . 'store/data/' . $filename, 'wb');
				@fwrite($cache, $file_info);
				@fclose($cache);

				@fclose($fsock);
			}
			else
			{
				if ($errstr)
				{
					$errstr = utf8_convert_message($errstr);
					return false;
				}
				else
				{
					$errstr = $lang['FSOCK_DISABLED'];
					return false;
				}
			}
		}

		$info = explode("\n", $file_info);

		return $info[0];
	}	
}
