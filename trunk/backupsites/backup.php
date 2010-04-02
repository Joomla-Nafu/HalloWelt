<?php
/**
 * @version SVN $Id$
 * @version $HeadURL$
 *
 * Backup script to backup Joomla! installations - Files and database.
 *
 * @author Nikolai Plath
 * @author created on 1-Apr-2010 -- NoJoke =;)
 * @license GPL
 */

/**
 * This contains the path to your backup directory.
 * The file 'backupsites' is expected in this directory.
 * You may customize this to any custom directory or
 * override it with the command line parameter --backupdirs.
 *
 * @var string Absolute path to backup directory.
 */
$backupDir = dirname(__FILE__);

/**
 * Directory searator
 * @var string
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

$backup = new Backup($backupDir);

//-- Execute the backup
$ret = $backup->run();

if( ! $ret)
{
    //-- Bad things happened...
}

//-- That's all =;)

class MInterface
{
    /**
     * True if run from CLI
     * @var boolean
     */
    protected $isCLI = false;

    /**
     * Arguments from the command line or $_GET
     * @var object Args
     */
    protected $Args = null;

    public function __construct()
    {
        $this->isCLI =(substr(php_sapi_name(), 0, 3) == 'cli') ? true : false;

        if($this->isCLI)
        {
            $this->Args = new Args();

            /**
             * Line break for CLI output
             * @var string
             */
            define('BR', "\n");

            return;
        }

        //-- Prepare for browser output

        //-- Translate $_GET args to $GLOBALS['argv'] format
        $gets = array('dummy');

        foreach($_GET as $getVar => $getVal)
        {
            $gets[] =($getVal) ? "--$getVar=$getVal" : $getVar;
        }//foreach

        $this->Args = new Args($gets);

        /**
         * Line break for browser output
         * @var string
         */
        define('BR', "<br />\n");
    }//function

}//class

/**
 * Makes Joomla! backups.
 *
 * @return boolean true on success =;)
 */
class Backup extends MInterface
{
    private $backupDir = '';

    /**
     * Testing mode
     * @var boolean
     */
    private $testing = false;

    private $verbose = false;

    private $excludeDirs = array();
    private $excludeFiles = array();

    /**
     * Constructor.
     */
    public function __construct($backupDir)
    {
        parent::__construct();

        //-- Global args
        $args = $this->Args;
        if($args->arg('test') || $args->arg('t') || $args->flag('test')) $args->testing = true;
        if($args->arg('verbose') || $args->arg('v') || $args->flag('verbose')) $args->verbose = true;

        $this->backupDir =($args->flag('backup-dir')) ? $args->flag('backup-dir') : $backupDir;
        $this->tmpDir = $this->backupDir.DS.'temp';
    }//function

