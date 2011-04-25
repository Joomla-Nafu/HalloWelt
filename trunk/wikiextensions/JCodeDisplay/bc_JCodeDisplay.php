<?php
/**
* @version $Id$
* @package
* @subpackage
* @author EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
* @author Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
* @author Created on 09.02.2009
*
* Install:
* Add
* require_once( "$IP/extensions/JCodeDisplay/JCodeDisplay.php" );
* to your LocaleSettings.php
*
* Usage:
* <jcodedisplay>Class/Function</jcodedisplay>
*
* Example:
* <jcodedisplay>JLog/addEntry</jcodedisplay>
*
* Required:
* Have Fun =;)
*/

//--No direct access
defined('MEDIAWIKI') or die('=;)');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

//Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) )
{
$wgHooks['ParserFirstCallInit'][] = 'wfJCodeDisplay';
}
else
{
// Otherwise do things the old fashioned way
$wgExtensionFunctions[] = 'wfJCodeDisplay';
}

$wgExtensionCredits['parserhook'][] = array(
'version' => '1.0',
'name' => 'JCodeDisplay',
'author' => array('Nikolai Plath'),
'email' => 'nik@',
'url' => 'http://www.mediawiki.org/wiki/Extension:JCodeDisplay...soon',
'description' => 'Interface for documenting Joomla! classes',
);

function wfJCodeDisplay()
{
global $wgParser, $wgHooks;
$wgParser->setHook( 'jcodedisplay', 'renderJCodeDisplay' );
$wgHooks['OutputPageParserOutput'][] = 'fnJCodeDisplayOutputHook';

return true;
}//function

function renderJCodeDisplay($input, $argv, &$parser)
{
defined('NL') or define('NL', "\n");

//--Version to use
/** @todo selectable J! version - see below..*/
$JVersion = '1.5.11';

// if( ! strpos($input, '#') )
// {
// return 'missing # in path';
// }
//
// list( $path, $classAndMethod ) = explode('#', $input);
//
// if( ! strpos($classAndMethod, '/') )
// {
// return 'missing / in class/method';
// }

list( $class, $method ) = explode('/', $input);

if( ! $class | ! $method )
{
return 'invalid call';
}

// $pathParts = explode('/', $path);
//
// if( count($pathParts) < 2 )
// {
// return 'invalid path 0';
// }

//--Let's find the path...
# $baseDir = str_replace('/extensions/JCodeDisplay', '', dirname(__FILE__));
global $IP;
$baseDir = $IP;

// global $wgScriptPath;
// $baseDir = substr($b, 0, strpos($b, $wgScriptPath)+strlen($wgScriptPath));

$JVersionRoot = $baseDir.DS.'sources'.DS.'joomla';

//--Draw a selectbox for J! version
//-- @todo hard to include in media wiki - excessive caching kills the cat..
$selector = '';
if(false)
{
$folders = EasyFolder::folders($JVersionRoot);
$selector .= '<form action="'.$_SERVER['PHP_SELF'].'?action=purge" method="post">';
$selector .= '<select name="jcd_version" onchange="this.form.submit();">';

$selVersion =( isset($_REQUEST['jcd_version'])) ? ($_REQUEST['jcd_version']) : 0;
$i = 1;
foreach ($folders as $v)
{
$selected = '';
if( $i == $selVersion )
{
$JVersion = $v;
$selected = ' selected="selected"';
}
$selector .= '<option value="'.$i.'"'.$selected.'>'.$v.'</option>';
$i ++;

}//foreach

$selector .= '</select>';
$selector .= '</form>';
}

$s = str_replace('.', '_', $JVersion);
$fName = 'jmethodlist_'.$s.'.txt';
if( ! file_exists($baseDir.DS.'sources'.DS.'joomla'.DS.$fName))
{
return 'method list not found '.$baseDir.DS.'sources'.DS.'joomla'.DS.$fName;
}
$methodList = file($baseDir.DS.'sources'.DS.'joomla'.DS.$fName);
# print_r($methodList);

$baseDir .= DS.'sources'.DS.'joomla'.DS.$JVersion.DS.'libraries';
$rootDir = $baseDir;

if( ! is_dir($baseDir))
{
return 'Joomla! sources not found in /sources'.DS.'joomla'.DS.$JVersion;
}


foreach ($methodList as $line)
{
$line = trim($line);
if( strpos($line, $class.'#'.$method ) !== 0 )
{
continue;
}
list($c, $m, $path, $start, $end) = explode('#', $line);
$fullPath = $baseDir.DS.'joomla'.DS.$path;
if( ! file_exists($fullPath))
{
return 'file not found ';//.$fullPath;
}
//--Read the file contents
$lines = file($fullPath);

$code = '';

//--leading whitespace
$remove = null;

$cLines = array();
for ($i = $start-1; $i < $end; $i++)
{
if( $remove === null )
{
$remove = substr($lines[$i], 0, strpos($lines[$i], 'function'));
}

$l = $lines[$i];

//--Strip leading whitespace
$r = strlen($l);
if( strlen($l)>1 )
{
$l = substr($l, strlen($remove));
}

//--Convert tabs to three spaces
$l = str_replace("\t", '   ', $l);

$code .= $l;
}

if( class_exists('GeSHI'))
{
$geshi = new GeSHi($code, 'php');
$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
$geshi->start_line_numbers_at($start);
// $geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');
$parsedCode = $geshi->parse_code();
}
else
{
$parsedCode = '<pre>'.$code.'</pre>';
}


$pathTree = '{{folder|libraries/joomla}}'.NL;

$pathParts = explode('/', $path);

$cc = 1;
$colours = array('', '|red', '|green', '|yellow');
foreach ($pathParts as $part)
{
if( strpos($part, '.') )
{
//--Seems to be a file
$pathTree .= str_repeat('*', $cc).'{{file|'.$part.'|php}}'.NL;
continue;
}
$color = '';
$pathTree .= str_repeat('*', $cc).'{{folder|'.$part.$colours[$cc].'}}'.NL;
$cc ++;
}//foreach

$head = '';
$head .= "'''".$class.' -> '.$method."'''".' in Joomla! '.$JVersion.NL.NL;
$head .= $pathTree;
$head = $parser->recursiveTagParse($head);

# return $selector.$head.$parsedCode;

return $head.$parsedCode;
}//foreach

return 'NOT FOUND';
}//function

