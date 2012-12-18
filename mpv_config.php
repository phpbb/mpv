<?php
/**
* Config file
*
* @package mpv_server
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
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
define('PHPBB_VERSION', '3.0.11');

// Latest MODX version. Only used if LOCAL_ONLY is set to true
define('LATEST_MODX', '1.2.6');

// Latest UMIL version. Only used if LOCAL_ONLY is set to true
define('LATEST_UMIL', '1.0.5');

// MPV Debug, disabled by default, will add some extra info about the MPV status
define('MPV_DEBUG', false);

// MPV language, by default english. Any possiblities from includes/languages/ is allowed.
// Use the _exact_ directory name
define('MPV_LANG', 'en');

// Files that generate a warning, and that _arent_ checked.
// Seperate with |
define('UNWANTED', '__macosx|.ds_store|thumbs.db|.svn|.git|.project');

// This is for statistics gathering
// Format is:  'tag' => array('properties')
$statistics = array(
						'meta'		=> array('name', 'content'),
				);

?>
