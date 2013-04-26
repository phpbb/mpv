<?php
/**
* Tests for checking the MODX file(s)
*
* @package mpv
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

/**
 * Collection of tests which are run on the MOD's MODX files
 *
 * @package		mpv
 * @subpackage	tests
 */
class mpv_tests_modx extends test_base
{
	/**
	 * The filename of the MODX file we're currently working with
	 *
	 * @access	private
	 * @var		string
	 */
	private $modx_filename;

	/**
	 * The MODX cortex_xml object we're currently working with
	 *
	 * @access	public
	 * @var		object
	 */
	public $modx_object;

	/**
	 * The directory this MODX file is in.
	 *
	 * @access	private
	 * @var		string
	 */
	private $modx_dir;

	/**
	 * Run the tests in this collection
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function run()
	{
		global $statistics;

		$test_methods = get_class_methods($this);

		if (!is_array($this->validator->modx_files) || !sizeof($this->validator->modx_files))
		{
			$this->validator->push_error(mpv::ERROR_FAIL, 'NO_MODX_FILES');
			return;
		}

		foreach ($this->validator->modx_files as $modx_filename => $modx_object)
		{
			$this->modx_filename = $this->file_name = $modx_filename;
			
			if (mpv::check_unwanted($modx_filename))
			{
				continue;
			}			

			if (preg_match('#modx-(.*?)\.xsd#s', (string) $modx_object, $matches))
			{
				$current_modx_version = mpv::get_current_version('modx');
				if ($matches[1] != $current_modx_version)
				{
					$this->push_error(mpv::ERROR_FAIL, 'USING_MODX_OUTDATED', array($matches[1], $current_modx_version));
					//continue;
				}
				//Let's see if we can download the file from phpbb.compact
				if (!LOCAL_ONLY && !@file_exists($this->validator->dir . 'store/data/' . $matches[0]))
				{
					$data = @file_get_contents("http://www.phpbb.com/mods/xml/{$matches[0]}");

					//Now we write out the .xsd
					if (isset($data) && $data !== false)
					{
						$cache = @fopen($this->validator->dir . 'store/data/' . $matches[0], 'wb');
						@fwrite($cache, $data);
						@fclose($cache);
					}
					else
					{
						$this->push_error(mpv::ERROR_FAIL, sprintf('UNABLE_OPEN', "http://www.phpbb.com/mods/xml/{$matches[0]}"));
						continue;
					}
				}
				$errors = $modx_object->validate($this->validator->dir . 'store/data/' . $matches[0]);
			}
			else
			{
				$this->push_error(mpv::ERROR_FAIL, 'USING_MODX_UNKNOWN');
				continue;
			}

			if ($errors !== true)
			{
				foreach ($errors as $error)
				{
					$this->push_error(mpv::ERROR_FAIL, 'MODX_SCHEMA_INVALID', $error);
				}
				continue;
			}

			$this->failed_tests	= array();

			$this->modx_object 		= $modx_object;
			$this->modx_dir			= $this->extract_dir($modx_filename);
			$mod_title = $this->modx_object->get_xpath('//header/title', true);

			//Do we need to store some statistics?
			if (sizeof($statistics))
			{
				foreach ($statistics as $tag => $properties)
				{
					$line_to_write = '';

					//First we need to get the tag's existing nodes
					$tag_node = $this->modx_object->get_by_name($tag, false);
					//Now we go through each node to get the properties
					foreach ($tag_node as $node)
					{
						foreach ($properties as $property)
						{
							if (empty($line_to_write))
							{
								//Let's start with the MOD's title
								$line_to_write = $mod_title->value;
							}
							//Let's build the string we're going to save
							$line_to_write .= "||" . $property . ": " . $node->attributes[$property];
						}
					}
					if (!empty($line_to_write))
					{
						//Open text file and write to it
						$file_handle = @fopen($this->validator->dir . 'store/data/' . $tag . '_data.txt', 'a');
						if ($file_handle !== false)
						{
							@fwrite($file_handle, $line_to_write . "\r\n");
							@fclose($fh);
						}
					}
				}

			}

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
	}

	/**
	 * Test the MOD's version
	 *
	 * @access	public
	 * @return	bool
	 */
	protected function test_version()
	{
		$version = $this->modx_object->get_xpath('//header/mod-version', true);

		if (!is_object($version))
		{
			$this->push_error(mpv::ERROR_FAIL, 'VERSION_FIELD_MISSING');

			return false;
		}
		$version = strtolower($version->value);
		
		$unstable = array(
			'rc',
			'alpha',
			'beta',
			'dev',
			'a',
			'b',
		);
		
		$option = implode('|', $unstable);

		// Check the version format and numbering
		if (preg_match('#((\d+)\.)+(\d+)[a-z]?#', $version, $matches))
		{
			if (preg_match('#((\d+)\.)+(\d+)-(' . $option . '+)(\d+{0,)?#i', $version, $matches))
			{
				$this->push_error(mpv::ERROR_FAIL, 'MAJOR_VERSION_UNSTABLE', array($version));
				return false;
			}

			if (version_compare('1.0.0', $version) > 0)
			{
				$this->push_error(mpv::ERROR_FAIL, 'MAJOR_VERSION_UNSTABLE', array($version));
				return false;
			}
		}
		else
		{
			// Not a valid version
			$this->push_error(mpv::ERROR_FAIL, 'INVALID_VERSION_FORMAT', array($version));
			return false;
		}

		return true;
	}

