<?php
/**
 * @version $Id$
 *
 * @description scans the directory /libraries/joomla/* and lists all the foud methods whith path and line numbers
 *
 * This file must be placed in joomla root directory.
 * It then scans the directories under /libraries/joomla/ and writes the output to a text file called
 * jmethodlist_JVERSION_.txt
 */

//--DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 2);
define('DEBUG', 1);

$reqJVER =( isset($_REQUEST['jver'])) ? $_REQUEST['jver'] : '';
$action =( isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';

if( ! $reqJVER ) { echo 'NO VERSION GIVEN'; return; }

define('DS', DIRECTORY_SEPARATOR );
define('BR', '<br />');
define('NL', "\n");

if( ! is_dir(dirname(__FILE__).DS.'joomla'.DS.$reqJVER))
{
    #   echo dirname(__FILE__).DS.$reqJVER;
    echo 'VERSION NOT FOUND';//.$reqJVER;
    return;
}

$basePath = dirname(__FILE__).DS.'joomla'.DS.$reqJVER.DS.'libraries'.DS.'joomla';

define('JPATH_SITE', '');
define('JPATH_ROOT', '');
define('_JEXEC', '');

define('JPATH_BASE', '');

define('JPATH_LIBRARIES', dirname(__FILE__).DS.'joomla'.DS.$reqJVER.DS.'libraries');

tryInc('joomla'.DS.'base'.DS.'object.php');
tryInc('joomla'.DS.'base'.DS.'observer.php');
tryInc('joomla'.DS.'base'.DS.'observable.php');
tryInc('joomla'.DS.'event'.DS.'event.php');
tryInc('joomla'.DS.'document'.DS.'renderer.php');
tryInc('joomla'.DS.'registry'.DS.'registry.php');
tryInc('joomla'.DS.'version.php');
tryInc('joomla'.DS.'environment'.DS.'request.php');

if( substr($reqJVER, 0, 3) == '1.5')
{
    //-- J! 1.5
    tryInc('phpmailer'.DS.'phpmailer.php');
    tryInc('pattemplate'.DS.'patTemplate'.DS.'Module.php');
    tryInc('pattemplate'.DS.'patTemplate'.DS.'Function.php');
    tryInc('pattemplate'.DS.'patTemplate'.DS.'Modifier.php');
    tryInc('pattemplate'.DS.'patTemplate.php');
    tryInc('phpgacl'.DS.'gacl.php');
    tryInc('phpgacl'.DS.'gacl_api.php');
}
else
{
    //-- J! 1.6
    tryInc('joomla'.DS.'application'.DS.'component'.DS.'model.php');
    tryInc('joomla'.DS.'database'.DS.'table.php');
    tryInc('joomla'.DS.'database'.DS.'tablenested.php');
    tryInc('joomla'.DS.'form'.DS.'formfield.php');
    tryInc('joomla'.DS.'form'.DS.'fields'.DS.'text.php');
    tryInc('joomla'.DS.'form'.DS.'fields'.DS.'list.php');
     tryInc('joomla'.DS.'form'.DS.'fields'.DS.'mediamanager.php');
  tryInc('joomla'.DS.'base'.DS.'adapterinstance.php');
    tryInc('joomla'.DS.'base'.DS.'node.php');
    tryInc('joomla'.DS.'updater'.DS.'updateadapter.php');

    tryInc('phpmailer'.DS.'phpmailer.php');

}


$folders = EasyFolder::folders($basePath);
array_unshift($folders, '');//add 'base folder'
$folders[] = 'xxxxx';//for custom class list

$JMethods = array();

$classes = array();

$packages = array();

$extenders = array();

for ($i = 0; $i < count($folders); $i++)
{
    #var_dump($files);
    if( $folders[$i] == 'xxxxx' )
    {
        $files = array('xxxxx');
    }
    else
    {
        $p = ($folders[$i]) ? $basePath.DS.$folders[$i] : $basePath;
        if( $folders[$i])
        {
            $files = EasyFolder::files($p, 'php', true, true, $basePath);

        }
        else
        {
            $files = EasyFolder::files($p, 'php', false, true, $basePath);
        }
    }
    foreach ($files as $file)
    {
        echo(DEBUG==2)?'<hr />FILE: '.str_replace($basePath.DS, '', $file).BR:'';
        if($file == 'xxxxx')
        {
            //--custom class list - files included before
            $foundClasses = array('JObject', 'JObservable', 'JObserver', 'JDocumentRenderer', 'JRequest', 'JEvent', 'JRegistry', 'JVersion');//, 'JController', 'JComponentHelper', 'JModel');
            if( version_compare($reqJVER, '1.6.a', '<'))//, '<'))
            {
                //-- J! 1.5
            }
            else
            {
                //-- J! 1.6
                $foundClasses = array_merge($foundClasses, array('JModel'));
            }
        }
        else
        {
            $allClasses = get_declared_classes();
            if( ! $allClasses )
            {
                $allClasses = array();
            }
            if( strpos($file, 'import.php')){; continue;}
            include_once $file;
            $foundClasses = array_diff(get_declared_classes(), $allClasses);

            //--Exeptions from the rules..
            if( ! count($foundClasses))
            {
                $fileName = $file;
                if($file == 'methods.php')
                {
                    $foundClasses = array('JRoute', 'JText');
                }

                if( ! count($foundClasses))
                {
                    echo(DEBUG==2)? sprintf('<h3>no classes found in -- %s</h3>',str_replace($basePath.DS, '', $file)):'';
                }
            }
        }

        foreach ($foundClasses as $c)
        {
            $cl = new stdClass();
            echo(DEBUG==2)? '<span style="background-color: yellow;">CLASS: '.$c.'</span>'.BR:'';

            $class = new ReflectionClass($c);

            if( $parent = $class->getParentClass() )
            {
                $extenders[$parent->name][] = $c;
                //			    if( array_key_exists($parent->name, $extenders))
                //			    {
                //            $parentName =( $parent ) ? ' extends <span style="color: orange;">'.$parent->name.'</span>':'';
                //
                //			    }
                //			    else
                //			    {
                    //			        $extenders[$parent->name][] = $c;
                    //			    }
            }

            $cl->comment = $class->getDocComment();
            $comment = explode(NL, $cl->comment);
            $searches = array('static', 'subpackage', 'since');
            $subPackage = '';
            foreach ($comment as $co)
            {
                foreach ($searches as $search)
                {
                    if( strpos($co, '@'.$search))
                    {
                        if( $search == 'subpackage')
                        {
                            $p =  strpos($co, $search);
                            $subPackage = trim(substr($co, strpos($co, $search)+strlen($search)));
                            if( ! in_array($subPackage, $packages))
                            {
                                $packages[] = $subPackage;
                            }
                        }
                    }
                }//foreach
            }//foreach

            #			$subPackage =( $subPackage ) ? $subPackage : $sub_package;

            $ms = array();

            $methods = $class->getMethods();
            foreach ($methods as $method)
            {
                $rr = $method->getDeclaringClass();
                if( $method->getDeclaringClass()->getName() != $c ) { continue; }
                if( substr($method->name, 0, 1) == '_'
                && $method->name != '_'
                && substr($method->name, 0, 2) == '__')
                { continue; }

                echo(DEBUG==2)?$method->name.BR:'';

                $m = new stdClass();
                $m->class = $c;
                $m->name = $method->name;
                $s =( $file == 'xxxxx' ) ? $method->getFileName() : $file;
                $m->file = str_replace($basePath.DS, '', $s);
                $m->start = $method->getStartLine();
                $m->end = $method->getEndLine();
                $ms[$method->name] = $m;
                $JMethods[$c][$method->name] = $m;
            }//foreach
            $classes[$c] = new stdClass();
            $classes[$c]->package =($subPackage) ? $subPackage : 'Base';
            $classes[$c]->class = $class;
            $classes[$c]->methods = $ms;
        }//foreach
    }//foreach
}//for

ksort($classes);
#echo JPATH_LIBRARIES;
$ver = new JVersion();
#echo '<pre>';
$re = '';
switch ($action)
{
	case 'methods':

	    $mStrings = array();
foreach( $classes as $c)
{

foreach ($c->methods as $m)
{
    $mStrings[] = $m->class.'#'.$m->name.'#'.$m->file.'#'.$m->start.'#'.$m->end;
}
}

$re = implode(NL, $mStrings);


//        $classList = BR.', [\''.implode('\'], [\'', $superClasses).'\']';
//        $classList .= BR.']';
//        $methodList = '';
//        foreach ($JMethods as $cName => $cMethods)
//        {
//
//          $methodList .= ", '$cName' : [".BR;
//          $methodList .= "['".implode("()'], ['", array_keys($cMethods))."']".BR;
//          $methodList .= ']'.BR;
//        }
//        echo $classList.BR;
//        echo $methodList;
//        echo ']'.BR;
//        echo '<hr />Have FUN <tt>=;)</tt><hr />';
//        #echo '<pre>';
//        $mStrings = array();
//
//        foreach ($JMethods as $m)
//        {
//            $mStrings[] = $m->class.'#'.$m->name.'#'.$m->file.'#'.$m->start.'#'.$m->end;
//        }
//
//        $s = implode(NL, $mStrings);
//        $version = new JVersion();
//        $v = str_replace('.', '_', $version->getShortVersion());
        $fName = 'jmethodlist_'.str_replace('.', '_', $reqJVER).'.txt';
	break;

	default:
        $re .= '<?php'.NL;
        $re .= '/* Class list for Joomla! '.$ver->getShortVersion().' generated on '.date('Y-M-d').' */'.NL;
        $re .= 'function getJoomlaClasses() {'.NL;
        $re .= '$c = array();'.NL;

        foreach ($classes as $cName => $c)
        {
            $p = str_replace(JPATH_LIBRARIES.DS.'joomla'.DS, '', $c->class->getFileName());

            //-- Method list
            $ms = implode("','", array_keys($c->methods));
            #	$re .= '$c'."['$cName']=array('$c->package','$p',array('$ms'));".NL;

            //-- NO Method list
            $re .= '$c'."['$cName']=array('$c->package','$p');".NL;
        }//foreach
        $re .= 'return $c;'.NL;
        $re .= '}'.NL;
        $re .= '/* Package list for Joomla! '.$ver->getShortVersion().' generated on '.date('Y-M-d').' */'.NL;
        $re .= 'function getJoomlaPackages() {'.NL;
        $re .= "return array('".implode("','",$packages)."');".NL;
        $re .= '}'.NL;
        $re .= '/* Extending classes list for Joomla! '.$ver->getShortVersion().' generated on '.date('Y-M-d').' */'.NL;
        $re .= 'function getExtendingClasses() {'.NL;
        foreach ($extenders as $eNa => $eExs)
        {
            $re .= '$e[\''.$eNa.'\']=array(\''.implode("','", $eExs).'\');'.NL;
        }//foreach
        $re .= 'return $e;'.NL;
        $re .= '}'.NL;

        $fName = 'jclasslist_'.str_replace('.', '_', $reqJVER).'.php';
    break;
}//switch

$path = dirname(__FILE__).DS.'joomla'.DS.$fName;

if( file_put_contents($path, $re))
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = dirname($_SERVER['PHP_SELF']);
    $uri = str_replace('sources', '', $uri);
    $uri   = rtrim($uri, '/\\');
    $extra = 'jdoc.php?mlist_built=1&j_version='.$reqJVER;

    $href = "$host$uri/$extra";
    header("Location: http://$href");

}
else
{
    echo '<h1 style="color: red;">WRITE ERROR</h1>';
}

