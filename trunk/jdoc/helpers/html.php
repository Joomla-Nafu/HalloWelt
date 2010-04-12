<?php
/**
 * @version $Id$
 * @package JFrameWorkDoc
 * @subpackage  Helpers
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 18.07.2009
 */
abstract class EasyHtml
{
    public $indent = 0;

    /**
     *
     * @param $ac
     * @param $newIndent
     * @return unknown_type
     */
    public static function idt($ac = '', $newIndent = 0)
    {
        static $indent = 0;

        if( $newIndent )
        {
            $indent = $newIndent;
        }

        if($ac == '-') $indent --;

        $i = NL.str_repeat('   ', $indent);

        if($ac == '+') $indent ++;

        return $i;
    }//function

    /**
     * Extract strings from svn:property Id
     *
     * @param string $path full path to file
     * @param bool $revOnly true to return revision number only
     * @return string/bol propertystring or FALSE
     * like:
     * @ version $I d: CHANGELOG.php 362 2007-12-14 22:22:19Z elkuku $
     * [0] => Id: [1] => CHANGELOG.php [2] => 362 [3] => 2007-12-14 [4] => 22:22:19Z [5] => elkuku [6] => ;)
     */
    public static function getVersionFromFile( $path, $revOnly=false )
    {
        // TODO change to getVersionFromFile

        if( ! file_exists($path)) return false;

        //--we do not use JFile here cause we only need one line which is
        //--normally at the beginning..
        $f = fopen($path, 'r');
        $ret = false;

        while($line = fgets($f, 1000))
        {
            if(strpos( $line, '@version'))
            {
                $line = explode('$', $line);
                $line = explode(' ', $line[1]);
                $svn_rev = $line[2];
                $svn_date = date("Y-M-d", strtotime($line[3]));
                $ret = $svn_rev;
                $ret .=($revOnly) ? '' : '  / '.$svn_date;

                break;
            }
        }// while

        fclose($f);

        return $ret;
    }// function

    public static function footer()
    {
        ?>
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
        <?php
    }//function

}//class
