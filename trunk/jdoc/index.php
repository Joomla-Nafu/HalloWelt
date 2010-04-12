<?php
/**
 * @version SVN: $Id$
 * @package    JFrameWorkDoc
 * @subpackage Base
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 23-Jun-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$base = dirname(__FILE__);
define('DS', DIRECTORY_SEPARATOR);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:schemaLocation="http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
     xml:lang="de-de" >

<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="index, follow" />
  <meta name="keywords" content="joomla, Joomla" />
  <meta name="description" content="Joomla! - dynamische Portal-Engine und Content-Management-System" />
  <meta name="generator" content="Joomla! 1.5 - Open Source Content Management" />
  <title>JFrameworkDocumentor</title>
  <link href="/assets/images/jfavicon_t.ico" rel="shortcut icon" type="image/x-icon" />

  <link rel="stylesheet" href="assets/css/default.css" type="text/css" />
</head>

<body>
<div>
    <img src="assets/images/joomla_logo_black.jpg" alt="Joomla! Logo"  />
    <div style="text-align: center;">
    	<img src="assets/images/welcome1.png" alt="Welcome my fellow documentors" />
    </div>
</div>

<?php if(file_exists($base.DS.'svn_info')) : ?>
<pre class="svn_info" style="float: right;">
<?php
$fileContents = file($base.DS.'svn_info');
foreach($fileContents as $line)
{
    if( ! trim($line)) continue;
	if(strpos($line, 'Pfad') === 0) continue;
	if(strpos($line, 'UUID') === 0) continue;
	if(strpos($line, 'Knotentyp') === 0) continue;
	if(strpos($line, 'Plan') === 0) continue;

	echo $line;
}//foreach
?>
</pre>
<?php endif; ?>

<div style="float: left; padding: 0.5em; border: 1px solid gray;">
    <ul>
    	<li><a href="jdoc.php">Joomla! framework documentor</a></li>
    	<li><a href="drawJreleases.php">Joomla! Releases</a></li>
    	<li><a href="drawTables.php">Joomla! core database changes</a></li>
    	<li><a href="doccommenterrors.html">Joomla! trunk DocComment ERRORs</a></li>
    </ul>
</div>

<div style="clear: both;"></div>

<div class="easy_footer">The code is released und <a href="http://www.gnu.org/licenses/gpl.html">GPL</a> and available from SVN at <a href="http://joomlacode.org/svn/nafuwiki/trunk/jdoc">Joomlacode</a> <small><em>anonymous with no password</em></small></div>

<div style="float: right;">
    <a href="http://validator.w3.org/check?uri=referer">
    	<img src="http://www.w3.org/Icons/valid-xhtml11-blue" alt="Valid XHTML 1.1" height="31" width="88" />
    </a>
    <a href="http://jigsaw.w3.org/css-validator/check/referer">
        <img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS ist valide!" />
    </a>
</div>

</body>

</html>