	protected function test_xsl_exists()
	{
		$found = false;
		foreach ($this->validator->xsl_files as $xsl_file)
		{
			if ($this->modx_dir == $this->extract_dir($xsl_file))
			{
				$found = true;

				break;
			}
		}

		if (!$found)
		{
			$this->push_error(mpv::ERROR_NOTICE, 'NO_XSL_FOUND_IN_DIR', $this->modx_dir);
			$this->push_error(mpv::ERROR_NOTICE, 'NO_XSL_FOUND_IN_DIR2', $this->modx_dir);
		}
		return $found;
	}

	/**
	* Check whether files to open exist in the phpBB package
	* @return bool
	* @access private
	*/
	protected function test_open()
	{
		$return = true;
		$open_ary = $this->modx_object->get_by_name('open', false);

		foreach ($open_ary as $open_src)
		{
			$filename_ary[] = $open_src->attributes['src'];
		}
		unset($open_ary);

		$files_array = file($this->validator->dir . 'includes/tests/filelist.txt');

		// Simulation of in_array. Might be able to switch back to the actual
		// function.
		/**
		 * @TODO: Does this actually work?
		 */
		foreach ($files_array as $filename)
		{
			$match = false;
			foreach ($files_array as $test_filename)
			{
				if (trim($filename) == trim($test_filename))
				{
					$match = true;
					break;
				}
			}

			if (!$match)
			{
				$return = false;

				if (strpos($filename, '/') === 0)
				{
					$this->push_error(mpv::ERROR_FAIL, 'OPEN_FAIL_LEADING_SLASH');
				}
				else if (strpos($filename, '\\') !== false)
				{
					$this->push_error(mpv::ERROR_FAIL, 'OPEN_FAIL_BACKSLASH');
				}
				else if (preg_match('#language/[a-z_.]+/(.*)\.php#', $filename))
				{
					$this->push_error(mpv::ERROR_NOTICE, 'OPEN_FAIL_TRANSLATION');
				}
				else if (preg_match('#styles/[a-z_.]+/(.*)/#', $filename))
				{
					$this->push_error(mpv::ERROR_NOTICE, 'OPEN_FAIL_STYLE');
				}
				else
				{
					$this->push_error(mpv::ERROR_FAIL, 'OPEN_FAIL_NOT_EXISTS');
				}
			}
		}

		return $return;
	}

	/**
	 * Check if the opens are the same.
	 *
	 * @access private
	 * @return bool
	 */
	protected function test_copy()
	{
		$copy_ary = $this->modx_object->get_by_name('//action-group/copy/file', false);

		$return = true;

		foreach ($copy_ary as $copy_tag)
		{
			$from	= str_replace('\\', '/', $copy_tag->attributes['from']);
			$to		= str_replace('\\', '/', $copy_tag->attributes['to']);
			if (isset ($from) && isset($to) && trim(basename($from)) != trim(basename($to)))
			{
				$this->push_error(mpv::ERROR_FAIL, 'COPY_BASENAME_DIFFER', array(basename($from), basename($to)));

				$return = false;
			}
		}

		return $return;
	}

