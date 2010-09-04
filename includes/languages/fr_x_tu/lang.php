<?php
/**
*
* This file is part of French (Casual Honorifics) MPV translation.
* Copyright (C) 2010 phpBB.fr
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*
* MPV language [French (Casual Honorifics)]
*
* @package   mpv
* @author    Maël Soucaze <maelsoucaze@phpbb.fr> (Maël Soucaze) http://www.phpbb.fr/
* @copyright (c) 2010 phpBB Group
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License
* @version   $Id$
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
	'ZIP_METHOD'			=> 'Utilisant %s comme une méthode ZIP',
	'TYPE_PHP'			=> 'PHP',
	'TYPE_PHPBB'			=> 'phpBB',
	'TYPE_EXEC'			=> 'Exécutable',
	'INVALID_ZIP_METHOD'		=> '%s est une méthode ZIP invalide',

	'TITLE'					=> 'Résultats du MOD Pre-Validator de phpBB',
	'TITLE_STATS'			=> 'Statistiques de MPV',
	'MIN_PHP'				=> 'PHP 5.2.0 est obligatoire',
	'INVALID_ID'			=> 'ID invalide',
	'NO_DATA'				=> 'Aucune donnée n’a été trouvée',
	'VALIDATION_RESULTS'	=> 'Résultats de la validation :',
	'MEM_USE'				=> 'Utilisation de la mémoire :',
	'TOTAL_TIME'			=> 'Durée : %.3fs',
	'GB'					=> 'Go',
	'GIB'					=> 'Gio',
	'MB'					=> 'Mo',
	'MIB'					=> 'Mio',
	'BYTES'					=> 'Octets',
	'KB'					=> 'Ko',
	'KIB'					=> 'Kio',
	'NO_MODX_FILES'			=> 'Aucun fichier MODX n’a été trouvé dans cette archive',
	'MODX_SCHEMA_INVALID'	=> 'XML/MODX invalide [code]%s[/code]',

	'FSOCK_DISABLED'		=> 'L’opération n’a pu se terminer car la fonction <var>fsockopen</var> a été désactivée ou le serveur demandé est introuvable.',
	'FILE_NOT_FOUND'		=> 'Le fichier demandé est introuvable',
	'VERSION_FIELD_MISSING'		=> 'L’élément “version” n’a pas été spécifié dans le fichier MODX.',
	'LICENSE_FIELD_MISSING'		=> 'L’élément “license” n’a pas été spécifié dans le fichier MODX.',
	'TARGET_VERSION_NOT_FOUND'	=> 'L’élément “target-version” n’a pas été spécifié dans le fichier MODX',
	'INVALID_VERSION_FORMAT'	=> 'La version que tu as fourni (%s) est invalide. Le format doit être : 1.0.0.',

	'VALIDATING_ZIP'			=> '(En train de valider %s)',
	'MAJOR_VERSION_UNSTABLE'	=> 'La version de ton MOD (%s) est instable. Elle doit être obligatoirement plus élevée que la version initiale 1.0.0.
	Par exemple :
	[b]0.0.1[/b] est instable
	[b]0.1.0[/b] est instable
	[b]1.0.1[/b] est stable',

	'NOT_LATEST_PHPBB'		=> 'La révision cible, située dans le fichier MODX, indique que le MOD est compatible avec la version %s alors que la dernière version de phpBB est la %s',

	'INVALID_INLINE_ACTION'	=> 'Une action “dans la ligne” contient de nouvelles lignes [code]%s[/code]',
	'INVALID_INLINE_FIND'	=> 'Une action “dans la ligne, rechercher” contient de nouvelles lignes [code]%s[/code]',
	'SHORT_TAGS'			=> 'Ce fichier utilise des balises d’ouvertures raccourcies (<? à la place de <?php) à la ligne %s : %s',

	'LICENSE_NOT_GPL2'		=> 'La licence spécifiée dans le fichier MODX ne peut pas être la GPLv2.',

	'MODIFIED_XSL'		=> 'La signature MD5 du fichier XSL est inconnue, le fichier doit être modifié. Signature trouvée : %s. Signature souhaitée : %s',
	'OLD_XSL'		=> 'Tu utilises une version obsolète du fichier XSL de MODX. Tu devrais mettre à jour le XSL avant tout transfert',
	'LICENSE_MD5'		=> 'Une signature MD5 invalide a été trouvée. Signature trouvée : %s. Signature souhaitée : %s',	

	'MANY_EDIT_CHILDREN'	=> 'Le MOD utilise de nombreuses requêtes d’éditions. Cela peut indiquer un usage incorrect de la balise d’édition.',
	'MULTIPLE_XSL'			=> 'Tu bénéficies de nombreux fichiers XSL. Il est recommandé de n’avoir qu’un seul et unique fichier XSL afin de ne pas déstabiliser les utilisateurs.',

	'NO_XSL_FOUND_IN_DIR'	=> 'Aucun fichier XSL n’a été trouvé dans le répertoire %s. Depuis le 27 juillet 2008, il est obligatoire d’avoir un fichier XSL dans tous les répertoires contenant un fichier MODX à cause d’une limitation du navigateur Mozilla Firefox 3.',
	'NO_XSL_FOUND_IN_DIR2'	=> 'IMPORTANT : MPV ne peut pas détecter s’il y a bien un fichier XSL dans un répertoire supérieur. Teste l’affichage avec le navigateur Mozilla Firefox 3 afin de t’assurer que tout fonctionne correctement. Si le MOD s’affiche correctement, tu peux alors ignorer l’avertissement affiché ci-dessus ! Pour plus d’informations, consulte notre politique de fonctionnement.',

	'NO_LICENSE'		=> 'Le fichier license.txt est introuvable alors que celui-ci est obligatoire.',
	'NO_UNIX_ENDINGS'	=> 'Ce fichier n’utilise aucune ligne de fin UNIX.',
	'NO_XSL_FILE'		=> 'Le fichier XSL est introuvable alors que celui-ci est obligatoire afin d’afficher le fichier XML dans un navigateur.',

	'USAGE_MYSQL'		=> 'Tu utilises une fonction MySQL modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_MYSQLI'		=> 'Tu utilises une fonction MySQLi modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_OCI'			=> 'Tu utilises une fonction OCI (Oracle) modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_SQLITE'		=> 'Tu utilises une fonction SQLite modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_PG'			=> 'Tu utilises une fonction PostgreSQL modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_MSSQL'		=> 'Tu utilises une fonction MSSQL modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_ODBC'		=> 'Tu utilises une fonction ODBC modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_SQLSRV'		=> 'Tu utilises une fonction SQLSRV (MSSQL) modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_IBASE'		=> 'Tu utilises une fonction iBase (Interbase/Firebird) modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',
	'USAGE_DB2'			=> 'Tu utilises une fonction DB2 modifiée à la ligne %s : %sLes MODs de phpBB sont obligatoires afin d’utiliser le “Database Abstraction Layer” (DBAL).',

	'USAGE_GET'			=> 'Tu utilises $_GET à la ligne %s : %srequest_var() devrait être utilisé à la place.',
	'USAGE_POST'		=> 'Tu utilises $_POST à la ligne %s : %srequest_var() devrait être utilisé à la place.',
	'USAGE_COOKIE'		=> 'Tu utilises $_COOKIE à la ligne %s : %srequest_var() devrait être utilisé en utilisant le quatrième paramètre.',
	'USAGE_SERVER'		=> 'Tu utilises $_SERVER à la ligne %s : %s[b]$_SERVER [u]EST[/u] une saisie qui doit être réalisée par l’utilisateur ![/b]',
	'USAGE_SESSION'		=> 'Tu utilises $_SESSION à la ligne %s : %sLe système de session de phpBB devrait être utilisé à la place.',
	'USAGE_REQUEST'		=> 'Tu utilises $_REQUEST à la ligne %s : %srequest_var devrait être utilisé à la place.',
	'USAGE_ENV'			=> 'Tu utilises $_ENV à la ligne %s : %s',
	'USAGE_FILES'		=> 'Tu utilises $_FILES à la ligne %s : %sLes fonctions de transfert inclues dans phpBB devraient être utilisées à la place.',
	'USAGE_GLOBALS'		=> 'Tu utilises $GLOBALS à la ligne %s : %s',

	'USAGE_PRINT'		=> 'Tu utilises print() à la ligne %s : %s Le système de template de phpBB devrait être utilisé à la place.',
	'USAGE_PRINTF'		=> 'Tu utilises printf() à la ligne %s : %s Le système de template de phpBB devrait être utilisé à la place.',
	'USAGE_ECHO'		=> 'Tu utilises echo() à la ligne %s : %s Le système de template de phpBB devrait être utilisé à la place.',
	'USAGE_PRINTR' 		=> 'Tu utilises printr() à la ligne %s : %s Le système de template de phpBB devrait être utilisé à la place.',

	'USAGE_`'				=> 'Tu utilises backticks à la ligne %s : %s',
	'USAGE_EVAL'			=> 'Tu utilises eval() à la ligne %s : %s',
	'USAGE_EXEC'			=> 'Tu utilises exec() à la ligne %s : %s',
	'USAGE_SYSTEM'			=> 'Tu utilises system() à la ligne %s : %s',
	'USAGE_PASSTHRU'		=> 'Tu utilises passthru() à la ligne %s : %s',
	'USAGE_GETENV'			=> 'Tu utilises getenv() à la ligne %s : %s',
	'USAGE_DIE'				=> 'Tu utilises die() à la ligne %s : %s',
	'USAGE_MD5'				=> 'Tu utilises md5() à la ligne %s : %sMD5 ne devrait pas être utilisé pour tout ce qui concerne les mots de passe. Les autres utilisations de MD5 seront probablement valides.',
	'USAGE_SHA1'			=> 'Tu utilises sha1() à la ligne %s : %s',
	'USAGE_ADDSLASHES'		=> 'Tu utilises addslashes() à la ligne %s : %s',
	'USAGE_STRIPSLASHES'	=> 'Tu utilises stripslashes() à la ligne %s : %s',
	'USAGE_INCLUDEONCE'		=> 'Tu utilises include_once() à la ligne %s : %sIl est préférable que tu utilises include avec function/class_exists check plutôt qu’avec include/require _once',
	'USAGE_REQUIREONCE'		=> 'Tu utilises require_once() à la ligne %s : %sIl est préférable que tu utilises include avec function/class_exists check plutôt qu’avec include/require _once',
	'USAGE_VARDUMP'			=> 'Tu utilises var_dump à la ligne %s : %s',

	'USAGE_BOM'				=> 'Ton fichier est encodé en UTF-8 avec BOM',

	'USAGE_REQUEST_VAR_INT'	=> 'Une liaison à request_var() incante un entier à une chaîne à la ligne %s : %s',

	'UNWANTED_FILE'			=> 'Ton archive contient un fichier indésirable nommé "%2$s" et situé dans %1$s',

	'INCLUDE_NO_ROOT'		=> 'Une liaison à “include” ou à “require” est manquante à $phpbb_root_path à la ligne %s : %s',
	'INCLUDE_NO_PHP'		=> 'Une liaison à “include” ou à “require” est manquante à $phpEx à la ligne %s : %s',

	'PACKAGE_NOT_EXISTS'	=> 'Le fichier sélectionné (%s) n’existe pas',
	'UNABLE_EXTRACT_PHP'	=> 'Impossible d’extraire %s en utilisant la méthode PHP.',
	'UNABLE_OPEN_PHP'	=> 'Impossible d’ouvrir %s en utilisant la méthode PHP.',

	'LINK_NOT_EXISTS'		=> 'Le(s) fichier(s) pour lier %s n’existe(nt) pas dans le fichier compressé.',

	'NO_IN_PHPBB'			=> 'Une définition pour IN_PHPBB est manquante ou il n’y a aucune vérification pour savoir si IN_PHPBB est paramétré.',
	'FILE_EMPTY'			=> 'Ce fichier PHP a été détecté comme étant vide.',

	'COPY_BASENAME_DIFFER'	=> 'Les noms de base de la commande de copie diffèrent : de %s à %s. Les deux apparaissent comme étant identiques',

	'USING_MODX_OUTDATED'	=> 'Tu utilises la version %s de MODX alors que la dernière version de MODX est la %s. Met à jour ton fichier MODX vers la dernière version.',
	'USING_MODX_UNKNOWN'  => 'Une version incorrecte de MODX a été trouvée concernant le fichier XML. Il est impossible de continuer la prévalidation de ce fichier.',

	'PROSILVER_NO_MAIN_MODX'	=> 'Les modifications du style prosilver devraient être inclues dans le fichier principal de MODX et non dans %s.',
	'ENGLISH_NO_MAIN_MODX'		=> 'Les modifications des fichiers de langue anglais devraient être inclues dans le fichier principal de MODX et non dans %s.',

	'MPV_XML_ERROR'			=> 'Une erreur XML est survenue dans le fichier MODX %s',

	'USAGE_BR_NON_CLOSED'	=> 'Une balise BR n’a pas été fermée correctement et rend invalide le fichier au niveau XHTML. L’erreur est localisée à la ligne %s : %s',
	'USAGE_IMG_NON_CLOSED'	=> 'Une balise IMG n’a pas été fermée correctement et rend invalide le fichier au niveau XHTML. L’erreur est localisée à la ligne %s : %s',

	'FILE_NON_BINARY'		=> 'Le fichier a été détecté comme étant non binaire alors que l’extension est quant à elle binaire. Vérifie le code PHP par mesure de sécurité.',
	
	'GENERAL_NOTICE'  => 'Sache que toutes les vérifications ont été réalisées par un outil automatique. Dans [u]certains[/u] cas, un échec ou un avertissement peut être considéré comme étant correct et fonctionnel.',
	
	'UNABLE_OPEN' => 'Impossible d’ouvrir %s.',
	'UNABLE_WRITE'=> 'Impossible d’écrire sur %s.',
	'PHP_ERROR'   => 'Une erreur PHP a été trouvée : [code]%s[/code]',
	'NO_PRE_VAL_ERRORS'	=> 'Aucun problème de prévalidation n’a été trouvé',
	'REPORT_BY'		=> 'Rapport réalisé par MPV',
	'MPV_SERVER'	=> 'Serveur MPV',
	'UNKNOWN_OUTPUT'	=> 'Type de sortie inconnu',
	'MPV_NOTICE'		=> 'Avis MPV trouvé sur %1$s à la ligne %2$s : %3$s',
	'MPV_WARNING'		=> 'Avertissement MPV trouvé sur %1$s à la ligne %2$s : %3$s',
	'MPV_NOTICE'		=> 'Avis MPV trouvé sur %1$s à la ligne %2$s : %3$s',
	'MPV_USER_NOTICE'		=> 'Avis utilisateur MPV trouvé sur %1$s à la ligne %2$s : %3$s',
	'MPV_GENERAL_ERROR'		=> 'MPV a rencontré une erreur sur %1$s à la ligne %2$s : %3$s',
	'MPV_FAIL_RESULT'			=> 'ÉCHEC',
	'MPV_NOTICE_RESULT'			=> 'AVIS',
	'MPV_WARNING_RESULT'		=> 'AVERTISSEMENT',
	'MPV_INFO_RESULT'			=> 'INFORMATION',
	'INVALID_TYPE'			=> '$type invalide concernant cette fonction !',
	'MOD_NAME'				=> 'Nom du MOD',
	'SUBMIT_COUNT'			=> 'Compteur d’envois',
	'TAG_DATA'				=> 'Tag de données',
	'DATA_COUNT'			=> 'Compteur de données',

	'NO_UMIL_VERSION'		=> 'Impossible de trouver UMIL_VERSION dans umil.php',
	'UMIL_OUTDATED'			=> 'L’UMIL fourni est obsolète.',
	'INCORRECT_UMIL_MD5'		=> 'L’UMIL fourni est modifié.',
	'UNKNOWN_VERSION_UMIL'		=> 'La version d’UMIL fournie est inconnue. Cela peut signifier que la version est obsolète.',

));