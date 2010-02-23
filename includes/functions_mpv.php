<?php
/**
* Main file
*
* @package mpv_server
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license Internal use only
*
*/

function end_output()
{
	global $starttime, $lang;
	
	// Some debug stuff.
	$mtime = explode(' ', microtime());
	$totaltime = $mtime[0] + $mtime[1] - $starttime;

	$debug_output = "\n" . sprintf($lang['TOTAL_TIME'], $totaltime);

	if (function_exists('memory_get_usage'))
	{
		if ($memory_usage = memory_get_usage())
		{
			global $base_memory_usage;
			$memory_usage -= $base_memory_usage;
			$memory_usage = get_formatted_filesize($memory_usage);

			$debug_output .= ' | ' . $lang['MEM_USE'] . ' ' . $memory_usage;
		}
	}

	print $debug_output . ((HTML_HEADERS) ? "</body></html>" : "");
}

/**
* Return formatted string for filesizes - "Borrowed" from phpBB3
*
* @param int	$value			filesize in bytes
* @param bool	$string_only	true if language string should be returned
* @param array	$allowed_units	only allow these units (data array indexes)
*
* @return mixed					data array if $string_only is false
* @author bantu
*/
function get_formatted_filesize($value, $string_only = true, $allowed_units = false)
{
	global $lang;
	
	$available_units = array(
		'gb' => array(
			'min' 		=> 1073741824, // pow(2, 30)
			'index'		=> 3,
			'si_unit'	=> $lang['GB'],
			'iec_unit'	=> $lang['GIB'],
		),
		'mb' => array(
			'min'		=> 1048576, // pow(2, 20)
			'index'		=> 2,
			'si_unit'	=> $lang['MB'],
			'iec_unit'	=> $lang['MIB'],
		),
		'kb' => array(
			'min'		=> 1024, // pow(2, 10)
			'index'		=> 1,
			'si_unit'	=> $lang['KB'],
			'iec_unit'	=> $lang['KIB'],
		),
		'b' => array(
			'min'		=> 0,
			'index'		=> 0,
			'si_unit'	=> $lang['BYTES'], // Language index
			'iec_unit'	=> $lang['BYTES'],  // Language index
		),
	);

	foreach ($available_units as $si_identifier => $unit_info)
	{
		if (!empty($allowed_units) && $si_identifier != 'b' && !in_array($si_identifier, $allowed_units))
		{
			continue;
		}

		if ($value >= $unit_info['min'])
		{
			$unit_info['si_identifier'] = $si_identifier;

			break;
		}
	}
	unset($available_units);

	for ($i = 0; $i < $unit_info['index']; $i++)
	{
		$value /= 1024;
	}
	$value = round($value, 2);

	// Default to IEC
	$unit_info['unit'] = $unit_info['iec_unit'];

	if (!$string_only)
	{
		$unit_info['value'] = $value;

		return $unit_info;
	}

	return $value  . ' ' . $unit_info['unit'];
}

/**
* Return unique id - Based on phpBB3 function
* @param string $extra additional entropy
*/
function unique_id($extra = 'c')
{
	$rand_seed = md5(md5(rand(1000000, 9999999) . microtime()) . $extra);

	return substr($rand_seed, 4, 16);
}

