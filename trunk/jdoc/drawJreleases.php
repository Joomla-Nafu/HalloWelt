<?php
/**
 * @version $Id$
 * @package     JFrameWorkDoc
 * @subpackage  External
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 22.07.2009
 */

error_reporting(E_ALL);

defined('BR') or define('BR', '<br />');
defined('NL') or define('NL', "\n");

$display = new EasyProjectDisplay();

$ID =( isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;
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
  <title>Draw JReleases4Wiki</title>

  <link href="/assets/images/jfavicon_t.ico" rel="shortcut icon" type="image/x-icon" />

  <link rel="stylesheet" href="assets/css/default.css" type="text/css" />
</head>

<body>
<div id="homeLink"><a href="index.php">Home</a></div>
<h3 style="float: right;">Downloads provided by <a href="httzp://joomlacode.org">JoomlaCode.org</a></h3>

<div>
    <img src="assets/images/joomla_logo_black.jpg" alt="Joomla! Logo"  />
Releases
<form action="drawJreleases.php">
    <div>
    Joomla! Version:
    <select name="id" onchange="form.submit();">
        <option value="0">Select...</option>
        <?php
        foreach($display->getReleases() as $idName => $idId)
        {
            $selected =( $idId == $ID ) ? ' selected="selected"' : '';
        	echo '<option value="'.$idId.'"'.$selected.'>'.$idName.'</option>';
        }//foreach
        ?>
    </select>
    <noscript><div style="display: inline;"><input type="submit" value="Submit" /></div></noscript>
    </div>
</form>

<div style="background-color: #eee;">
<?php
if( ! $ID )
{
    echo 'Please select a version...';
}
else
{
    $display->drawRelease($ID);
}
?>
</div>
</div>

<div class="easy_footer"><a class="toplink" href="#">Top</a>
<div class="valid_xhtml"><a
	href="http://validator.w3.org/check?uri=referer" class="external">XHTML
1.1</a><br />
<a href="http://jigsaw.w3.org/css-validator/check/referer"
	class="external">CSS 2.1</a></div>
Developed 2009 by <img src="assets/images/easy-joomla-favicon.ico"
	alt="Easy-Joomla.org" /> <a href="http://easy-joomla.org"
	class="external">Easy-Joomla</a> <br />
&bull;&bull;&bull; <em>Have FUN <tt>=;)</tt></em>
<div style="clear: both;"></div>
</div>

</body>

</html>
<?php
    /*
     * END...
     */

class EasyProjectDisplay
{
    private $JReleases = array(
        '1.5.15'=>4947
        , '1.5.14'=>4734
        ,'1.5.13'=>4712
        , '1.5.12'=>4665
        , '1.5.11'=>4556
        , '1.5.10'=>4460
        , '1.5.9'=>4288
        , '1.5.8'=>4136
        ///// '1.5.7'=>3941
        , '1.5.6'=>3883
        //, '1.5.5'=>3846
        , '1.5.4'=>3786
        , '1.5.3'=>3587
        , '1.5.2'=>3466
        , '1.5.1'=>3322
        , '1.5.0'=>2
        );

    function __construct()
    {
    }//function

    public function getReleases()
    {
    	return $this->JReleases;
    }//function

    public function drawRelease($ID)
    {
        if( ! $ID = intval($ID))
        {
            return '';
        }

        $versionlinks = array();
        $updateLinks = array();
        $options = array(
        'baseURL'=>'http://joomlacode.org'
        , 'project'=>'joomla'
        );


        if( $key = array_search($ID, $this->JReleases))
        {
            $version = $key;
        }
        else
        {
            $version = 'unknown';
        }

        $options['pkgID'] = $ID;

        $package =$this->getPackage($options);
        $regex = '/Joomla_(.*?)_to_/';

        foreach ($package->links as $link)
        {
            if( ! strpos($link, 'download')) { continue; }

            $ext = substr($link, strrpos($link, '.') + 1);
            $ext =( $ext == 'gz' ) ? 'tgz' : $ext;
            if( strpos($link, 'Stable-Full_Package'))
            {
                $versionLinks[$version][$ext] = $link;
            }
            elseif ( strpos($link, 'Stable-Patch_Package'))
            {
                preg_match('/Joomla_(.*?)_to_/',$link, $matches);
                $updateLinks[$version][$matches[1]][$ext] = $link;
            }
            if( isset($matches[1]))
            {
              arsort($updateLinks[$version][$matches[1]]);
            }
        }//foreach

        if( ! $updateLinks)
        {
            echo 'found NOTHING ...';
            return;
        }

        arsort($updateLinks[$version]);

        echo '<hr /><h2>HTML</h2>';

        $html = '';
        $html .= '<ul>'.NL;

        $html .= '<li class="version">Joomla! '.$version;
        foreach ($versionLinks[$version] as $vExt => $vLink)
        {
            $html .= NL.'&nbsp;&bull;&nbsp;<a href="'.$vLink.'">'.$vExt.'</a>';
        }//foreach
        $html .= '</li>';

        $html .= NL.'</ul>';
        $html .= NL.'<h3>Updates</h3>';
        $html .= NL.'<ul>';

        foreach ($updateLinks[$version] as $uVersion=>$uLinks)
        {
            $html .= NL.'<li class="version">Update '.$uVersion.' => '.$version;

            foreach ($uLinks as $uExt => $uLink)
            {
                $html .= NL.'&nbsp;&bull;&nbsp;<a href="'.$uLink.'">'.$uExt.'</a>';
            }//foreach

            $html .= '</li>';
        }//foreach

        $html .= NL.'</ul>';

        echo $html;
        echo '<textarea style="width: 100%; height: 100px;" cols="1000" rows="1000">'.htmlentities($html).'</textarea>';

        echo '<hr /><h2>Wiki syntax</h2>';
        echo '<textarea style="width: 100%; height: 100px;" cols="1000" rows="1000">';
        echo "== $version ==".NL;
        echo '* ';

        foreach ($versionLinks[$version] as $vExt => $vLink)
        {
            echo " [$vLink $vExt]";
        }//foreach

        foreach ($updateLinks[$version] as $uVersion=>$uLinks)
        {
            echo NL."**Update von '''$uVersion'''";
            foreach ($uLinks as $uExt => $uLink)
            {
                echo " [$uLink $uExt]";
            }//foreach
        }//foreach

        echo '</textarea>';
        echo '<hr /><h2>DOWNLOADS</h2><hr />';
        echo $package->string;

    }//function

