<?php
/**
 * @version $Id$
 * @package
 * @subpackage
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 09.02.2009
 *
 * Install:
 * Add
 * require_once( "$IP/extensions/PermCalc/PermCalc.php" );
 * to your LocaleSettings.php
 *
 * Usage:
 * <permcalc>Introtext(optional</permcalc>
 *
 * Required:
 * Have Fun =;)
 */

//--No direct access
defined('MEDIAWIKI') or die('=;)');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

$wgExtensionFunctions[] = "wfPermCalc";
$wgExtensionCredits['parserhook'][] = array(
                'version'     => '1.0',
                'name'        => 'PermCalc',
                'author'      => array('Nikolai Plath'),
                'email'       => 'nik@',
                'url'         => 'http://www.mediawiki.org/wiki/Extension:PermCalc...soon',
                'description' => 'adds <tt>&lt;permcalc&gt;</tt> tags',
                );

function wfPermCalc()
{
	global $wgParser, $wgHooks;
	$wgParser->setHook( "permcalc", "renderPermCalc" );
	$wgHooks['OutputPageParserOutput'][] = 'fnPermCalcOutputHook';
}//function

function renderPermCalc($input, $argv, &$parser)
{

	$html = '<div id="permcalc"><div style="font-weight: bold; background-color: red; padding: 10px;">Bitte aktivieren Sie Javascript in Ihrem Browser</div></div>';
	$javascript  = '<!-- PermCalc Instance --><script type="text/javascript">drawForm();</script>';

	return $input.$html.$javascript;
}//function

/**
 * Entry point for the hook for printing JS and CSS:
 */
function fnPermCalcOutputHook( &$m_pageObj, $m_parserOutput ) {
	global $wgScriptPath;

	//--CSS
	$m_pageObj->addLink(
    array(
      'rel'   => 'stylesheet',
      'type'  => 'text/css',
      'href'  => $wgScriptPath . '/extensions/PermCalc/PermCalc.css'
    )
  );

  //--JS
  $m_pageObj->addScriptFile($wgScriptPath.DS.'extensions'.DS.'PermCalc'.DS.'PermCalc.js');

  # Be nice:
  return true;
}//function