/**
* @author Chris Smith <chris@project-minerva.org>
* @copyright 2006 Project Minerva Team
* @param string $path The path which we should attempt to resolve.
* @return mixed
*/
function phpbb_own_realpath($path)
{
	// Now to perform funky shizzle

	// Switch to use UNIX slashes
	$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
	$path_prefix = '';

	// Determine what sort of path we have
	if (is_absolute($path))
	{
		$absolute = true;

		if ($path[0] == '/')
		{
			// Absolute path, *NIX style
			$path_prefix = '';
		}
		else
		{
			// Absolute path, Windows style
			// Remove the drive letter and colon
			$path_prefix = $path[0] . ':';
			$path = substr($path, 2);
		}
	}
	else
	{
		// Relative Path
		// Prepend the current working directory
		if (function_exists('getcwd'))
		{
			// This is the best method, hopefully it is enabled!
			$path = str_replace(DIRECTORY_SEPARATOR, '/', getcwd()) . '/' . $path;
			$absolute = true;
			if (preg_match('#^[a-z]:#i', $path))
			{
				$path_prefix = $path[0] . ':';
				$path = substr($path, 2);
			}
			else
			{
				$path_prefix = '';
			}
		}
		else if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME']))
		{
			// Warning: If chdir() has been used this will lie!
			// Warning: This has some problems sometime (CLI can create them easily)
			$path = str_replace(DIRECTORY_SEPARATOR, '/', dirname($_SERVER['SCRIPT_FILENAME'])) . '/' . $path;
			$absolute = true;
			$path_prefix = '';
		}
		else
		{
			// We have no way of getting the absolute path, just run on using relative ones.
			$absolute = false;
			$path_prefix = '.';
		}
	}

	// Remove any repeated slashes
	$path = preg_replace('#/{2,}#', '/', $path);

	// Remove the slashes from the start and end of the path
	$path = trim($path, '/');

	// Break the string into little bits for us to nibble on
	$bits = explode('/', $path);

	// Remove any . in the path, renumber array for the loop below
	$bits = array_values(array_diff($bits, array('.')));

	// Lets get looping, run over and resolve any .. (up directory)
	for ($i = 0, $max = sizeof($bits); $i < $max; $i++)
	{
		// @todo Optimise
		if ($bits[$i] == '..' )
		{
			if (isset($bits[$i - 1]))
			{
				if ($bits[$i - 1] != '..')
				{
					// We found a .. and we are able to traverse upwards, lets do it!
					unset($bits[$i]);
					unset($bits[$i - 1]);
					$i -= 2;
					$max -= 2;
					$bits = array_values($bits);
				}
			}
			else if ($absolute) // ie. !isset($bits[$i - 1]) && $absolute
			{
				// We have an absolute path trying to descend above the root of the filesystem
				// ... Error!
				return false;
			}
		}
	}

	// Prepend the path prefix
	array_unshift($bits, $path_prefix);

	$resolved = '';

	$max = sizeof($bits) - 1;

	// Check if we are able to resolve symlinks, Windows cannot.
	$symlink_resolve = (function_exists('readlink')) ? true : false;

	foreach ($bits as $i => $bit)
	{
		if (@is_dir("$resolved/$bit") || ($i == $max && @is_file("$resolved/$bit")))
		{
			// Path Exists
			if ($symlink_resolve && is_link("$resolved/$bit") && ($link = readlink("$resolved/$bit")))
			{
				// Resolved a symlink.
				$resolved = $link . (($i == $max) ? '' : '/');
				continue;
			}
		}
		else
		{
			// Something doesn't exist here!
			// This is correct realpath() behaviour but sadly open_basedir and safe_mode make this problematic
			// return false;
		}
		$resolved .= $bit . (($i == $max) ? '' : '/');
	}

	// @todo If the file exists fine and open_basedir only has one path we should be able to prepend it
	// because we must be inside that basedir, the question is where...
	// @internal The slash in is_dir() gets around an open_basedir restriction
	if (!@file_exists($resolved) || (!is_dir($resolved . '/') && !is_file($resolved)))
	{
		return false;
	}

	// Put the slashes back to the native operating systems slashes
	$resolved = str_replace('/', DIRECTORY_SEPARATOR, $resolved);

	// Check for DIRECTORY_SEPARATOR at the end (and remove it!)
	if (substr($resolved, -1) == DIRECTORY_SEPARATOR)
	{
		return substr($resolved, 0, -1);
	}

	return $resolved; // We got here, in the end!
}

if (!function_exists('realpath'))
{
	/**
	* A wrapper for realpath
	* @ignore
	*/
	function phpbb_realpath($path)
	{
		return phpbb_own_realpath($path);
	}
}
else
{
	/**
	* A wrapper for realpath
	*/
	function phpbb_realpath($path)
	{
		$realpath = realpath($path);

		// Strangely there are provider not disabling realpath but returning strange values. :o
		// We at least try to cope with them.
		if ($realpath === $path || $realpath === false)
		{
			return phpbb_own_realpath($path);
		}

		// Check for DIRECTORY_SEPARATOR at the end (and remove it!)
		if (substr($realpath, -1) == DIRECTORY_SEPARATOR)
		{
			$realpath = substr($realpath, 0, -1);
		}

		return $realpath;
	}
}

/**
* Display validation results as HTML
*/
function generate_text_for_html_display($text)
{
	//Replace new 
	$text = str_replace("\n", "<br />\n", $text);
	
	//BBCode replacement array
	$bbcode = array(
		"/\[b\](.*?)\[\/b\]/is" => '<span style="font-weight:bold;">$1</span>',
		"/\[u\](.*?)\[\/u\]/is" => '<span style="text-decoration:underline;">$1</span>',
		"/\[color\=(.*?)\](.*?)\[\/color\]/is" => '<span style="color:$1;">$2</span>',
		"/\[code\](.*?)\[\/code\]/is" => '<pre style="padding-left:20px;">$1</pre>',
	);

	//Replace BBCode
	$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
	return $text;
}

// phpbb_chmod() permissions
@define('CHMOD_ALL', 7);
@define('CHMOD_READ', 4);
@define('CHMOD_WRITE', 2);
@define('CHMOD_EXECUTE', 1);