/**
* Entry point for the hook for printing JS and CSS:
*/
function fnJCodeDisplayOutputHook( &$m_pageObj, $m_parserOutput ) {
global $wgScriptPath;

//--CSS
// $m_pageObj->addLink(
// array(
// 'rel' => 'stylesheet',
// 'type' => 'text/css',
// 'href' => $wgScriptPath . '/extensions/JCodeDisplay/JCodeDisplay.css'
// )
// );

//--JS
# $m_pageObj->addScriptFile($wgScriptPath.DS.'extensions'.DS.'PermCalc'.DS.'PermCalc.js');

# Be nice:
return true;
}//function

abstract class EasyFolder
{
/**
* Wrapper for the standard file_exists function
*
* @param string $path Folder name relative to installation dir
* @return boolean True if path is a folder
* @since 1.5
*/
function exists($path)
{
return is_dir(self::clean($path));
}

/**
* Utility function to read the folders in a folder
*
* @param string $path The path of the folder to read
* @param string $filter A filter for folder names
* @param mixed $recurse True to recursively search into sub-folders, or an integer to specify the maximum depth
* @param boolean $fullpath True to return the full path to the folders
* @param array $exclude Array with names of folders which should not be shown in the result
* @return array Folders in the given folder
* @since 1.5
*/
public function folders($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
{
// Initialize variables
$arr = array ();

// Check to make sure the path valid and clean
$path = self::clean($path);

// Is the path a folder?
if (!is_dir($path)) {
echo 'JFolder::folder: Path is not a folder '.$path;
return false;
}

// read the source directory
$handle = opendir($path);
while (($file = readdir($handle)) !== false)
{
$dir = $path.DS.$file;
$isDir = is_dir($dir);
if (($file != '.') && ($file != '..') && (!in_array($file, $exclude)) && $isDir) {
// removes SVN directores from list
if (preg_match("/$filter/", $file)) {
if ($fullpath) {
$arr[] = $dir;
} else {
$arr[] = $file;
}
}
if ($recurse) {
if (is_integer($recurse)) {
$recurse--;
}
$arr2 = EasyFolder::folders($dir, $filter, $recurse, $fullpath);
$arr = array_merge($arr, $arr2);
}
}
}
closedir($handle);

asort($arr);
return $arr;
}
/**
* Utility function to read the files in a folder
*
* @param string $path The path of the folder to read
* @param string $filter A filter for file names
* @param mixed $recurse True to recursively search into sub-folders, or an integer to specify the maximum depth
* @param boolean $fullpath True to return the full path to the file
* @param array $exclude Array with names of files which should not be shown in the result
* @return array Files in the given folder
* @since 1.5
*/
function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
{
// Initialize variables
$arr = array ();

// Check to make sure the path valid and clean
$path = self::clean($path);

// Is the path a folder?
if (!is_dir($path)) {
echo 'JFolder::files: Path is not a folder '.$path;
return false;
}

// read the source directory
$handle = opendir($path);
while (($file = readdir($handle)) !== false)
{
$dir = $path.DS.$file;
$isDir = is_dir($dir);
if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
if ($isDir) {
if ($recurse) {
if (is_integer($recurse)) {
$recurse--;
}
$arr2 = EasyFolder::files($dir, $filter, $recurse, $fullpath);
$arr = array_merge($arr, $arr2);
}
} else {
if (preg_match("/$filter/", $file)) {
if ($fullpath) {
$arr[] = $path.DS.$file;
} else {
$arr[] = $file;
}
}
}
}
}
closedir($handle);

asort($arr);
return $arr;
}
/**
* Function to strip additional / or \ in a path name
*
* @static
* @param string $path The path to clean
* @param string $ds Directory separator (optional)
* @return string The cleaned path
* @since 1.5
*/
function clean($path, $ds=DS)
{
$path = trim($path);

if (empty($path)) {
$path = '';
} else {
// Remove double slashes and backslahses and convert all slashes and backslashes to DS
$path = preg_replace('#[/\\\\]+#', $ds, $path);
}

return $path;
}//function



}//class

/**
* HELPERS
* these classes / functions must be defined to include J classes - we will provide the functionality..
*/
function jimport( $path )
{
return true;
# return JLoader::import($path);
}

class JLoader
{
function register()
{
;
}//function

}//class