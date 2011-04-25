<?php
/**
 * @version $Id$
 * @package    HalloWelt
 * @subpackage MediaWiki extension
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 25-Apr-2011
 *
 * Install:
 * Add
 * require_once $IP.'/extensions/HalloWelt/HalloWelt.php';
 * to your LocaleSettings.php
 *
 * Display code:
 * <hallowelt>VERSION/CHAPTER/path/to/code</hallowelt>
 *
 * Update code:
 * <hallowelt>update/VERSION</hallowelt>
 *
 * Required:
 * Have Fun =;)
 */

//-- No direct access
defined('MEDIAWIKI') || die('=;)');

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

defined('BR') || define('BR', '<br />');

defined('MW_SUPPORTS_PARSERFIRSTCALLINIT')
? //Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
$wgHooks['ParserFirstCallInit'][] = 'wfHalloWelt'
: // Otherwise do things the old fashioned way
$wgExtensionFunctions[] = 'wfHalloWelt';

$wgExtensionCredits['parserhook'][] = array(
'version' => '1.0',
'name' => 'HalloWelt',
'author' => array('Nikolai Plath'),
'email' => 'nik@',
'url' => 'http://www.mediawiki.org/wiki/Extension:HalloWelt...soon',
'description' => 'Interface for documenting Joomla! classes',
);

function wfHalloWelt()
{
    global $wgParser, $wgHooks;

    $wgParser->setHook('hallowelt', 'renderHalloWelt');

    $wgHooks['OutputPageParserOutput'][] = 'fnHalloWeltOutputHook';

    return true;
}//function

function renderHalloWelt($input, $argv, &$parser)
{
    global $IP;

    try
    {
        $hwBuilder = new HWBuilder($IP);

        if(0 === strpos($input, 'update'))
        {
            $hwBuilder->update($input);

            return 'HalloWelt sources has been updated :)';
        }

        $code = $hwBuilder->display($input);

        return $code;
    }
    catch (Exception $e)
    {
        return '<b style="color: red;">'.$e->getMessage().'</b>';
    }
}//function

/**
 *
 * Enter description here ...
 * @author elkuku
 *
 */
class HWBuilder
{
    private $IP = '';

    private $basePath = '';

    private $baseUri = 'http://joomlacode.org/svn/nafuwiki/';

    private $project = '';

    public function __construct($IP)
    {
        $this->IP = $IP;

        $this->basePath = $IP.'/sources/hallowelt';
    }

    public function display($input)
    {
        $dirtyPath = $input;

        $cleanPath = str_replace('..', '', $dirtyPath);

        //@todo - clean the path even more ;)

        $path = $this->basePath.'/'.$cleanPath;

        if( ! file_exists($path))
        throw new Exception('HalloWelt Source not found :( ');//.$path);

        if( ! class_exists('GeSHi'))
        {
            require_once $this->IP.'/extensions/SyntaxHighlight_GeSHi/geshi/geshi.php';

            if( ! class_exists('GeSHi'))
            throw new Exception('GeSHi not found :(');
        }

        $lines = file($path);

        $cleanLines = array();

        foreach ($lines as $line)
        {
            $line = rtrim($line);
            $cleanLines[] = $line;
        }//foreach

        $ext = substr($path, strrpos($path, '.'));

        $geshi = new GeSHi(implode("\n", $cleanLines), $ext);

        // $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        //            $geshi->start_line_numbers_at($start);
        // $geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');

        setupGeSHiForJoomla($geshi);

        $parsedCode = $geshi->parse_code();

        return $parsedCode;
    }//function

    public function update($input)
    {
        $parts = explode('/', $input);

        if(2 != count($parts))
        throw new Exception('Please specify as: "update/VERSION"');

        $this->project = $parts[1];

        $base = $this->basePath.'/'.$parts[1];

        if( ! file_exists($base))
        throw new Exception('HalloWelt: Invalid Project Dir ');

        require_once $this->IP.'/extensions/HalloWelt/svnclient/phpsvnclient.php';

        $subDir = 'hallowelt_1.6';

        return $this->checkout($this->project);
    }//function

    private function checkout($subDir)
    {
        static $iniDir = '';

        if( ! $iniDir)
        {
            $subDir = 'hallowelt_'.$subDir.'/sources';//@todo temp
            $iniDir = $subDir;
        }

        $DEBUG = false;

        $user = 'anonymous';
        $pass = '';

        $svnClient = new phpsvnclient($this->baseUri, $subDir, $user, $pass, $DEBUG);

        $files = $svnClient->getDirectoryFiles();

        foreach($files as $file)
        {
            if($file['path'] == $subDir)
            continue;


            if('directory' == $file['type'])
            {
                $this->Checkout($file['path']);

                continue;
            }

            $parts = explode('/', $file['path']);

            $fileName = array_pop($parts);

            $contents = $svnClient->getFile($fileName);

            $path = substr($file['path'], strlen($iniDir) + 1);

            $this->writeFile($path, $contents);
        }

        return true;
    }//function

    private function writeFile($path, $contents)
    {
        $parts = explode(DS, $path);

        array_pop($parts);

        $p = $this->basePath.'/'.$this->project;

        foreach ($parts as $part)
        {
            if( ! $part)
            continue;

            $p .= '/'.$part;

            if( ! is_dir($p))
            mkdir($p);
        }//foreach

        $handle = fopen($this->basePath.'/'.$this->project.'/'.$path, 'w');

        fwrite($handle, $contents);
    }//function
}//class

/**
 * Entry point for the hook for printing JS and CSS:
 */
function fnHalloWeltOutputHook( &$m_pageObj, $m_parserOutput ) {
    global $wgScriptPath;

    //--CSS
    // $m_pageObj->addLink(
    // array(
    // 'rel' => 'stylesheet',
    // 'type' => 'text/css',
    // 'href' => $wgScriptPath . '/extensions/HalloWelt/HalloWelt.css'
    // )
    // );

    //--JS
    # $m_pageObj->addScriptFile($wgScriptPath.DS.'extensions'.DS.'PermCalc'.DS.'PermCalc.js');

    //-- Be nice:
    return true;
}//function