/**
* Global function for chmodding directories and files for internal use
*
* This function determines owner and group whom the file belongs to and user and group of PHP and then set safest possible file permissions.
* The function determines owner and group from common.php file and sets the same to the provided file.
* The function uses bit fields to build the permissions.
* The function sets the appropiate execute bit on directories.
*
* Supported constants representing bit fields are:
*
* CHMOD_ALL - all permissions (7)
* CHMOD_READ - read permission (4)
* CHMOD_WRITE - write permission (2)
* CHMOD_EXECUTE - execute permission (1)
*
* NOTE: The function uses POSIX extension and fileowner()/filegroup() functions. If any of them is disabled, this function tries to build proper permissions, by calling is_readable() and is_writable() functions.
*
* @param string	$filename	The file/directory to be chmodded
* @param int	$perms		Permissions to set
*
* @return bool	true on success, otherwise false
* @author faw, phpBB Group
*/
function phpbb_chmod($filename, $perms = CHMOD_READ)
{
	static $_chmod_info;

	// Return if the file no longer exists.
	if (!file_exists($filename))
	{
		return false;
	}

	// Determine some common vars
	if (empty($_chmod_info))
	{
		if (!function_exists('fileowner') || !function_exists('filegroup'))
		{
			// No need to further determine owner/group - it is unknown
			$_chmod_info['process'] = false;
		}
		else
		{
			global $root_path, $phpEx;

			// Determine owner/group of common.php file and the filename we want to change here
			$common_php_owner = @fileowner($phpbb_root_path . 'index.' . $phpEx);
			$common_php_group = @filegroup($phpbb_root_path . 'index.' . $phpEx);

			// And the owner and the groups PHP is running under.
			$php_uid = (function_exists('posix_getuid')) ? @posix_getuid() : false;
			$php_gids = (function_exists('posix_getgroups')) ? @posix_getgroups() : false;

			// If we are unable to get owner/group, then do not try to set them by guessing
			if (!$php_uid || empty($php_gids) || !$common_php_owner || !$common_php_group)
			{
				$_chmod_info['process'] = false;
			}
			else
			{
				$_chmod_info = array(
					'process'		=> true,
					'common_owner'	=> $common_php_owner,
					'common_group'	=> $common_php_group,
					'php_uid'		=> $php_uid,
					'php_gids'		=> $php_gids,
				);
			}
		}
	}

	if ($_chmod_info['process'])
	{
		$file_uid = @fileowner($filename);
		$file_gid = @filegroup($filename);

		// Change owner
		if (@chown($filename, $_chmod_info['common_owner']))
		{
			clearstatcache();
			$file_uid = @fileowner($filename);
		}

		// Change group
		if (@chgrp($filename, $_chmod_info['common_group']))
		{
			clearstatcache();
			$file_gid = @filegroup($filename);
		}

		// If the file_uid/gid now match the one from common.php we can process further, else we are not able to change something
		if ($file_uid != $_chmod_info['common_owner'] || $file_gid != $_chmod_info['common_group'])
		{
			$_chmod_info['process'] = false;
		}
	}

	// Still able to process?
	if ($_chmod_info['process'])
	{
		if ($file_uid == $_chmod_info['php_uid'])
		{
			$php = 'owner';
		}
		else if (in_array($file_gid, $_chmod_info['php_gids']))
		{
			$php = 'group';
		}
		else
		{
			// Since we are setting the everyone bit anyway, no need to do expensive operations
			$_chmod_info['process'] = false;
		}
	}

	// We are not able to determine or change something
	if (!$_chmod_info['process'])
	{
		$php = 'other';
	}

	// Owner always has read/write permission
	$owner = CHMOD_READ | CHMOD_WRITE;
	if (is_dir($filename))
	{
		$owner |= CHMOD_EXECUTE;

		// Only add execute bit to the permission if the dir needs to be readable
		if ($perms & CHMOD_READ)
		{
			$perms |= CHMOD_EXECUTE;
		}
	}

	switch ($php)
	{
		case 'owner':
			$result = @chmod($filename, ($owner << 6) + (0 << 3) + (0 << 0));

			clearstatcache();

			if (is_readable($filename) && is_writable($filename))
			{
				break;
			}

		case 'group':
			$result = @chmod($filename, ($owner << 6) + ($perms << 3) + (0 << 0));

			clearstatcache();

			if ((!($perms & CHMOD_READ) || is_readable($filename)) && (!($perms & CHMOD_WRITE) || is_writable($filename)))
			{
				break;
			}

		case 'other':
			$result = @chmod($filename, ($owner << 6) + ($perms << 3) + ($perms << 0));

			clearstatcache();

			if ((!($perms & CHMOD_READ) || is_readable($filename)) && (!($perms & CHMOD_WRITE) || is_writable($filename)))
			{
				break;
			}

		default:
			return false;
		break;
	}

	return $result;
}

?>