    /**
     * Run the backups.
     */
    public function run()
    {
        echo 'Welcome =;)'.BR;

        if( ! file_exists($this->backupDir.DS.'backupsites'))
        {
            echo 'File not found: '.BR;
            echo $this->backupDir.DS.'backupsites'.BR;
            echo '*** ABORT... :(';

            return false;
        }

        $lines = file($this->backupDir.DS.'backupsites');

        foreach($lines as $line)
        {
            $line = trim($line);

            if( ! $line) continue;
            if(strpos($line, '#') === 0) continue;

            $cc = explode(' ', $line);

            //-- Remove trailing slash
            if(strrpos($cc[0], DS) == strlen($cc[0]) - 1) $cc[0] = substr($cc[0], 0, strlen($cc[0]) - 1);
            $siteToBackup = $cc[0];

            $cs = explode(DS, $siteToBackup);
            $this->subDir = $cs[count($cs) - 1];

            //-- Are there arguments ?
            $this->parseArgs(new Args($cc));

            echo BR;
            echo  str_repeat('*', 70).BR;
            echo 'Processing...'.$siteToBackup.BR;

            //-- Read Joomla! configuration.php
            $configPath = $siteToBackup.DS.'configuration.php';

            if( ! file_exists($configPath))
            {
                echo 'Joomla! config file not found in path: '.BR.$configPath.BR;
                echo '*** ABORT... !!'.BR;

                continue;
            }

            $JConfig = new stdClass();

            $lines = file($configPath);

            foreach($lines as $line)
            {
                $line = trim($line);

                if( ! $line) continue;

                if(preg_match("%var\s\\$([A-z0-9]+) = '([A-z0-9/.:@-\s]*)';%i", $line, $matches))
                {
                    $JConfig->$matches[1] = $matches[2];
                }
            }//foreach

            //-- Clean the name
            $fileName = $JConfig->sitename;
            $fileName = str_replace(' ', '_', $fileName);
            $fileName = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $fileName);
            $fileName .= '_'.date('Ymd_His');

            if( ! $this->testing)
            {
                if( ! is_dir($this->tmpDir)) mkdir($this->tmpDir);
                echo 'Created dir: '.$this->tmpDir.BR;
            }
            else
            {
                echo 'TEST temp dir: '.$this->tmpDir.BR;
            }

            echo 'Starting MySqlDump...';
            $cmd = 'mysqldump';
            $cmd .=($this->verbose) ? ' -v' : '';
            $cmd .= ' -u '.$JConfig->user;
            $cmd .=($JConfig->password) ? ' -p '.$JConfig->password : '';
            $cmd .= ' '.$JConfig->db.' 2>&1 >';
            $cmd .= ' '.$this->tmpDir.DS.'backup.sql';
            $ret =($this->testing) ? 'TEST: '.$cmd : shell_exec($cmd);
            if($ret) echo ($this->isCLI) ? $ret : '<pre>'.$ret.'</pre>';
            echo ' done.'.BR;

            echo 'Copying files...';
            $cmd = 'cp -R';
            $cmd .=($this->verbose) ? ' -v' : '';
            $cmd .= ' '.$siteToBackup;
            $cmd .= ' '.$this->tmpDir.' 2>&1';
            $ret =($this->testing) ? 'TEST: '.$cmd : shell_exec($cmd);
            if($ret) echo ($this->isCLI) ? $ret : '<pre>'.$ret.'</pre>';
            echo ' done.'.BR;

            $this->processArgs();

            echo 'Creating archive...';
            $cmd = 'tar czf';
            $cmd .=($this->verbose) ? 'v' : '';
            $cmd .= ' '.$this->backupDir.DS.$fileName.'.tgz';
            $cmd .= ' -C '.$this->backupDir.DS.' temp';
            $cmd .= ' 2>&1';
            $ret =($this->testing) ? 'TEST: '.$cmd : shell_exec($cmd);
            if ($ret) echo ($this->isCLI) ? $ret : '<pre>'.$ret.'</pre>';
            echo ' done.'.BR;

            echo 'Cleaning up...';
            $cmd = 'rm -Rf';
            $cmd .=($this->verbose) ? 'v' : '';
            $cmd .= ' '.$this->tmpDir.DS.'*';
            $ret =($this->testing) ? 'TEST: '.$cmd : shell_exec($cmd);
            echo ($ret) ? ($this->isCLI) ? $ret : '<pre>'.$ret.'</pre>' : '';
            echo ' done.'.BR;

            echo 'File has been written to:'.BR;
            echo $this->backupDir.DS.$fileName.'.tgz'.BR;
            if( ! $this->testing) echo self::byte_convert(filesize($this->backupDir.DS.$fileName.'.tgz')).BR;
        }//foreach

        echo BR;
        echo  str_repeat('*', 70).BR;
        echo '***** F I N I S H E D   =;)'.BR;
        echo  str_repeat('*', 70).BR;

