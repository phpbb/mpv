<?php
/**
* Tests for verifying the package's structure in accordance with the packaging
* rules
*
* @package mpv
* @version $Id$
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

/**
 * Collection of tests which are used to check the MOD's file structure
 *
 * @package		mpv
 * @subpackage	tests
 */
class mpv_tests_packaging extends test_base
{
	/**
	 * String with valid MD5 for license.txt
	 * @access	private
	 * @var 	string	 
	 */
	private $license_md5 = array('eb723b61539feef013de476e68b5c50a', '0cce1e42ef3fb133940946534fcf8896', 'e060338598cd2cd6b8503733fdd40a11'); // Both seems to be good, one with unix lineends, one with windows?
	
	/**	 
	 * Array with MD5's for the MODX xsl files.
	 * IMPORTANT: The first MD5 is _always_ the newest and only valid MD5!
	 * @access	private
	 * @var		array	
	 */
	private $valid_md5_xsl = array(
		'515b908b69d5a926fefa9d4176565575', // md5 for http://www.phpbb.com/mods/modx/1.2.5/modx.prosilver.en.xsl
		'515b908b69d5a926fefa9d4176565575',
		'cbb5a076d38102ed083b1a0538ee4980', // md5 for http://www.phpbb.com/mods/modx/1.2.4/modx.prosilver.en.xsl
		'732e30fb150234112cf46516551f28fb', // md5 for http://www.phpbb.com/mods/modx/1.2.3/modx.prosilver.en.xsl
		'ad685f8a0b1bef7651222531824e0d5b', // md5 for http://www.phpbb.com/mods/modx/1.2.2/modx.prosilver.en.xsl
		'b96fbe26f60eea25ca8632c670ae7421', // md5 for http://www.phpbb.com/mods/modx/1.2.1/modx.prosilver.en.xsl
		'95e0c31a6a6d31922eb6e92b2747dd2b', // md5 for http://www.phpbb.com/mods/modx/1.2.0/modx.prosilver.en.xsl
		'6497dcb866bf9d0d882368cc9d4c8fae', // md5 for http://www.phpbb.com/mods/utilities/creator/file.php?file=modx.prosilver.en.xsl
	);
	
	private $valid_md5_umil = array(
		'1.0.2'		=>	'db40dbe549fcbbb5a790f7e81ce8e4db',
		'1.0.3'		=>	'e0999771662ee6eada0425ff076c3062',
		'1.0.4'		=>	'ada48057d56ebd22298648a8130fe573',
		'1.0.5'		=>	'6fc2ecd42400e93fea6468212fef73ab',
	);

	/**
	 * Run the tests in this collection
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function run()
	{
		$test_methods = get_class_methods($this);
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
					return;
				}
			}
		}
	}

	/**
	 * Test if the there is more as 1 xsl file
	 *
	 * @access private
	 * @return bool
	 */
	protected function test_xsl()
	{
		if (sizeof($this->validator->xsl_files) == 0)
		{
			$this->push_error(mpv::ERROR_FAIL, 'NO_XSL_FILE');
			return false;
		}

		return true;
	}
	
	
	/**
	 * Test the MD5 for certian files like UMIL and xsl
	 *
	 * @access private
	 * @return bool	 
	 */
	protected function test_files()
	{
		$error = false;
		$found_umil = false;
		if (sizeof($this->validator->xsl_files) > 0)
		{
			foreach ($this->validator->xsl_files as $file)
			{
				$md5 = md5_file($this->validator->temp_dir . $file);
				
				if (!in_array($md5, $this->valid_md5_xsl))
				{
					$error = true;
					$this->push_error(mpv::ERROR_WARNING, 'MODIFIED_XSL', $file, array($md5, $this->valid_md5_xsl[0]));
				}
				else if ($md5 != $this->valid_md5_xsl[0]) // Note, newest MD5 for xsl should be the first!
				{
					$error = true;
					$this->push_error(mpv::ERROR_FAIL, 'OLD_XSL', $file, array($md5));
				}
			}
			
			foreach ($this->validator->package_files as $file)
			{
				if (strpos($file, 'license.txt') !== false)
				{
					$md5 = md5_file($this->validator->temp_dir . $file);
					
					if (!in_array($md5, $this->license_md5))
					{
						$this->push_error(mpv::ERROR_FAIL, 'LICENSE_MD5', $file, array($md5, implode(', ',$this->license_md5)));
					}
				}
				
				$tmp = explode('/', $file);

				if (isset($tmp[sizeof($tmp) - 2]) && isset($tmp[sizeof($tmp) - 1]) && $tmp[sizeof($tmp) - 2] == 'umil' && $tmp[sizeof($tmp) - 1] == 'umil.php')
				{
					$md5 = md5_file($this->validator->temp_dir . $file);
					
					if ($found_umil)
					{
						$this->push_error(mpv::ERROR_WARNING, 'POSSIBLE_TWO_UMIL', null, array($file, $found_umil));
						continue;
					}
					
					if (!defined('IN_PHPBB'))
					{
						define('IN_PHPBB', true);
					}
					include ($this->validator->temp_dir . $file);
					
					if (!defined('UMIL_VERSION'))
					{
						$this->push_error(mpv::ERROR_FAIL, 'NO_UMIL_VERSION', $file);
						
						continue;
					}
					else if (version_compare(UMIL_VERSION, mpv::get_current_version('umil'), '<'))
					{
						$this->push_error(mpv::ERROR_FAIL, 'UMIL_OUTDATED', $file);
						
						// Check to see if the md5 still exists
						if (isset($this->valid_md5_umil[UMIL_VERSION]) && $this->valid_md5_umil[UMIL_VERSION] != $md5)
						{
							// Invalid MD5 for version as well :)
							$this->push_error(mpv::ERROR_WARNING, 'INCORRECT_UMIL_MD5', $file);							
						}
					}
					else if (!isset($this->valid_md5_umil[UMIL_VERSION]))
					{
						$this->push_error(mpv::ERROR_FAIL, 'UNKNOWN_VERSION_UMIL', $file);
					}
					else if ($this->valid_md5_umil[UMIL_VERSION] != $md5)
					{
						$this->push_error(mpv::ERROR_WARNING, 'INCORRECT_UMIL_MD5', $file);
					}
					$found_umil = $file;
				}
			}
		}
	}

