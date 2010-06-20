<?php
/**
*
* MPV language [English]
*
* @package mpv
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	// Only used for debug purposes.
	'ZIP_METHOD'			=> 'Using %s as zip method',
	'TYPE_PHP'			=> 'php',
	'TYPE_PHPBB'			=> 'phpBB',
	'TYPE_EXEC'			=> 'exec',
	'INVALID_ZIP_METHOD'		=> 'Invalid zip method %s',

	'TITLE'					=> 'phpBB MOD Pre-Validator Results',
	'TITLE_STATS'			=> 'MPV Stats',
	'MIN_PHP'				=> 'PHP 5.2.0 is required',
	'INVALID_ID'			=> 'Invalid ID',
	'NO_DATA'				=> 'No data found',
	'VALIDATION_RESULTS'	=> 'Validation results:',
	'MEM_USE'				=> 'Memory Usage:',
	'TOTAL_TIME'			=> 'Time : %.3fs',
	'GB'					=> 'GB',
	'GIB'					=> 'GiB',
	'MB'					=> 'MB',
	'MIB'					=> 'MiB',
	'BYTES'					=> 'Bytes',
	'KB'					=> 'KB',
	'KIB'					=> 'KiB',
	'NO_MODX_FILES'			=> 'No MODX files found in package',
	'MODX_SCHEMA_INVALID'	=> 'Invalid XML/MODX [code]%s[/code]',

	'FSOCK_DISABLED'		=> 'The operation could not be completed because the <var>fsockopen</var> function has been disabled or the server being queried could not be found.',
	'FILE_NOT_FOUND'		=> 'The requested file could not be found',
	'VERSION_FIELD_MISSING'		=> 'The version element is missing from the MODX file.',
	'LICENSE_FIELD_MISSING'		=> 'The license element is missing from the MODX file.',
	'TARGET_VERSION_NOT_FOUND'	=> 'The target-version element is missing from the MODX file',
	'INVALID_VERSION_FORMAT'	=> 'The supplied version (%s) is invalid. The format should be: 1.0.0.',

	'VALIDATING_ZIP'			=> '(Validating %s)',
	'MAJOR_VERSION_UNSTABLE'	=> 'Your MOD version (%s) is unstable. It should be higher starting at 1.0.0.
	Example:
	[b]0.0.1[/b] is unstable
	[b]0.1.0[/b] is unstable
	[b]1.0.1[/b] is stable',

	'NOT_LATEST_PHPBB'		=> 'Target revision in the MODX file says the MOD is for %s while the latest phpBB version is %s',

	'INVALID_INLINE_ACTION'	=> 'An inline action contains new lines [code]%s[/code]',
	'INVALID_INLINE_FIND'	=> 'An inline find contains new lines [code]%s[/code]',
	'SHORT_TAGS'			=> 'This file is using short open tags (<? instead of <?php) at line %s: %s',

	'LICENSE_NOT_GPL2'		=> 'The license specified in the MODX file may not be the GPLv2.',

	'MANY_EDIT_CHILDREN'	=> 'The MOD uses many edit children.  This could indicate incorrect usage of the edit tag.',
	'MULTIPLE_XSL'			=> 'You have multiple XSL files.  It is preferred to have only one XSL file so as to not confuse the user.',

	'NO_XSL_FOUND_IN_DIR'	=> 'There is no XSL found in directory %s. Starting from July 27, 2008 it is required/preferred to have an XSL file in all directories where a MODX file is located at due to a FireFox 3 limitation.',
	'NO_XSL_FOUND_IN_DIR2'	=> 'IMPORTANT: MPV cannot detect if there is an XSL at a higher directory.  Please test with FireFox 3 to verify it displays correctly.  If the MOD displays correctly you can ignore above warning! See our policy for more information.',

	'NO_LICENSE'		=> 'You are missing the required license.txt file.',
	'NO_UNIX_ENDINGS'	=> 'This file doesn\'t use UNIX line endings.',
	'NO_XSL_FILE'		=> 'You are missing the required XSL file for displaying the XML file in the browser.',

	'USAGE_MYSQL'		=> 'You are using a hardcoded MySQL function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_MYSQLI'		=> 'You are using a hardcoded MySQLi function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_OCI'			=> 'You are using a hardcoded oci (oracle) function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_SQLITE'		=> 'You are using a hardcoded SQLite function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_PG'			=> 'You are using a hardcoded PostgreSQL function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_MSSQL'		=> 'You are using a hardcoded MSSQL function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_ODBC'		=> 'You are using a hardcoded ODBC function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_SQLSRV'		=> 'You are using a hardcoded SQLSRV (MSSQL) function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_IBASE'		=> 'You are using a hardcoded ibase (Interbase/Firebird) function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',
	'USAGE_DB2'			=> 'You are using a hardcoded DB2 function at line %s: %sphpBB MODs are required to use the Database Abstraction Layer (DBAL).',

	'USAGE_GET'			=> 'Using $_GET at line %s: %srequest_var() should be used instead.',
	'USAGE_POST'		=> 'Using $_POST at line %s: %srequest_var() should be used instead.',
	'USAGE_COOKIE'		=> 'Using $_COOKIE at line %s: %srequest_var() should be used with the fourth parameter.',
	'USAGE_SERVER'		=> 'Using $_SERVER at line %s: %s[b]$_SERVER [u]IS[/u] user input![/b]',
	'USAGE_SESSION'		=> 'Using $_SESSION at line %s: %sThe phpBB session system should be used instead.',
	'USAGE_REQUEST'		=> 'Using $_REQUEST at line %s: %srequest_var should be used instead.',
	'USAGE_ENV'			=> 'Using $_ENV at line %s: %s',
	'USAGE_FILES'		=> 'Using $_FILES at line %s: %sThe upload functions included in phpBB should be used instead.',
	'USAGE_GLOBALS'		=> 'Using $GLOBALS at line %s: %s',

	'USAGE_PRINT'		=> 'Using print() at line %s: %s The phpBB template system should be used instead.',
	'USAGE_PRINTF'		=> 'Using printf() at line %s: %s The phpBB template system should be used instead.',
	'USAGE_ECHO'		=> 'Using echo() at line %s: %s The phpBB template system should be used instead.',
	'USAGE_PRINTR' 		=> 'Using printr() at line %s: %s The phpBB template system should be used instead.',

	'USAGE_`'				=> 'Using backticks at line %s: %s',
	'USAGE_EVAL'			=> 'Using eval() at line %s: %s',
	'USAGE_EXEC'			=> 'Using exec() at line %s: %s',
	'USAGE_SYSTEM'			=> 'Using system() at line %s: %s',
	'USAGE_PASSTHRU'		=> 'Using passthru() at line %s: %s',
	'USAGE_GETENV'			=> 'Using getenv() at line %s: %s',
	'USAGE_DIE'				=> 'Using die() at line %s: %s',
	'USAGE_MD5'				=> 'Using md5() at line %s: %sMD5 should not be used for anything related to passwords. Other usage of MD5 is probably valid.',
	'USAGE_SHA1'			=> 'Using sha1() at line %s: %s',
	'USAGE_ADDSLASHES'		=> 'Using addslashes() at line %s: %s',
	'USAGE_STRIPSLASHES'	=> 'Using stripslashes() at line %s: %s',
	'USAGE_INCLUDEONCE'		=> 'Using include_once() at line %s: %sUsing include with a function/class_exists check is preferred over include/require _once',
	'USAGE_REQUIREONCE'		=> 'Using require_once() at line %s: %sUsing include with a function/class_exists check is preferred over include/require _once',
	'USAGE_VARDUMP'			=> 'Using var_dump at line %s: %s',

	'USAGE_BOM'				=> 'Your file is using a UTF-8 byte-order-mark (BOM)',

	'USAGE_REQUEST_VAR_INT'	=> 'A call to request_var() is casting an integer to a string at line %s: %s',

	'UNWANTED_FILE'			=> 'Your package contains an unwanted file "%2$s" in %1$s',

	'INCLUDE_NO_ROOT'		=> 'A call to include or require is missing $phpbb_root_path in call at line %s: %s',
	'INCLUDE_NO_PHP'		=> 'A call to include or require is missing $phpEx in call at line %s: %s',

	'PACKAGE_NOT_EXISTS'	=> 'Choosen file (%s) does not exist',
	'UNABLE_EXTRACT_PHP'	=> 'Unable to extract %s using php.',
	'UNABLE_OPEN_PHP'	=> 'Unable to open %s using php.',

	'LINK_NOT_EXISTS'		=> 'The file(s) for link %s do(es) not exist in the zip file.',

	'NO_IN_PHPBB'			=> 'A define for IN_PHPBB is missing or there is no check for if IN_PHPBB is set.',
	'FILE_EMPTY'			=> 'This PHP file has been detected as being empty.',

	'COPY_BASENAME_DIFFER'	=> 'Basenames of the copy command differ: From %s to %s.  Both should be the same',

	'USING_MODX_OUTDATED'	=> 'You are using MODX version %s while the latest release of MODX is %s.  Please update your MODX file to the latest version.',
	'USING_MODX_UNKNOWN'  => 'Found a invalid MODX version for XML file.  Cannot continue pre-validating this file.',

	'PROSILVER_NO_MAIN_MODX'	=> 'ProSilver style changes should be placed in the main MODX file and not in %s.',
	'ENGLISH_NO_MAIN_MODX'		=> 'English language changes should be placed in the main MODX file and not in %s.',

	'MPV_XML_ERROR'			=> 'XML error in MODX file %s',

	'USAGE_BR_NON_CLOSED'	=> 'A BR is not closed correctly which causes invalid XHTML at line %s: %s',
	'USAGE_IMG_NON_CLOSED'	=> 'An IMG is not closed correctly which causes invalid XHTML at line %s: %s',

	'FILE_NON_BINARY'		=> 'File has been detected as non-binary while the extension IS binary. Checking for PHP code for security reasons. ',

	'GENERAL_NOTICE'  => 'Please note that all checks are done by an automated tool.  In [u]some[/u] cases a FAIL/WARNING can be valid/allowed usage of a function.',

	'UNABLE_OPEN' => 'Unable to open %s.',
	'UNABLE_WRITE'=> 'Unable to write to %s.',
	'PHP_ERROR'   => 'A PHP error was found: [code]%s[/code]',
	'NO_PRE_VAL_ERRORS'	=> 'No pre-validation problems found',
	'REPORT_BY'		=> 'Report made by MPV',
	'MPV_SERVER'	=> 'MPV server',
	'UNKNOWN_OUTPUT'	=> 'Unknown output type',
	'MPV_NOTICE'		=> 'MPV notice found at %1$s line %2$s: %3$s',
	'MPV_WARNING'		=> 'MPV warning found at %1$s line %2$s: %3$s',
	'MPV_NOTICE'		=> 'MPV notice found at %1$s line %2$s: %3$s',
	'MPV_USER_NOTICE'		=> 'MPV user notice found at %1$s line %2$s: %3$s',
	'MPV_GENERAL_ERROR'		=> 'MPV encountered an error at %1$s line %2$s: %3$s',
	'MPV_FAIL_RESULT'			=> 'FAIL',
	'MPV_NOTICE_RESULT'			=> 'NOTICE',
	'MPV_WARNING_RESULT'		=> 'WARNING',
	'MPV_INFO_RESULT'			=> 'INFO',
	'INVALID_TYPE'			=> 'Invalid $type for this function!',
	'MOD_NAME'				=> 'MOD Name',
	'SUBMIT_COUNT'			=> 'Submission Count',
	'TAG_DATA'				=> 'Tag Data',
	'DATA_COUNT'			=> 'Data Count',
	
	'MODIFIED_XSL'		=> 'The MD5 signature of the XSL file %s is unknown, file might be modified. Found signature %s, expected newest %s',
	'OLD_XSL'		=> 'You are using a old version of the MODX xsl for file %s. You should update the XSL prior to submitting',
	'LICENSE_MD5'		=> 'Found a wrong md5 signature for %s. Found %s while expected %s',
));
