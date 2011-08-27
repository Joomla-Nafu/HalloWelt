<?php
/**
 * @version $Id$
 * @package    JCodeDisplay
 * @subpackage MediaWiki extension
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 09-Feb-2009
 *
 * Install:
 * Add
 * require_once $IP.'/extensions/JCodeDisplay/JCodeDisplay.php';
 * to your LocaleSettings.php
 *
 * Usage:
 * <jcodedisplay [JVersion="" req=""]>Class/Function</jcodedisplay>
 *
 * Example:
 * <jcodedisplay>JLog/addEntry</jcodedisplay>
 * <jcodedisplay JVersion="1.6.rc1" req="min">JLog/addEntry</jcodedisplay>
 *
 * Note: If you specify a version you must also set the 'req' parameter either to "max" or "min"
 *
 * Required:
 * Have Fun =;)
 */

//--No direct access
defined('MEDIAWIKI') || die('=;)');

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

class JCodeDisplay
{
    public static function render($input, $argv, $parser)
    {
        global $IP;

        //-- Default version to use
        $JVersion = '11.1';

        $sourceDir = $IP.'/sources/joomla';

        defined('NL') || define('NL', "\n");
        defined('BR') || define('BR', '<br />');

        list($class, $method) = explode('/', $input);

        if( ! $class || ! $method )
        throw new Exception('Invalid call');

        //-- Let's find the path...
        $baseDir = $IP;

        $fName = 'jmethodlist_'.$JVersion.'.txt';

        //-- Override if argument is set
        if(isset($argv['jversionmin'])
        || isset($argv['jversionmax']))
        {
            $vs = array();

            foreach(new DirectoryIterator($baseDir.'/sources/joomla') as $item)
            {
                if(isset($argv['jversionmin'])
                && -1 == version_compare($item->getFilename(), $argv['jversionmin']))
                continue;

                if(isset($argv['jversionmax'])
                && 1 == version_compare($item->getFilename(), $argv['jversionmax']))
                continue;
                $dddd = $item->getFilename();
                $fName = $baseDir.'/sources/joomla/'.$item->getFilename().'/classes.xml';

                if(file_exists($fName))
                $vs[$item->getFilename()] = $fName;
            }//foreach

            if( ! count($vs))
            {
                foreach(new DirectoryIterator($baseDir.'/sources/joomla') as $item)
                {
                    if( ! preg_match('/jmethodlist_(.*).txt/', $item, $matches))
                    continue;

                    if(isset($argv['jversionmin'])
                    && -1 == version_compare($matches[1], $argv['jversionmin']))
                    continue;

                    if(isset($argv['jversionmax'])
                    && 1 == version_compare($matches[1], $argv['jversionmax']))
                    continue;

                    $vs[$matches[1]] = $item->getFilename();
                }//foreach

                if( ! count($vs))
                throw new Exception('No matching versions found');
            }

            uasort($vs, 'version_compare');

            $vs = array_reverse($vs);

            $keys = array_keys($vs);

            $JVersion = $keys[0];

            $fName = $vs[$JVersion];
        }

        if( ! file_exists($baseDir.DS.'sources'.DS.'joomla'.DS.$fName))
        {
            $path = $baseDir.'/sources/joomla/'.$JVersion.'/xml/classes.xml';

            if( ! file_exists($path))
            throw new Exception('Method list not found for this version =:(');

            $methodList = simplexml_load_file($path);
        }
        else
        {
            $methodList = file($baseDir.DS.'sources'.DS.'joomla'.DS.$fName);
        }

        if( ! is_dir($baseDir.'/sources/joomla/'.$JVersion.'/libraries'))
        throw new Exception('Joomla! sources not found in /sources/joomla');

        if(is_a($methodList, 'SimpleXMLElement'))
        {
            $xml = '';

            foreach ($methodList->class as $c)
            {
                if(strtolower($class) != strtolower($c->attributes()->name))
                continue;

                $sourcePath = $c->attributes()->src;//@todo clean path...
                $sourcePath = $baseDir.'/sources/joomla/'.$JVersion.'/'.$sourcePath;

                $path = $baseDir.'/sources/joomla/'.$JVersion.'/xml/'.$c->attributes()->xml;

                $cl = simplexml_load_file($path);

                if( ! $cl)
                {
                    $msg = '';
                    $msg .= 'Class file not found :(';
                    $msg .=(DBG_NAFUCODE) ? ' '.$path : '';

                    throw new Exception($msg);
                }

                foreach ($cl->class as $cla)
                {
                    if(strtolower($class) != strtolower($cla->attributes()->name))
                    continue;

                    foreach ($cla->method as $m)
                    {
                        if($method != $m->attributes()->name)
                        continue;

                        /*
                         * FOUND IT :)
                        */

                        //  var_dump($m)    ;

                        if( ! file_exists($sourcePath))
                        {
                            $msg = '';
                            $msg .= 'File not found in method list';
                            $msg .=(DBG_NAFUCODE) ? ': '.$sourcePath : '';

                            throw new Exception($msg);
                        }

                        //--Read the file contents
                        $lines = file($sourcePath);

                        $cLines = NafuCodeHelper::cutCode($lines, $m->attributes()->start, $m->attributes()->end);

                        $parsedCode = NafuCodeHelper::highlightCode($cLines, 'php', $m->attributes()->start, $m->attributes()->end);

                        $pathTree = '{{folder|libraries/joomla}}'.NL;

                        $p = $path;

                        $stripPath = '/home/elkuku/eclipsespace/indigogit2/nafuwiki_20110818/sources/joomla/'
                        .$JVersion.'/xml/libraries/joomla/';//@todo...

                        $p = str_replace($stripPath, '', $p);

                        $stripPath = '/kunden/214043_96450/rp-hosting/14658/43509/joomla_nafu/wiki/sources/joomla/'
                        .$JVersion.'/xml/libraries/joomla/';//@todo...

                        $p = str_replace($stripPath, '', $p);

                        $stripPath = '/home/elkuku/eclipsespace/indigogit2/nafuwiki_20110818/sources/joomla/'
                        .$JVersion.'/xml/libraries/';//@todo...

                        $p = str_replace($stripPath, '', $p);

                        $stripPath = '/kunden/214043_96450/rp-hosting/14658/43509/joomla_nafu/wiki/sources/joomla/'
                        .$JVersion.'/xml/libraries/';//@todo...

                        $p = str_replace($stripPath, '', $p);

                        if(0 === strpos($p, '/'))
                        {
                            //-- Path clean FAILED :(
                            $p = 'error_happened___:(___/file.php.xml';
                        }

                        $p = substr($p, 0, strrpos($p, '.'));//-- strip the 'xml' extension

                        $pathParts = explode('/', $p);

                        $cc = 1;
                        $colors = array('', '|red', '|green', '|yellow');

                        foreach($pathParts as $part)
                        {
                            if(strpos($part, '.'))
                            {
                                //--Seems to be a file..argh
                                $pathTree .= str_repeat('*', $cc).'{{file|'.$part.'|php}}'.NL;

                                continue;
                            }

                            $color =(isset($colors[$cc])) ? $colors[$cc] : '';

                            $pathTree .= str_repeat('*', $cc).'{{folder|'.$part.$color.'}}'.NL;

                            $cc ++;
                        }//foreach

                        $parts = explode('.', $JVersion);

                        $JMinor = $parts[0].'.'.$parts[1];

                        $sig =('true' == $m->attributes()->static) ? '::' : '->';

                        $head = '';

                        $head .= '{{JVer|'.$JMinor.'}} ';
                        $head .= "'''<tt>".$cla->attributes()->name.$sig.$m->attributes()->name."()</tt>'''";

                        //                         if(isset($argv['jversionmin'])
                        //                         ||isset($argv['jversionmax']))
                        //                         $a = 1;

                        //                                     $head .= ' in Joomla! <b>'.$JVersion.'</b>';

                        $head .= NL.NL;

                        //             if(isset($argv['jversionmin']))
                        //             $head .= sprintf('Diese Funktion exisiert seit Joomla! %s<br />', $argv['jversionmin']);

                        //             if(isset($argv['jversionmax']))
                        //             $head .= sprintf('Diese Funktion exisiert bis Joomla! %s<br />', $argv['jversionmax']);

                        $head .= $pathTree;
                        $head = $parser->recursiveTagParse($head);

                        return $head.$parsedCode;
                    }//foreach
                }//foreach
            }//foreach
        }
        else
        {
            foreach($methodList as $line)
            {
                $line = trim($line);

                if(strpos($line, $class.'#'.$method.'#' ) !== 0 )
                continue;

                list($c, $m, $path, $start, $end) = explode('#', $line);

                $fullPath = $baseDir.'/sources/joomla/'.$JVersion.'/libraries/joomla/'.$path;

                if( ! file_exists($fullPath))
                throw new Exception('file not found in method list');//.$fullPath;

                //--Read the file contents
                $lines = file($fullPath);

                $cLines = NafuCodeHelper::cutCode($lines, $start, $end);

                $parsedCode = NafuCodeHelper::highlightCode($cLines, 'php', $start, $end);

                $fSig = $cLines[0];
                $fSig = substr($fSig, 0, strpos($fSig, 'function'));

                $sig =(strpos($fSig, 'static') !== false) ? '::' : '->';

                $pathTree = '{{folder|libraries/joomla}}'.NL;

                $pathParts = explode('/', $path);

                $cc = 1;
                $colours = array('', '|red', '|green', '|yellow');

                foreach($pathParts as $part)
                {
                    if(strpos($part, '.'))
                    {
                        //--Seems to be a file..argh
                        $pathTree .= str_repeat('*', $cc).'{{file|'.$part.'|php}}'.NL;

                        continue;
                    }

                    $color = '';
                    $pathTree .= str_repeat('*', $cc).'{{folder|'.$part.$colours[$cc].'}}'.NL;
                    $cc ++;
                }//foreach

                $parts = explode('.', $JVersion);

                $JMinor = $parts[0].'.'.$parts[1];

                $head = '';

                $head = '{{JVer|'.$JMinor.'}} ';
                $head .= "'''<tt>".$class.$sig.$method."()</tt>'''";

                if(isset($argv['jversionmin'])
                ||isset($argv['jversionmax']))
                $a = 1;

                $head .= ' in Joomla! <b>'.$JVersion.'</b>';

                $head .= NL.NL;

                //             if(isset($argv['jversionmin']))
                //             $head .= sprintf('Diese Funktion exisiert seit Joomla! %s<br />', $argv['jversionmin']);

                //             if(isset($argv['jversionmax']))
                //             $head .= sprintf('Diese Funktion exisiert bis Joomla! %s<br />', $argv['jversionmax']);

                $head .= $pathTree;
                $head = $parser->recursiveTagParse($head);

                return $head.$parsedCode;
            }//foreach
        }

        throw new Exception('SOURCE CODE NOT FOUND :(');
    }//function
}//class

function jcode_version_compare($version1, $version2)
{
    return version_compare($version1, $version2);
}//function
