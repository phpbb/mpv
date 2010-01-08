<?php
/**
* an example call
*
* @package mpv
* @version $Id$
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

// if you dont want to use a phpBB installation, define the next constant.
// Please note, only the EXEC (So basicly linux) is supported then as unzip method.
// If you have enabled this piece of code, you can securly remove the code below.
// define ('NO_PHPBB', true);

// Standard phpBB includes
define('IN_PHPBB', true);
$phpbb_root_path = './../../www/community/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

require_once($phpbb_root_path . 'common.' . $phpEx);
error_reporting(E_ALL);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

// $user->add_lang('lang'); // If you want to display text place lang in language/en :)

if (version_compare(PHP_VERSION, '5.2.1', '<'))
{
	trigger_error('PHP 5.2.1 is required');
}

require_once('mpv.php');

$mpv = new mpv('./');
$mpv->validate('my-mod.zip');

print "Validation results:\n <br />" . nl2br($mpv);

?>