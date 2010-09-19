#!/usr/bin/php
<?php
/**
* command line client
* usage:
* path/to/php.exe mpv.php my-mod.zip
*
* @package mpv
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

if (php_sapi_name() != 'cli')
{
	die('Please run this from the command line.');
}

if (!isset($argv[1]))
{
	die("Usage: mpv my-mod.zip\n");
}

if (!file_exists($argv[1]))
{
	die("Error: file {$argv[1]} not found\n");
}

$phpEx = substr(strrchr(__FILE__, '.'), 1);
$root_path = './';

// Will define all needed things.
require('./mpv_config.' . $phpEx);

if (version_compare(PHP_VERSION, '5.2.0', '<'))
{
	die($lang['MIN_PHP']);
}
require('./includes/functions_mpv.' . $phpEx);
require('./includes/mpv.' . $phpEx);

error_reporting(E_ALL);

$mpv = new mpv(null, mpv::UNZIP_PREFERENCE, false);
$mpv->output_type = (mpv::OUTPUT_TEXT);
$mpv->validate($argv[1]);

print $lang['VALIDATION_RESULTS'] . "\n\n" . $mpv;
