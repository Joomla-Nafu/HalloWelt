<?php
/**
 * @version $Id$
 * @package    NafuCode
 * @subpackage MediaWiki extension
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 25-Apr-2011
 *
 * Install:
 * Add
 * require_once $IP.'/extensions/NafuCode/NafuCode.php';
 * to your LocaleSettings.php
 *
 * Display code:
 * <nafucode>VERSION/PROJECT[/CHAPTER]/path/to/code</nafucode>
 *
 * Update code:
 * <nafucode>@update/PROJECT[/VERSION]</nafucode>
 *
 * Required:
 * Have Fun =;)
 */

//-- No direct access
defined('MEDIAWIKI') || die('=;)');

define('DBG_NAFUCODE', 1);

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('BR') || define('BR', '<br />');

define('NAFUCODE_PATH_SOURCES', $IP.'/sources/nafucode');
define('NAFUCODE_PATH_BASE', dirname(__FILE__));

defined('MW_SUPPORTS_PARSERFIRSTCALLINIT')
? //Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
$wgHooks['ParserFirstCallInit'][] = 'wfNafuCode'
: // Otherwise do things the old fashioned way
$wgExtensionFunctions[] = 'wfNafuCode';

$wgExtensionCredits['parserhook'][] = array(
    'version' => '1.0',
    'name' => 'NafuCode',
    'author' => array('Nikolai Plath'),
    'email' => 'nik@',
    'url' => 'http://www.mediawiki.org/wiki/Extension:NafuCode...soon',
    'description' => 'Interface for documenting Joomla! classes',
);

$wgExtensionCredits['specialpage'][] = array(
        'name' => 'MyExtension',
        'author' => 'My name',
        'url' => 'http://www.mediawiki.org/wiki/Extension:MyExtension',
        'description' => 'Default description message',
        'descriptionmsg' => 'myextension-desc',
        'version' => '0.0.0',
);

$wgAutoloadClasses['SpecialNafuCode'] = NAFUCODE_PATH_BASE.'/SpecialNafuCode.php';
$wgExtensionMessagesFiles['NafuCode'] = NAFUCODE_PATH_BASE.'/NafuCode.i18n.php';
$wgSpecialPages['NafuCode'] = 'SpecialNafuCode';
$wgSpecialPageGroups['NafuCode'] = 'Nafu';

function wfNafuCode()
{
    global $wgParser, $wgHooks;//-- Mediawiki stuff

    $wgParser->setHook('nafucode', 'renderNafuCode');

    $wgParser->setHook('hallowelt', 'render_OLD_NafuCode');//@todo deprecate
    $wgParser->setHook('jcodedisplay', 'render_OLD_JCode');//@todo deprecate

    $wgHooks['OutputPageParserOutput'][] = 'fnNafuCodeOutputHook';

    return true;
}//function

function renderNafuCode($input, $argv, $parser)
{
    global $IP, $wgUser, $wgOut;

    try
    {
        if( ! is_dir(NAFUCODE_PATH_SOURCES))
        throw new Exception('Please create the directory sources/nafucode in your webroot');

        include_once 'helpers/nafucode.php';

        $helper = new NafuCodeHelper($input, $argv, $parser);

        if(0 !== strpos($input, '@'))
        return $helper->display($input, $argv);

        //-- Command string
        $parts = explode('/', $input);

        switch($parts[0])
        {
            case '@update':
                return $helper->update();
                break;

            case '@projects' :
                return $helper->listProjects();
                break;

            case '@tree' :
                include_once 'helpers/hwbuilder.php';

                return $parser->recursiveTagParse(HWBuilder::tree($input));
                break;

            case '@J' :
                include_once 'helpers/jcode.php';

                $input = NafuCodeHelper::stripCommand($input);

                return JCodeDisplay::render($input, $argv, $parser);
                break;

            default:
                throw new Exception('Unknown command');
            break;
        }//switch
    }
    catch(Exception $e)
    {
        $msg = '';
        $msg .= '<p style="color: red;">'.$e->getMessage().'</p>';
        $msg .=(DBG_NAFUCODE) ? '<pre>'.$e.'</pre>' : '';

        return $msg;
    }//try
}//function

/**
 *
 * Enter description here ...
 * @param unknown_type $input
 * @param unknown_type $argv
 * @param unknown_type $parser
 *
 * @deprecated used for <jcodedisplay> extension
 *
 * @return Ambigous <string, mixed>
 */
function render_OLD_JCode($input, $argv, $parser)
{
    $input = '@J/'.$input;

    return renderNafuCode($input, $argv, $parser);
}//function

/**
 *
 * Enter description here ...
 * @param unknown_type $input
 * @param unknown_type $argv
 * @param unknown_type $parser
 *
 * @deprecated - used for <hallowelt> extension
 */
function render_OLD_NafuCode($input, $argv, $parser)
{
    $parts = explode('/', $input);

    switch($parts[0])
    {
        case 'tree':
            array_shift($parts);

            $project = array_shift($parts);

            if('1.6' != $project)
            return 'This is for hallowelt 1.6 ;(';

            $cmd = '@tree/hw16/'.$parts[0];

            break;

        default:
            $project = array_shift($parts);

        if('1.6' != $project)
        return 'This is for hallowelt 1.6 ;(';

        $cmd = 'hw16/'.implode('/', $parts);
        break;
    }//switch

    return renderNafuCode($cmd, $argv, $parser);
}//function

/**
 * Entry point for the hook for printing JS and CSS.
 */
function fnNafuCodeOutputHook(&$m_pageObj, $m_parserOutput)
{
    global $wgScriptPath;

    //--CSS
    // $m_pageObj->addLink(
    // array(
    // 'rel' => 'stylesheet',
    // 'type' => 'text/css',
    // 'href' => $wgScriptPath . '/extensions/NafuCode/NafuCode.css'
    // )
    // );

    //--JS
    // $m_pageObj->addScriptFile($wgScriptPath.DS.'extensions'.DS.'PermCalc'.DS.'PermCalc.js');

    //-- Be nice:
    return true;
}//function

/**
* Joomla Classes for GeSHi
* @param $geshi object GeSHi
*
* @deprecated use xml file to get a class list
*
* @return void
*/
function setupGeSHiForJoomla(GeSHi $geshi
, $uri = 'http://wiki.joomla-nafu.de/joomla-dokumentation/Joomla!_Programmierung/Framework/')
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
}//function
