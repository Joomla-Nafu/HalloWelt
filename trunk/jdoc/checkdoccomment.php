<?php
/**
 * @version $Id$
 * @package
 * @subpackage
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 08-Sep-2009
 */

/**
 *
 * @var boolean true if the script is called via CLI
 */
$CLI =(substr(php_sapi_name(), 0, 3) == 'cli') ? true : false;

error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);

define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__).DS.'sources'.DS.'joomla'.DS.'trunk');
define('JPATH_LIBRARIES', JPATH_BASE.DS.'libraries');

define('JPATH_ROOT', 'XXX');//should not be nessesary
define('JPATH_SITE', 'YYY');//should not be nessesary


define('BR', "<br />\n");
define('HR', "<hr />\n");
define('NL', "\n");

if($CLI)
{
    ob_start();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Errors in Joomla! framework DocComments</title>
<link href="/assets/images/jfavicon_t.ico" rel="shortcut icon" type="image/x-icon" />
<style type="text/css">
div.error {
   background-color: #ffb2b2;
   padding: 0.2em;
}

a img {
   border: 0;
}
</style>
</head>
<body>
<div>
<h2>This checks the Joomla! framework classes for correct <big><tt>@param</tt></big><small>eter</small>
declaration in /** DocBlocs */</h2>
<div style="border: 1px dashed red; padding: 0.5em;">Parameters are
expected as:<br />
<div style="background-color: #ffc; padding: 0.2em; font-size: 1.3em;">
<tt> @param <b>vartype $name Description</b> </tt><br />
e.g.:<br />
<tt> @param <b>boolean $standard Standards are great.</b></tt></div>
Otherwise they will be marked <b style="color: red;">red</b>.</div>
<?php

$JInspector = new JInspector($CLI);

$JInspector->checkDocComments();

?>
<hr />
<p style="float: right;"><a
	href="http://validator.w3.org/check?uri=referer"><img
	src="http://www.w3.org/Icons/valid-xhtml10"
	alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>
<h2 style="color: green;">Finished <tt>=;)</tt></h2>
<p>For more great scripts visit <a href="http://easy-joomla.org">Easy-Joomla.org</a></p>
<em>Generated on <tt><?php echo date('Y-M-d H:i:s'); ?></tt></em>
<hr />
</div>
</body>
</html>
<?php

if($CLI)
{
    $fileName = 'doccommenterrors.html';
    $output = ob_get_clean();
$path = dirname(__FILE__);
    if(JFile::write($path.DS.$fileName, $output))
    {
        echo 'File has been written to '.NL.$path.DS.$fileName.NL;
    }
    else
    {
        echo 'ERROR !! writing file to '.NL.$path.DS.$fileName.NL;
    }
}

//---------FINISHED here..

//--- Fake J! classes and functions

function jimport(){}

class JLoader {
    function register() {}
    function import() {}
}

class PHPMailer {}
class patTemplate {}
class patTemplate_Function {}
class patTemplate_Modifier {}
class gacl_api {}

/**
 *
 * @author elkuku
 *
 */
class JInspector
{

    /**
     *
     * @var array
     */
    private $baseClasses = array();

    /**
     *
     * @var boolean
     */
    private $CLI = false;

    /**
     *
     * @var JVersion
     */
    private $JVersion = null;

    private $showLineNumbers = true;

    /**
     * Constructor
     *
     * @param boolean $CLI
     */
    public function __construct($CLI = false)
    {
        $this->CLI = $CLI;

        require_once JPATH_LIBRARIES.DS.'joomla'.DS.'version.php';
        $this->JVersion = new JVersion();
    }//function

