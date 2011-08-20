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

define('DBG_NAFUCODE', 0);

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

$wgAutoloadClasses['SpecialNafuCode'] = NAFUCODE_PATH_BASE.'/SpecialNafuCode.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles['NafuCode'] = NAFUCODE_PATH_BASE.'/NafuCode.i18n.php'; # Location of a messages file (Tell MediaWiki to load this file)
$wgSpecialPages['NafuCode'] = 'SpecialNafuCode'; # Tell MediaWiki about the new special page and its class name
$wgSpecialPageGroups['NafuCode'] = 'Nafu';

function wfNafuCode()
{
    global $wgParser, $wgHooks;//-- Mediawiki stuff

    $wgParser->setHook('nafucode', 'renderNafuCode');
    $wgParser->setHook('hallowelt', 'renderOLDNafuCode');

    $wgHooks['OutputPageParserOutput'][] = 'fnNafuCodeOutputHook';

    return true;
}//function

function renderNafuCode($input, $argv, $parser)
{
    global $IP, $wgUser;//-- Mediawiki base path
    global $wgOut;
    //  $wgOut->addHTML('<b>This is not a pipe...</b><pre>'.print_r($wgOut, 1).'</pre>');
    //   $wgOut->showErrorPage('error','badarticleerror');
    try
    {
        if( ! is_dir(NAFUCODE_PATH_SOURCES))
        throw new Exception('Please create the directory sources/nafucode in your webroot');

        require_once 'helpers/nafucode.php';

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
                require_once 'helpers/hwbuilder.php';

                return $parser->recursiveTagParse(HWBuilder::tree($input));
                break;

            default:
                throw new Exception('Unknown command');
            break;
        }//switch
    }
    catch (Exception $e)
    {
        return '<p style="color: red;">'.$e->getMessage().'</p>';
    }
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
function renderOLDNafuCode($input, $argv, $parser)
{
    $parts = explode('/', $input);

    switch ($parts[0])
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
function fnNafuCodeOutputHook( &$m_pageObj, $m_parserOutput ) {
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
    # $m_pageObj->addScriptFile($wgScriptPath.DS.'extensions'.DS.'PermCalc'.DS.'PermCalc.js');

    //-- Be nice:
    return true;
}//function