        return true;
    }//function

    /**
     * Parse arguments.
     *
     * @param object Args $args
     *
     * @return void
     */
    private function parseArgs(Args $args)
    {
        $mFlags = array(
          'excludeDirs' => array('exclude-dirs', 'XD')
        , 'excludeFiles' => array('exclude-files', 'XF')
        );

        foreach($mFlags as $name => $flags)
        {
            $this->$name = array();

            foreach($flags as $flag)
            {
                $arg = $args->flag($flag);
                if($arg) $this->$name = explode(',', $arg);
            }//foreach
        }//foreach

        return;
    }//function

    /**
     * Process arguments.
     *
     * @return void
     */
    private function processArgs()
    {
        //-- Process arguments
        $workDir = $this->tmpDir.DS.$this->subDir;

        if(count($this->excludeDirs))
        {
            echo 'Removing excluded directories...';

            foreach ($this->excludeDirs as $excludeDir)
            {
                if( ! is_dir($workDir.DS.$excludeDir))
                {
                    echo BR;
                    echo ($this->testing) ? 'Exclude directory: ' : '***Exclude directory not found: ';
                    echo $excludeDir.BR;

                    continue;
                }

                echo $excludeDir.' - ';

                $cmd = 'rm -Rf '.$workDir.DS.$excludeDir;

                $ret =($this->testing) ? 'TEST: '.$cmd : shell_exec($cmd);

                if($ret) echo ($this->isCLI) ? $ret : '<pre>'.$ret.'</pre>';
            }//foreach

            echo 'done'.BR;
        }

        if(count($this->excludeFiles))
        {
            echo 'Removing excluded files...';

            foreach ($this->excludeFiles as $excludeDir)
            {
                if( ! file_exists($workDir.DS.$excludeDir))
                {
                    echo BR;
                    echo ($this->testing) ? 'Exclude file: ' : '***Exclude file not found: ';
                    echo $workDir.DS.$excludeDir.BR;

                    continue;
                }

                echo $excludeDir.' - ';

                $cmd = 'rm -f '.$workDir.DS.$excludeDir;

                $ret =($this->testing) ? 'TEST: '.$cmd : shell_exec($cmd);

                if($ret) echo ($this->isCLI) ? $ret : '<pre>'.$ret.'</pre>';
            }//foreach

            echo 'done'.BR;
        }

        return;
    }//function

    /**
     * converts a bytevalue into the highest possible unit and adds it's sign.
     * @version  2009-01-27 03:50h
     *
     * @param    bigint|float  $bytes    -bytevalue to convert
     * @param    int           $exp_max  -maximal allowed exponent (0='B', 1='KB', 2='MB', ...)
     *
     * @return   string
     */
    private static function byte_convert($bytes, $exp_max = null)
    {
        $symbols = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        $exp = 0;

        if($exp_max === null) $exp_max = count($symbols)-1;

        $converted_value = 0;

        if($bytes > 0)
        {
            $exp = floor(log($bytes)/log(1024));
            if($exp > $exp_max) $exp = $exp_max;
            $converted_value = $bytes / pow(1024, $exp);
        }

        return number_format($converted_value, 2, ',', '.').' '.$symbols[$exp];
    }//function

}//class

/**
 * Parse command line arguments
 *
 * @author http://code.google.com/p/tylerhall/source/browse/trunk/class.args.php
 * @author http://clickontyler.com/blog/2008/11/parse-command-line-arguments-in-php/
 *
 * @param array $args
 *
 * @return array
 */
class Args
{
    private $flags;
    private $args;

    public function __construct($args = null)
    {
        $this->flags = array();
        $this->args  = array();

        $argv =(is_null($args)) ? $GLOBALS['argv'] : $args;

        if( ! is_array($argv)) return array();

        array_shift($argv);

        for($i = 0; $i < count($argv); $i++)
        {
            $str = $argv[$i];

            // --foo
            if(strlen($str) > 2 && substr($str, 0, 2) == '--')
            {
                $str = substr($str, 2);
                $parts = explode('=', $str);
                $this->flags[$parts[0]] = true;

                // Does not have an =, so choose the next arg as its value
                if(count($parts) == 1 && isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0)
                {
                    $this->flags[$parts[0]] = $argv[$i + 1];
                }
                elseif(count($parts) == 2) // Has a =, so pick the second piece
                {
                    $this->flags[$parts[0]] = $parts[1];
                }
            }
            elseif(strlen($str) == 2 && $str[0] == '-') // -a
            {
                $this->flags[$str[1]] = true;
                if(isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0)
                $this->flags[$str[1]] = $argv[$i + 1];
            }
            elseif(strlen($str) > 1 && $str[0] == '-') // -abcdef
            {
                for($j = 1; $j < strlen($str); $j++)
                $this->flags[$str[$j]] = true;
            }
        }

        for($i = count($argv) - 1; $i >= 0; $i--)
        {
            if(preg_match('/^--?.+/', $argv[$i]) == 0)
            $this->args[] = $argv[$i];
            else
            break;
        }

        $this->args = array_reverse($this->args);
    }//function

    public function flag($name)
    {
        return isset($this->flags[$name]) ? $this->flags[$name] : false;
    }//function

    public function arg($name)
    {
        return in_array($name, $this->args) ? true : false;
    }//function

    public function getArgs()
    {
        return $this->args;
    }//function

}//class