	/**
	 * Checks to see if the required license.txt exists
	 *
	 * @access private
	 * @return bool
	 */
	protected function test_license()
	{
		foreach ($this->validator->package_files as $filename)
		{
			if (mpv::check_unwanted($filename))
			{
				continue;
			}
			
			if (strtolower(basename($filename)) == 'license.txt')
			{
				return true;
			}
		}
		$this->push_error(mpv::ERROR_FAIL, 'NO_LICENSE');
		return false;
	}

	/**
	 * Test to see if prosilver.xml or english.xml exisits.
	 *
	 * @return bool
	 * @access private
	 */
	protected function test_prosilver_english()
	{
		$return = true;
		foreach ($this->validator->package_files as $filename)
		{
			if (mpv::check_unwanted($filename))
			{
				continue;
			}		
		
			$file = strtolower(basename($filename));

			if ($file == 'prosilver.xml' || (strpos($file, 'prosilver') !== false && strpos($file, '.xml') !== false))
			{
				$this->push_error(mpv::ERROR_FAIL, 'PROSILVER_NO_MAIN_MODX', null, array($filename));
				$return = false;
			}

			if ($file == 'en.xml' || (strpos($file, 'english') !== false && strpos($file, '.xml') !== false))
			{
				$this->push_error(mpv::ERROR_FAIL, 'ENGLISH_NO_MAIN_MODX', null, array($filename));
				$return = false;
			}
		}

		return $return;
	}

	/**
	 * Checks to see if there are bad files from svn or the OS
	 *
	 * @access private
	 * @return bool
	 */
	protected function test_unwanted()
	{
		// precache regexp for efficiency
		$regexp = '#(^|.*/)(' . implode('|', array_map('preg_quote', mpv::$unwanted_files)) . ')(?:/|$)#i';

		$unwanted_files = array();
		foreach (mpv::$package_directories as $filename)
		{
			if (preg_match($regexp, $filename, $matches))
			{
				// don't add files multiple times
				if (isset($unwanted_files[$matches[1] . $matches[2]]))
				{
					continue;
				}

				// add unwanted file, use array keys for efficiency
				$unwanted_files[$matches[1] . $matches[2]] = true;

				// if there is no dir, it's the root
				if ($matches[1] === '')
				{
					$matches[1] = './';
				}

				// push notice
				$this->push_error(mpv::ERROR_FAIL, 'UNWANTED_FILE', null, array($matches[1], $matches[2]));
			}
		}

		return sizeof($unwanted_files);
	}

	/**
	 * Wrapper around $this->validator->push_error
	 *
	 * @access	private
	 * @param	int			Error type
	 * @param	string		Message
	 * @param	string		Filename of the file causing the error
	 * @param	mixed		Optional array of sprintf() values, or a non-array for passing one single value
	 * @return	void
	 */
	protected function push_error($type, $message, $filename = null, $sprintf_args = null)
	{
		$this->validator->push_error($type, $message, $filename, $sprintf_args);
	}
}
