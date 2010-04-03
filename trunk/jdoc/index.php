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
    <div style="float: left; padding: 0.5em; border: 1px solid gray;">
    <ul>
    	<li><a href="jdoc.php">Joomla! framework ducomentor</a></li>
    	<li><a href="doccommenterrors.html">Joomla! trunk DocComment ERRORs</a></li>
    	<li><a href="drawJreleases.php">Joomla! Releases</a></li>
    </ul>
    </div>
</div>
<div style="clear: both;"></div>

<?php if(file_exists($base.DS.'svn_info')) : ?>
<pre class="svn_info">
<?php
$fileContents = implode("", file($base.DS.'svn_info'));
echo $fileContents;
?>
</pre>
<?php endif; ?>

</body>

</html>