#var_dump($packages);
#echo '<pre>'.print_r($classes, true).'</pre>';
#var_dump($classes);
//-- Format
//$classList = BR.', [\''.implode('\'], [\'', $superClasses).'\']';
//$classList .= BR.']';
//$methodList = '';
//foreach ($JMethods as $cName => $cMethods)
//{
//
//	$methodList .= ", '$cName' : [".BR;
//	$methodList .= "['".implode("()'], ['", array_keys($cMethods))."']".BR;
//	$methodList .= ']'.BR;
//}
//echo $classList.BR;
//echo $methodList;
//echo ']'.BR;
//echo '<hr />Have FUN <tt>=;)</tt><hr />';
//#echo '<pre>';
//$mStrings = array();
//
//foreach ($JMethods as $m)
//{
//#	$mStrings[] = $m->class.'#'.$m->name.'#'.$m->file.'#'.$m->start.'#'.$m->end;
//}
//
//$s = implode(NL, $mStrings);
//$version = new JVersion();
//$v = str_replace('.', '_', $version->getShortVersion());
//$fName = 'jmethodlist_'.$v.'.txt';
#echo $fName.BR;
/*
 if( ! file_put_contents($fName, $s))
 {
 echo '<h1 style="color: red;">Unable to write file </h1>';
 }
 else
 {
 echo '<h1 style="color: green;">Finished <tt>=;)</tt></h1>';
 echo sprintf('Written to: %s',$fName).BR;
 echo sprintf('%s methods found in %s classes', count($JMethods), count($superClasses)).BR;
 }
 */
