#!/opt/lampp/bin/php
<?php
/**
 * @version SVN $Id$
 */

defined('NL') || define('NL', "\n");

echo '* KuKuKleaner *'.NL;

if(count($argv) < 3)
die('Usage: kukukleaner.php file path'.NL);

$path = $argv[1];
$toClean = $argv[2];

echo 'Input file   : '.$path.NL;
echo 'Path to clean: '.$toClean.NL;

if( ! file_exists($path))
die('File not found: '.$path);

$contents = file_get_contents($path);

$contents = str_replace($toClean, '', $contents);

$h = fopen($path, 'w');
fwrite($h, $contents);
fclose($h);

echo 'Finished =;)'.NL.NL;