	/**
	* Attempt to detect whether edit tags are well-formed
	* @access private
	* @return bool
	*/
	protected function test_edit()
	{
		// First, grab all of the edit tags
		$edit_ary = $this->modx_object->get_by_name('edit', false);

		$return = true;

		// Then grab the children of each edit tag
		foreach ($edit_ary as $edit_tag)
		{
			// This is pretty darn simplistic, but it stands to reason.
			// Two children is normal, three will happen occasionally
			// Four is very rare unless the author did something wrong :)
			// And at five we give a fail by default.
			// PAUL: changed from 2 3/5 to 5/7.
			if (isset($edit_tag->children[5]))
			{
				$this->push_error(mpv::ERROR_NOTICE, 'MANY_EDIT_CHILDREN');

				$return = false;
			}
			else if (isset($edit_tag->children[7])) // Changed from 3 to 5, no idea why this one was the same as above?
			{
				$this->push_error(mpv::ERROR_FAIL, 'MANY_EDIT_CHILDREN');

				$return = false;
			}
		}
		return $return;
	}

	/**
	* Check if license matches "boilerplate" GPLv2 notice
	* @access private
	* @return bool
	*/
	protected function test_license()
	{
		$license = $this->modx_object->get_by_name('license', true);

		if (!is_object($license))
		{
			$this->push_error(mpv::ERROR_FAIL, 'LICENSE_FIELD_MISSING');

			return false;
		}
		$license = $license->value;

		if ($license != 'http://opensource.org/licenses/gpl-license.php GNU General Public License v2')
		{
			$this->push_error(mpv::ERROR_WARNING, 'LICENSE_NOT_GPL2');

			return false;
		}

		return true;
	}

	/**
	* Test whether in-line find tags contain new lines
	* @access private
	* @return bool
	*/
	protected function test_inline_find()
	{
		$return = true;
		$inline_find_ary = $this->modx_object->get_by_name('inline-find', false);

		foreach ($inline_find_ary as $inline_find_tag)
		{
			$contents = $inline_find_tag->value;
			if (strpos($contents, "\n") !== false)
			{
				$return = false;
				$this->push_error(mpv::ERROR_FAIL, 'INVALID_INLINE_FIND', $contents);
			}
		}

		return $return;
	}

	/**
	* Test whether in-line action tags contain new lines
	* @access private
	* @return bool
	*/
	protected function test_inline_action()
	{
		$return = true;
		$inline_action_ary = $this->modx_object->get_by_name('inline-action', false);

		foreach ($inline_action_ary as $inline_action_tag)
		{
			$contents = $inline_action_tag->value;
			if (strpos($contents, "\n") !== false)
			{
				$return = false;
				$this->push_error(mpv::ERROR_FAIL, 'INVALID_INLINE_ACTION', $contents);
			}
		}

		return $return;
	}

	/**
	* Test whether links exist
	* Note: links can be external, and we can't very well check those. They are
	* exempt from the check
	* @return bool
	* @access private
	*/
	protected function test_links()
	{
		$return = true;
		$link_ary = $this->modx_object->get_by_name('link', false);

		foreach ($link_ary as $link_tag)
		{
			$link_href = $link_tag->attributes['href'];

			if (!file_exists($this->validator->temp_dir . $this->modx_dir . $link_href) && strpos($link_href, '://') === false)
			{
				$return = false;
				$this->push_error(mpv::ERROR_FAIL, 'LINK_NOT_EXISTS', $link_href);
			}
		}

		return $return;
	}

	/**
	* Test the phpBB version
	*/
	protected function test_phpbb_version()
	{
		$return = true;

		$phpbb_version = $this->modx_object->get_by_name('target-version', true);
		//If we're only going to be local then we get the version from the config file
		$current_phpbb_version = mpv::get_current_version('phpbb');

		if (!is_object($phpbb_version))
		{
			$this->push_error(mpv::ERROR_FAIL, 'TARGET_VERSION_NOT_FOUND');

			$return = false;
		}
		else if (version_compare(strtolower(trim($phpbb_version->value)), strtolower($current_phpbb_version), '<'))
		{
			if (mpv::$mod_dir . '/' . basename($this->modx_filename) == $this->modx_filename)
			{
				$this->push_error(mpv::ERROR_FAIL , 'NOT_LATEST_PHPBB', array($phpbb_version->value, $current_phpbb_version));
			}
			else
			{
				$this->push_error(mpv::ERROR_WARNING , 'NOT_LATEST_PHPBB', array($phpbb_version->value, $current_phpbb_version));
			}

			$return = false;
		}

		return $return;
	}
}
