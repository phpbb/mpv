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

$phpEx = substr(strrchr(__FILE__, '.'), 1);
$root_path = './';

// Will define all needed things.
require($root_path . 'mpv_config.' . $phpEx);
require($root_path . 'includes/lang.' . $phpEx);

if (version_compare(PHP_VERSION, '5.2.0', '<'))
{
    die($lang['MIN_PHP']);
}
require($root_path . 'includes/functions_mpv.' . $phpEx);
require($root_path . 'includes/mpv.' . $phpEx);

register_shutdown_function('end_output');

error_reporting(E_ALL);

$id = $_SERVER['QUERY_STRING'];
$ip = $_SERVER['REMOTE_ADDR'];

if (strlen($id) <= 5)
{
 	die($lang['INVALID_ID']);
}

$mpv = new mpv();

if (strpos($id, 'http://') !== false)
{
	define('HTML_FORMAT', true);
	$mpv->output_type = ((HTML_FORMAT) ? mpv::OUTPUT_HTML : mpv::OUTPUT_BBCODE);
	$data = @file_get_contents($id);
}
else if (preg_match("#([0-9]+)/([a-z0-9]+)\.zip#si", $id))
{
	$data = @file_get_contents('http://www.phpbb.com/mods/db/eal/' . $id);
}
else
{
	$data = false;
}

if (!$data || !isset($data))
{
	die($lang['NO_DATA']);
}

$file = 'store/' . md5(unique_id($ip)) . '.zip';

$o = fopen($file, 'wb');
fwrite($o, $data);
fclose($o);

$mpv->validate($file);
$mpv->server_signature .= sprintf("mpv%s", SERVER_ID);
@unlink($file);

if (HTML_FORMAT)
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