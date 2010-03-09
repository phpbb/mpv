<?php
/**
* an example call
*
* @package mpv
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

$phpEx = substr(strrchr(__FILE__, '.'), 1);
$root_path = './';

// Will define all needed things.
require('./mpv_config.' . $phpEx);
require('./includes/lang.' . $phpEx);

if (version_compare(PHP_VERSION, '5.2.0', '<'))
{
	die($lang['MIN_PHP']);
}
require('./includes/functions_mpv.' . $phpEx);
require('./includes/mpv.' . $phpEx);

//Override HTML format
define('HTML_FORMAT', true);

register_shutdown_function('end_output');

error_reporting(E_ALL);

$mpv = new mpv(null, mpv::UNZIP_PREFERENCE, false);
$mpv->output_type = ((HTML_FORMAT) ? mpv::OUTPUT_HTML : mpv::OUTPUT_BBCODE);
$mpv->validate('my-mod.zip');

if (HTML_HEADERS)
{
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-gb" xml:lang="en-gb">
	<head>
	<title>' . $lang['TITLE'] . '</title>
	</head>
	<body>';
}

print $lang['VALIDATION_RESULTS'] . "\n\n" . ((HTML_FORMAT) ? nl2br($mpv) : $mpv);

?>
