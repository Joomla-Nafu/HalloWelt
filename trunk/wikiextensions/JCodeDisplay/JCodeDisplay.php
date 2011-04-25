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

defined('MW_SUPPORTS_PARSERFIRSTCALLINIT')
? //Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
$wgHooks['ParserFirstCallInit'][] = 'wfJCodeDisplay'
: // Otherwise do things the old fashioned way
$wgExtensionFunctions[] = 'wfJCodeDisplay';

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

    $wgParser->setHook('jcodedisplay', 'renderJCodeDisplay');

    $wgHooks['OutputPageParserOutput'][] = 'fnJCodeDisplayOutputHook';

    return true;
}//function

function renderJCodeDisplay($input, $argv, &$parser)
{
    global $IP;

    defined('NL') || define('NL', "\n");
    defined('BR') || define('BR', '<br />');

    list( $class, $method ) = explode('/', $input);

    if( ! $class | ! $method )
    return 'invalid call';

    //-- Default version to use
    $JVersion = '1.5.22';

    //-- Let's find the path...
    $baseDir = $IP;

    $fName = 'jmethodlist_'.$JVersion.'.txt';

    //-- Override if argument is set
    if(isset($argv['jversionmin'])
    || isset($argv['jversionmax']))
    {
        $vs = array();

        foreach(new DirectoryIterator($baseDir.DS.'sources'.DS.'joomla') as $item)
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
        return 'No matching versions found';

        uasort($vs, 'version_compare');

        $vs = array_reverse($vs);

        $keys = array_keys($vs);

        $JVersion = $keys[0];

        $fName = $vs[$JVersion];
    }

    if( ! file_exists($baseDir.DS.'sources'.DS.'joomla'.DS.$fName))
    return 'Method list <b style="color: red;">not found</b> for this version <tt>=:(</tt>';

    $methodList = file($baseDir.DS.'sources'.DS.'joomla'.DS.$fName);

    $baseDir .= DS.'sources'.DS.'joomla'.DS.$JVersion.DS.'libraries';

    if( ! is_dir($baseDir))
    return 'Joomla! sources not found in /sources/joomla';

    foreach($methodList as $line)
    {
        $line = trim($line);

        if(strpos($line, $class.'#'.$method.'#' ) !== 0 )
        continue;

        list($c, $m, $path, $start, $end) = explode('#', $line);

        $fullPath = $baseDir.DS.'joomla'.DS.$path;

        if( ! file_exists($fullPath))
        return 'file not found in method list';//.$fullPath;

        //--Read the file contents
        $lines = file($fullPath);

        $code = '';
        $codeRaw = '';

        for($i = $start-1; $i <= $end-1; $i++)
        {
            $l = rtrim($lines[$i]);

            //-- Strip leading tabs
            if(substr($l, 0, 1) == "\t")
            $l = substr($l, 1);

            //-- Convert tabs to three spaces
            $l = str_replace("\t", '   ', $l);

            $cLines[] = $l;
            $codeRaw .= sprintf('%4s', $i + 1).' '.$l.NL;
        }//for

        if(class_exists('GeSHI'))
        {
            $geshi = new GeSHi(implode("\n", $cLines), 'php');
            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
            $geshi->start_line_numbers_at($start);
            // $geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');

            setupGeSHiForJoomla($geshi);

            $parsedCode = $geshi->parse_code();
        }
        else//
        {
            $parsedCode = '<pre>'.$codeRaw.'</pre>';
        }

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

        $head = '';
        $head .= "'''<tt>".$class.$sig.$method."()</tt>'''".' in Joomla! <b>'.$JVersion.'</b>'.NL.NL;

        if(isset($argv['jversionmin']))
        $head .= sprintf('Diese Funktion exisiert seit Joomla! %s<br />', $argv['jversionmin']);

        if(isset($argv['jversionmax']))
        $head .= sprintf('Diese Funktion exisiert bis Joomla! %s<br />', $argv['jversionmax']);

        $head .= $pathTree;
        $head = $parser->recursiveTagParse($head);

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

function jcode_version_compare($version1, $version2)
{
    return version_compare($version1, $version2);
}//function

/**
 * Joomla Classes for GeSHi
 * @param $geshi object GeSHi
 * @return void
 */
function setupGeSHiForJoomla($geshi
, $uri='http://wiki.joomla-nafu.de/joomla-dokumentation/Joomla!_Programmierung/Framework/')
{
    $JClasses = array('JFrameworkConfig', 'JFactory', 'JRoute', 'JText', 'JApplication', 'JController'
    , 'JComponentHelper', 'JModel', 'JView', 'JApplicationHelper', 'JMenu', 'JModuleHelper', 'JPathway', 'JRouter'
    , 'JTree', 'JNode', 'JCache', 'JCacheCallback', 'JCacheOutput', 'JCachePage', 'JCacheView', 'JCacheStorage'
    , 'JCacheStorageApc', 'JCacheStorageEaccelerator', 'JCacheStorageFile', 'JCacheStorageMemcache'
    , 'JCacheStorageXCache', 'JFTP', 'JClientHelper', 'JLDAP', 'JDatabase', 'JDatabaseMySQL', 'JDatabaseMySQLi'
    , 'JRecordSet', 'JTable', 'JTableARO', 'JTableAROGroup', 'JTableCategory', 'JTableComponent', 'JTableContent'
    , 'JTableMenu', 'JTableMenuTypes', 'JTableModule', 'JTablePlugin', 'JTableSection', 'JTableSession', 'JTableUser'
    , 'JDocument', 'JDocumentError', 'JDocumentFeed', 'JFeedItem', 'JFeedEnclosure', 'JFeedImage'
    , 'JDocumentRendererAtom', 'JDocumentRendererRSS', 'JDocumentHTML', 'JDocumentRendererComponent'
    , 'JDocumentRendererHead', 'JDocumentRendererMessage', 'JDocumentRendererModule', 'JDocumentRendererModules'
    , 'JDocumentPDF', 'JDocumentRAW', 'JBrowser', 'JResponse', 'JURI', 'JError', 'JException', 'JLog', 'JProfiler'
    , 'JDispatcher', 'JArchive', 'JArchiveBzip2', 'JArchiveGzip', 'JArchiveTar', 'JArchiveZip', 'JFile', 'JFolder'
    , 'JPath', 'JFilterInput', 'JFilterOutput', 'JEditor', 'JHTML', 'JHTMLBehavior', 'JHTMLContent', 'JHTMLEmail'
    , 'JHTMLForm', 'JHTMLGrid', 'JHTMLImage', 'JHTMLList', 'JHTMLMenu', 'JHTMLSelect', 'JPagination'
    , 'JPaginationObject', 'JPane', 'JPaneTabs', 'JPaneSliders', 'JParameter', 'JElement', 'JElementCalendar'
    , 'JElementCategory', 'JElementEditors', 'JElementFilelist', 'JElementFolderlist', 'JElementHelpsites'
    , 'JElementHidden', 'JElementImageList', 'JElementLanguages', 'JElementList', 'JElementMenu', 'JElementMenuItem'
    , 'JElementPassword', 'JElementRadio', 'JElementSection', 'JElementSpacer', 'JElementSQL', 'JElementText'
    , 'JElementTextarea', 'JElementTimezones', 'JElementUserGroup', 'JToolBar', 'JButton', 'JButtonConfirm'
    , 'JButtonCustom', 'JButtonHelp', 'JButtonLink', 'JButtonPopup', 'JButtonSeparator', 'JButtonStandard'
    , 'JInstallerComponent', 'JInstallerLanguage', 'JInstallerModule', 'JInstallerPlugin', 'JInstallerTemplate'
    , 'JInstallerHelper', 'JInstaller', 'JHelp', 'JLanguageHelper', 'JLanguage', 'JMailHelper', 'JMail'
    , 'JPluginHelper', 'JPlugin', 'JRegistryFormat', 'JRegistryFormatINI', 'JRegistryFormatPHP', 'JRegistryFormatXML'
    , 'JSession', 'JSessionStorage', 'JSessionStorageApc', 'JSessionStorageDatabase', 'JSessionStorageEaccelerator'
    , 'JSessionStorageMemcache', 'JSessionStorageNone', 'JSessionStorageXcache', 'patTemplate_Function_Sef'
    , 'patTemplate_Function_Translate', 'patTemplate_Modifier_SEF', 'patTemplate_Modifier_Translate', 'JTemplate'
    , 'JAuthentication', 'JAuthenticationResponse', 'JAuthorization', 'JUserHelper', 'JUser', 'JArrayHelper'
    , 'JBuffer', 'JDate', 'JSimpleCrypt', 'JSimpleXML', 'JSimpleXMLElement', 'JString', 'JUtility', 'JObject'
    , 'JObservable', 'JObserver', 'JDocumentRenderer', 'JRequest', 'JEvent', 'JRegistry', 'JVersion'
    );

    $geshi->add_keyword_group(10, 'color: #600000; border-bottom: 1px dashed gray;', true, $JClasses);
    $geshi->set_url_for_keyword_group(10, $uri.'{FNAME}');
}//functopn
