<?php
/**
* File list for phpBB.
* Just enter the base_dir value for a phpBB directory containing all files and open in browser
* Then copy and paste results into includes/tests/filelist.txt
*
* @package mpv_server
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license Internal use only
*
*/

$base_dir = 'phpBB3/';
list_files($base_dir);

function list_files($dir)
{
	global $base_dir;
	
	if ($handle = opendir($dir))
	{
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handle)))
		{
			if (is_file($dir . $file))
			{
				echo str_replace($base_dir, '', "$dir$file<br />");
			}
			else if (is_dir($dir . $file . '/') && $file != '.' && $file != '..')
			{
				list_files($dir . $file . '/');
			}
		}
		closedir($handle);
	}
}

?> 