    public function checkDocComments()
    {
        echo $this->JVersion->getLongVersion().BR;
        echo 'CHANGELOG: <tt>@version $Id: '.$this->getVersionFromCHANGELOG().'</tt>'.BR;
        echo HR.'Starting...';

        $this->baseClasses['JObject'] = 'base/object.php';
        $this->baseClasses['JDocumentRenderer'] = 'document/renderer.php';
        $this->baseClasses['JRegistry'] = 'registry/registry.php';
        $this->baseClasses['JVersion'] = 'version.php';

        if($this->JVersion->RELEASE == '1.6')
        {
            $this->baseClasses['JModel'] = 'application/component/model.php';
            $this->baseClasses['JTable'] = 'database/table.php';
            $this->baseClasses['JTableNested'] = 'database/tablenested.php';
            $this->baseClasses['JFormField'] = 'form/formfield.php';
            #            $baseClasses['JFormFieldText'] = 'form/fields/text.php';
            $this->baseClasses['JFormFieldList'] = 'form/fields/list.php';
            #    $baseClasses['JFormFieldList'] = 'form/fields/list.php';
            #    $baseClasses['JFormFieldList'] = 'form/fields/mediamanager.php';
            $this->baseClasses['JAdapterInstance'] = 'base/adapterinstance.php';
            $this->baseClasses['JUpdateAdapter'] = 'updater/updateadapter.php';

            $this->baseClasses['JNode'] = 'base/node.php';
        }

        $files = self::readFiles(JPATH_LIBRARIES.DS.'joomla', 'php', true, true);

        printf('found %d files.', count($files)).BR;

        $allClasses = get_declared_classes();

        //-- 'Including some "Base" classes which will be skipped later !';
        foreach ($this->baseClasses as $name => $path)
        {
            if($name == $path) continue;

            echo HR;
            echo $path.' ...';

            if($path == 'version.php')
            {
                $this->inspectClasses(array('JVersion'));
                continue;
            }

            include JPATH_LIBRARIES.DS.'joomla'.DS.$path ;
#            echo '...OK';

            $foundClasses = array_diff(get_declared_classes(), $allClasses);

            if( ! $foundClasses)
            {
                echo 'NOTHING FOUND !!!!'.BR;
                continue;
            }

            printf('found <b>%d</b> class(es): ', count($foundClasses));
            echo '<b>'.implode('</b>, <b>', $foundClasses).'</b>...ckecking...';

            $allClasses = array_merge($foundClasses, $allClasses);

            $this->inspectClasses($foundClasses);
        }//foreach

        $skippings = array(
    		'import.php'
    		, 'form/fields/list.php'
    		, 'html/parameter/element/list.php'
    		);

    		//-- The rest
    		foreach ($files as $file)
    		{
    		    echo HR;

    		    $s = str_replace(JPATH_LIBRARIES.DS.'joomla'.DS, '', $file);
    		    echo $s.'... ';

    		    if(in_array($s, $skippings)) { echo 'skipping'; continue; }
    		    if(in_array($s, $this->baseClasses)) {  echo 'skipping'; continue; }

    		    include($file);

    		    $foundClasses = array_diff(get_declared_classes(), $allClasses);

    		    if( ! $foundClasses)
    		    {
    		        echo 'NOTHING FOUND !!!!'.BR.NL;
    		        continue;
    		    }

    		    printf('found <b>%d</b> class(es): ', count($foundClasses));
    		    echo '<b>'.implode('</b>, <b>', $foundClasses).'</b>...checking...';

    		    $allClasses = array_merge($foundClasses, $allClasses);
    		    $this->inspectClasses($foundClasses);

    		}//foreach
    }//function

    /**
     *
     * @param array $classes
     */
    private function inspectClasses(array $classes)
    {
        foreach ($classes as $class)
        {
            $errors = false;

            $theClass = new ReflectionClass($class);

            $methods =  $theClass->getMethods();

            $mErrors = array();

            foreach ($methods as $method)
            {
                $declaringClass= $method->getDeclaringClass()->getName();

                if(strtolower($declaringClass) != strtolower($theClass->getName()))
                {
                    //-- method comes from extended class
                    continue;
                }

                $parameters = $method->getParameters();

                if( ! $comment = $method->getDocComment())
                {
                    $s = '<b style="color: red">NO METHOD DOCCOMMENT !!</b>';
                    $s .=($this->showLineNumbers) ? ' <tt>#'.$method->getStartLine().'</tt>' : '';
                    $mErrors[$method->getName()] = $s;
                }
                else
                {
                    $searches = array('@return', '@since', '@static', '@param');

                    $docComOptions = $this->parseDocComment($method->getDocComment(), $searches);

                    $n = array();

                    foreach ($parameters as $param)
                    {
                        if( ! array_key_exists($param->name, $docComOptions->params))
                        {
                            $n[] = $param->name;
                        }
                    }//foreach

                    if(count($docComOptions->params) > count($parameters))
                    {
                        foreach (array_keys($docComOptions->params) as $name)
                        {
                            $found = false;

                            foreach ($parameters as $param)
                            {
                                if($param->name == $name)
                                {
                                    $found = true;
                                    break;
                                }
                            }//foreach

                            if( ! $found)
                            {
                                $n1[] = $name;
                            }
                        }//foreach

                        $s = 'Parameter overkill: <b style="color: yellow;">'.implode('</b>, <b style="color: red;">', $n1).'</b>';
                        $s .=($this->showLineNumbers) ? ' <tt>#'.$method->getStartLine().'</tt>' : '';

                        $mErrors[$method->getName()] = $s;
                        $errors = true;
                    }

                    if(count($n))
                    {
                        $s = '<b style="color: red;">'.implode('</b>, <b style="color: red;">', $n).'</b>';
                        $s .=($this->showLineNumbers) ? ' <tt>#'.$method->getStartLine().'</tt>' : '';

                        $mErrors[$method->getName()] = $s;
                        $errors = true;
                    }
                }

            }//foreach

            $classComment = explode("\n", $theClass->getDocComment());

            if( ! $classComment)
            {
                $errors = true;
                echo '<div class="error">No class DocComment !!!</div>';
            }

            if(count($mErrors))
            {
                echo BR.'<b style="color: blue;">'.$theClass->getName().'</b>';

                if(count($mErrors))
                {
                    foreach ($mErrors as $title => $text)
                    {
                        echo BR.'<b style="color: orange;">'.$title.'</b> - '.$text;
                    }//foreach
                }
            }
            else
            {
                echo '<b style="color: green;"> No errors</b>';
            }

        }//foreach classes

    }//function