#$schtring = "'".implode("', '", $superClasses)."'";
#var_dump($superClasses);
#echo $schtring;
######################################
## helpers..
######################################

function tryInc($path)
{
    if( ! file_exists(JPATH_LIBRARIES.DS.$path))
 {
echo(DEBUG==2)?'<hr />FILE not found: '.$path.BR:'';
 return false; }

    include_once (JPATH_LIBRARIES.DS.$path);

    return true;
}
function jimport()
{
    ;
}//function

class JLoader
{
    function register()
    {
        ;
    }//function

    function import()
    {
        ;
    }//function
}//class

class EasyFolder
{
    /**
     * Utility function to read the files in a folder.
     *
     * @param	string	The path of the folder to read.
     * @param	string	A filter for file names.
     * @param	mixed	True to recursively search into sub-folders, or an
     * integer to specify the maximum depth.
     * @param	boolean	True to return the full path to the file.
     * @param	array	Array with names of files which should not be shown in
     * the result.
     * @return	array	Files in the given folder.
     * @since 1.5
     */
    function files($path, $filter = '.', $recurse = false, $fullpath = false, $stripPath = '', $exclude = array('.svn', 'CVS'))
    {
        // Initialize variables
        $arr = array();

        // Check to make sure the path valid and clean
        #		$path = JPath::clean($path);

        // Is the path a folder?
        if (!is_dir($path)) {
            echo 'EasyFolder::files: Path is not a folder: ' . $path;
            return false;
        }

        // read the source directory
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false)
        {
            if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
                $dir = $path . DS . $file;
                $isDir = is_dir($dir);
                if ($isDir) {
                    if ($recurse) {
                        if (is_integer($recurse)) {
                            $arr2 = EasyFolder::files($dir, $filter, $recurse - 1, $fullpath);
                        } else {
                            $arr2 = EasyFolder::files($dir, $filter, $recurse, $fullpath);
                        }

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
    }//function

    /**
     * Utility function to read the folders in a folder.
     *
     * @param	string	The path of the folder to read.
     * @param	string	A filter for folder names.
     * @param	mixed	True to recursively search into sub-folders, or an
     * integer to specify the maximum depth.
     * @param	boolean	True to return the full path to the folders.
     * @param	array	Array with names of folders which should not be shown in
     * the result.
     * @return	array	Folders in the given folder.
     * @since 1.5
     */
    function folders($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
    {
        // Initialize variables
        $arr = array();

        // Check to make sure the path valid and clean
        #		$path = JPath::clean($path);

        // Is the path a folder?
        if (!is_dir($path)) {
            JError::raiseWarning(21, 'JFolder::folder: ' . JText::_('Path is not a folder'), 'Path: ' . $path);
            return false;
        }

        // read the source directory
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false)
        {
            if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
                $dir = $path . DS . $file;
                $isDir = is_dir($dir);
                if ($isDir) {
                    // Removes filtered directories
                    if (preg_match("/$filter/", $file)) {
                        if ($fullpath) {
                            $arr[] = $dir;
                        } else {
                            $arr[] = $file;
                        }
                    }
                    if ($recurse) {
                        if (is_integer($recurse)) {
                            $arr2 = JFolder::folders($dir, $filter, $recurse - 1, $fullpath);
                        } else {
                            $arr2 = JFolder::folders($dir, $filter, $recurse, $fullpath);
                        }

                        $arr = array_merge($arr, $arr2);
                    }
                }
            }
        }
        closedir($handle);

        asort($arr);
        return $arr;
    }//function

}//class
