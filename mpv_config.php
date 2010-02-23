<?php
/**
* Config file
*
* @package mpv_server
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license Internal use only
*
*/

// Measure the amount of time MPV takes to process zip package
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// Server ID. Can be any number.
define('SERVER_ID', 1);

// If you don't want to connect to phpbb.com to get the xsd then set this to true
// and copy the xsd(s) to the root/includes/xsd directory of your MPV copy
define('LOCAL_ONLY', false);

// Return with HTML formatting or not
// Useful if displaying through forum
// Third parameter allows for overriding
define('HTML_FORMAT', true, true);

// Sometimes we want HTML formatting without the HTML headers
define('HTML_HEADERS', false);

// Latest phpBB3 version. Only used if LOCAL_ONLY is set to true
define('PHPBB_VERSION', '3.0.6');

// Latest MODX version. Only used if LOCAL_ONLY is set to true
define('LATEST_MODX', '1.2.3');

// This is for statistics gathering
// Format is:  'tag' => array('properties')
$statistics = array(
						'meta'		=> array('name', 'content'),
				);

?>