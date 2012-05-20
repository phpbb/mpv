<?php
/**
* an example call
*
* @package mpv
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

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

//Override HTML format
define('HTML_FORMAT', true);

register_shutdown_function('end_output');

error_reporting(E_ALL);

$mpv = new mpv(null, mpv::UNZIP_PREFERENCE, false);
$mpv->output_type = ((HTML_FORMAT) ? mpv::OUTPUT_HTML : mpv::OUTPUT_BBCODE);
mpv::$exec_php = mpv::EXEC_PHP;
$mpv->validate('./test-file.zip');

	print '<!DOCTYPE html>
	<head>
	<title>' . $lang['TITLE'] . '</title>
    <link rel="stylesheet" href="style.css">
	</head>
	<body>';
	print '<div class="wrapper">';	
	print '<header>';
	print '<h2>' . $lang['VALIDATION_RESULTS'] . "</h2></br></header>";
	print '<section>';
	print ((HTML_FORMAT) ? nl2br($mpv) : $mpv);
	print '</section>';
	print '</div>';	
	print '</body></html> ';


?>
