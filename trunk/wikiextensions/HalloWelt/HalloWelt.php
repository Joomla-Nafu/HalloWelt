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
        $hwBuilder = new HWBuilder($IP, $parser);

        if(0 === strpos($input, 'update'))
        {
            $hwBuilder->update($input);

            return 'HalloWelt sources has been updated :)';
        }
        else if(0 === strpos($input, 'tree'))
        {
            return $hwBuilder->tree($input);
        }
        else if(0 === strpos($input, 'projecttree'))
        {
            return $hwBuilder->tree($input, true);
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

    private $baseUriHW = 'Joomla!_Programmierung/Programmierung/Hallo_Welt_J1.6';

    private $project = '';

    private $projectSub = '';

    public function __construct($IP, $parser)
    {
        $this->IP = $IP;
        $this->parser = $parser;

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

        array_shift($parts);

        $this->project = $parts[0];

        if(count($parts > 1))
        $this->projectSub = $parts[1];

        if( ! $this->project)
        throw new Exception('Please specify as: "update/VERSION"');

        if( ! file_exists($this->basePath.'/'.$this->project))
        throw new Exception('HalloWelt: Invalid Project Dir ');

        require_once $this->IP.'/extensions/HalloWelt/svnclient/phpsvnclient.php';

        return $this->checkout();
    }//function

    private function checkout($subDir = '')
    {
        static $iniDir = '';

        if( ! $iniDir)
        {
            $subDir = 'hallowelt_'.$this->project.'/sources';//@todo temp

            if($this->projectSub)
            $subDir .= '/'.$this->projectSub;

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

        $base = $this->basePath.'/'.$this->project;

        if($this->projectSub)
        {
            $base .= '/'.$this->projectSub;

            if( ! is_dir($base))
            mkdir($base);
        }

        $p = $base;

        foreach ($parts as $part)
        {
            if( ! $part)
            continue;

            $p .= '/'.$part;

            if( ! is_dir($p))
            mkdir($p);
        }//foreach

        $handle = fopen($base.'/'.$path, 'w');

        fwrite($handle, $contents);
    }//function

    public function tree($input, $projectOnly = false)
    {
        $parts = explode('/', $input);

        array_shift($parts);

        $this->project = $parts[0];

        if(count($parts > 1))
        $this->projectSub = $parts[1];

        $base = $this->basePath.'/'.$this->project;

        if($this->projectSub)
        {
            $base .= '/'.$this->projectSub;

            if( ! is_dir($base))
            throw new Exception('Project dir not found for tree');
        }

        if( ! file_exists($base.'/links'))
        throw new Exception('Link list not found for tree');


        $lines = file($base.'/links');

        $output = array();
        //$output[] = print_r($lines, 1);
        // $output[] = 'Project'.$this->project;

        //    $output[] = $base.'/links';

        $items = array();

        $i = 0;

        foreach ($lines as $line)
        {
            $line = trim($line);

            if( ! $line
            || 0 === strpos($line, '#'))
            continue;

            $parts = explode('/', $line);

            $projectNum = array_shift($parts);

            if($projectOnly
            && $projectNum != $this->projectSub)
            continue;

            $fileName = implode('/', $parts);

            $XfileName = array_pop($parts);

            $s = implode("']['", $parts);

            /*
             * eval is EVAL :|
             */

            eval("if( ! isset(\$items['".$s."'])) \$items['".$s."'] = array();");

            eval("\$items['".$s."'][] = '$projectNum/$fileName';");
        }//foreach

        //$output[] = print_r($items, 1);

        //        $output[] = '== Das Installationspaket ==';
        //        $output[] = 'Der Inhalt des [[Teil_01#Ein_Installationspaket_erstellen|Codeverzeichnisses]] außerhalb Ihrer Komponente.';

        $lines = $this->drawTreeLine($items);
        //$output[] = print_r($lines, 1);

        $output[] = '{| class="dirtree"';
        $output[] = '|-';
        $output[] = '|{{file|hallowelt_paket|zip}}';

        $output = array_merge($output, $lines);

        $output[] = '|-';
        $output[] = '|{{tree|L}}{{file|[[#ADMIN/hallowelt.xml|hallowelt.xml]]|xml}}';

        $output[] = '|}';

        $output = implode("\n", $output);

        $output = $this->parser->recursiveTagParse($output);
        return $output;

    }//function

    private function drawTreeLine($items, $level = 0, $levels = array())
    {
        static $output = array();

        static $levels = array();

        $fileCount = 0;
        $folderCount = 0;

        foreach ($items as $folder => $subItems)
        {
            if(is_array($subItems))
            {
                $output[] = '|-';

                $s = '';
                $s .= '|';

                if($level == 0)
                {
                    $s .= '{{tree|T}}';
                }
                else
                {
                    $s .= '{{tree|V}}';


                    $folderCount ++;
                    $s .= $this->treeDetectLevel($levels, 0, $folderCount);
                }

                $output[] = $s.'{{folder|'.$folder.'}}';

                $levels[$level] = count($subItems);

                $this->drawTreeLine($subItems, $level + 1, $levels);

                unset($levels[$level]);

                continue;
            }

            $fileCount ++;

            $levels[$level - 1] = $this->treeFileCount($items);

            $output[] = '|-';

            $s = '';
            $s .= '|';
            $s .= '{{tree|V}}';

            $s .= $this->treeDetectLevel($levels, $fileCount, $folderCount);

            $parts = explode('/', $subItems);

            $chapter = sprintf('%02d', array_shift($parts));
            $scope = strtoupper(array_shift($parts));
            $file = array_pop($parts);
            $ext = substr($file, strrpos($file, '.') + 1);
            $path = implode('/', $parts);

            $filePath =($path) ? $path.'/'.$file : $file;

            $output[] = $s."{{file|[[{$this->baseUriHW}/Teil_$chapter#$scope/$filePath|$file]]|$ext}}";
        }//foreach

        return $output;
    }//function

    private function treeFileCount($items)
    {
        $i = 0;

        foreach ($items as $item)
        {
            if( ! is_array($item))
            $i ++;
        }//foreach

        return $i;
    }//function

    private function treeDetectLevel($levels, $fileCount = 0, $folderCount = 0)
    {
        $s = '';
        $i = 1;

        foreach ($levels as $xxx => $l)
        {
            if($l > 1)
            {
                if($i == count($levels))
                {
                    if($fileCount)
                    {
                        if($fileCount == $l)
                        {
                            $s .= '{{tree|L}}';
                        }
                        else
                        {
                            $s .= '{{tree|T}}';
                        }
                    }
                    else
                    {
                        $s .= '{{tree|T}}';
                    }
                }
                else
                {
                    if($folderCount)
                    {
                        if($folderCount == $l)
                        {
                            $s .= '{{tree|L}}';
                        }
                        else
                        {
                            $s .= '{{tree|V}}';
                        }
                    }
                    else
                    {
                        $s .= '{{tree|V}}';
                    }
                }
            }
            else
            {
                if($i == count($levels))
                {
                    $s .= '{{tree|L}}';

                }
                else
                {
                    $s .= '{{tree|S}}';
                }
            }

            $i ++;
        }//foreach

        return $s;
    }
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