    /**
     * Enter description here...
     *
     * @param unknown_type $options
     * @return unknown
     */
    private static function getPackage($options)
    {
        $url = $options['baseURL'].'/gf/project/'.$options['project'].'/frs/?action=FrsReleaseBrowse&frs_package_id='.$options['pkgID'];

        $result = self::get_web_page( $url );
        $content = $result['content'];

        preg_match("~<div class=\"main\">(.*)<div class=\"paginator\">~smU",$content, $matches);
        $resultString = (isset($matches[1])) ? $matches[1] : '';

        preg_match_all("~<map.*>(.*)</map>~smU", $resultString, $matches);
        $k = 0;
        foreach ($matches[0] as $m)
        {
            $resultString = str_replace($m, '', $resultString);
        }//foreach

        preg_match_all("~<img.*>~smU",$resultString, $matches);
        $k = 0;
        foreach ($matches[0] as $m)
        {
            $resultString = str_replace($m, '', $resultString);
        }//foreach

        preg_match_all("~<table(.*)>~smU",$resultString, $matches);
        foreach ($matches[1] as $m)
        {
            $resultString = str_replace($m, '', $resultString);
        }//foreach

        preg_match_all("~<th(.*)>~smU",$resultString, $matches);
        foreach ($matches[1] as $m)
        {
            $resultString = str_replace($m, '', $resultString);
        }//foreach

        preg_match_all("~<tr(.*)>~smU",$resultString, $matches);
        foreach ($matches[1] as $m)
        {
            $resultString = str_replace($m, '', $resultString);
        }//foreach

        //      preg_match_all("~<td(.*)>~smU",$resultString, $matches);
        //      foreach ($matches[1] as $m)
        //      {
        //          $resultString = str_replace($m, '', $resultString);
        //      }//foreach

        preg_match_all("~<div.*>(.*)<\/div>~smU",$resultString, $matches);
        foreach ($matches[0] as $m)
        {
            $resultString = str_replace($m, '', $resultString);
        }//foreach

        $resultString = str_replace(array('nowrap="nowrap" ', 'bgcolor="#FFFFFF" '), '', $resultString);
        $resultString = str_replace(array('<p>', '</p>'), '', $resultString);
        $resultString = str_replace(array('<br />', '<br/>', '<strong>', '</strong>'), '', $resultString);

        $resultString = str_replace('<table', '<table width="100%"', $resultString);

        $resultString = str_replace('a href="', 'a target="_blank" href="'.$options['baseURL'], $resultString);

        $buus = array(' valign="top"', ' target="_blank"');

        $resultString = str_replace($buus, '', $resultString);

        $regex = '/href\s*=\s*\"*([^\">]*)/i';
        preg_match_all($regex, $resultString, $matches);

        $links =( $matches[1] ) ? $matches[1] : array();
        $ret = new stdClass();
        $ret->string = $resultString;
        $ret->links = $links;

        return $ret;
    }//function

    /**
     * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
     * array containing the HTTP server response header fields and content.
     */
    private static function get_web_page( $url )
    {
        $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );
        if ( ! function_exists('curl_setopt_array'))
        {
            //--For PHP 5.1.4
            function curl_setopt_array(&$ch, $curl_options)
            {
                foreach ($curl_options as $option => $value)
                {
                    if (!curl_setopt($ch, $option, $value))
                    {
                        return false;
                    }
                }//foreach
                return true;
            }
        }

        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

        return $header;
    }//function

}//class