    /**
     *
     * @param string $docComment
     * @param array $searchFor
     */
    private function parseDocComment($docComment, array $searchFor)
    {
        $DComm = new stdClass();
        $DComm->isStatic = false;
        $DComm->since = '';
        $DComm->return = '';
        $DComm->params = array();

        $comment = explode("\n", $docComment);

        foreach($comment as $c)
        {
            if( ! strpos($c, '@')) continue;

            foreach ($searchFor as $search)
            {
                if(strpos($c, $search))
                {
                    switch ($search)
                    {
                        case '@static':
                            $DComm->isStatic = true;
                            break;

                        case '@return':
                            preg_match ('/@return[\s\t](.*?\w)[\s\t](.+)/', $c, $matches);
                            $DComm->return = $c;

                            if( isset($matches[1]))
                            {
                                $DComm->return = str_replace($matches[1], '{{mark|'.$matches[1].'}}', $DComm->return);
                            }

                            $DComm->return = trim(str_replace('@return', "'''@return'''", $DComm->return));
                            break;

                        case '@since':
                            $DComm->since = trim(str_replace('@since', "'''@since'''", $c));
                            $DComm->since = str_replace('1.5', '{{JVer|1.5}}', $DComm->since);
                            $DComm->since = str_replace('1.6', '{{JVer|1.6}}', $DComm->since);
                            break;

                        case '@param':
                            preg_match ('/@param[\s\t](.+)[\s\t]\$(.*?\w)[\s\t](.+)/', $c, $matches);

                            if( isset($matches[1]) && isset($matches[2]) && isset($matches[3]))
                            {
                                $DComm->params[trim($matches[2])] = array(
                              'type'=>trim($matches[1])
                                , 'name'=>trim($matches[2])
                                , 'desc'=>trim($matches[3])
                                );
                            }
                            break;
                    }//switch
                }
            }//foreach
        }//foreach

        return $DComm;
    }//function

    /**
     * @takenfrom JFolder::files()
     *
     * Utility function to read the files in a folder.
     *
     * @param   string  The path of the folder to read.
     * @param   string  A filter for file names.
     * @param   mixed   True to recursively search into sub-folders, or an
     * integer to specify the maximum depth.
     * @param   boolean True to return the full path to the file.
     * @param   array   Array with names of files which should not be shown in
     * the result.
     * @return  array   Files in the given folder.
     * @since 1.5
     */
    private static function readFiles($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
    {
        // Initialize variables
        $arr = array();

        // Check to make sure the path valid and clean
        #      $path = JPath::clean($path);

        // Is the path a folder?
        if (!is_dir($path)) {
            echo 'readFiles() Path is not a folder Path: '.$path;
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
                            $arr2 = self::readFiles($dir, $filter, $recurse - 1, $fullpath);
                        } else {
                            $arr2 = self::readFiles($dir, $filter, $recurse, $fullpath);
                        }

                        $arr = array_merge($arr, $arr2);
                    }
                } else {
                    if (preg_match("/$filter/", $file)) {
                        if ($fullpath) {
                            $arr[] = $path . DS . $file;
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
     * Extract strings from svn:property Id
     *
     * @param bool $revOnly true to return revision number only
     * @return string/bol propertystring or FALSE
     * like:
     * @ version $I d: CHANGELOG.php 362 2007-12-14 22:22:19Z elkuku $
     * [0] => Id: [1] => CHANGELOG.php [2] => 362 [3] => 2007-12-14 [4] => 22:22:19Z [5] => elkuku [6] => ;)
     */
    private static function getVersionFromCHANGELOG($revOnly = false)
    {
        // TODO change to getVersionFromFile

        $file = JPATH_BASE.DS.'installation'.DS.'CHANGELOG';

        #		$file = JPATH_ADMINISTRATOR.DS.'components'.DS.$appName.DS.'CHANGELOG.php';
        if( ! file_exists($file)) return false;

        //--we do not use JFile here cause we only need one line which is
        //--normally at the beginning..
        $f = fopen($file, 'r');
        $ret = false;

        while($line = fgets($f, 1000))
        {
            if(strpos($line, '$Id:'))
            {
                $line = explode('$', $line);
                $line = explode(' ', $line[1]);
                $svn_rev = $line[2];
                $svn_date = date("d-M-Y", strtotime($line[3]));
                $ret = $svn_rev;
                $ret .=($revOnly) ? '' : '  / '.$svn_date;

                break;
            }
        }// while

        fclose($f);

        return $ret;
    }// function

}//class