<?php
/**
 * @package     HalloWelt
 * @subpackage  Base
 * @author      Joomla-wiki.de <kontakt@joomla-wiki.de>
 * @copyright   2012 Joomla-wiki.de
 * @license     GNU/GPL http://gnu.org
 */

define('DS', DIRECTORY_SEPARATOR);

define('BR', '<br />');

define('ROOT_PATH', str_replace('/', DS, dirname($_SERVER['SCRIPT_FILENAME'])));
define('PATH_SOURCES', ROOT_PATH . DS . 'sources');
define('PATH_BUILD', ROOT_PATH . DS . 'builds');

require_once 'fileinfo.php';

?>
	<html>
	<head>
		<title>HW Builder</title>
		<style>
			body {
				background-image: -moz-radial-gradient(50% 50% 360deg, circle cover, #949494, #C9C9C9, #C7C7C7 75%, #888999 100%);
			}

			a:hover {
				background-color: #ffc;
			}

			a.kuku {
				display: inline;
				color: black;
			}
		</style>
	</head>
	<body>
	<h1>HW Builder</h1>

	<p>
		<b>ROOT: <?php echo ROOT_PATH; ?> </b>
	</p>
	<?php

	if (!class_exists('ZipArchive'))
		exit('No zip support :(');

	$projects = getProjects();

	foreach ($projects as $num => $projectPath)
	{
		echo 'Project: ' . $num . '...';

		$symlinkList = getSyms($num);

		if (!$symlinkList)
		{
			echo 'LinkList not found :(' . BR;

			continue;
		}

		$zip = new ZipArchive;
		$filename = PATH_BUILD . DS . 'hallowelt_teil_' . $num . '.zip';

		if (file_exists($filename))
		{
			unlink($filename);
		}

		if (true !== $zip->open($filename, ZIPARCHIVE::CREATE))
			exit('cannot open <' . $filename . '>');

		FileInfo::deleteDir(PATH_BUILD . DS . $num);

		foreach ($symlinkList as $path)
		{
			$parts = explode(DS, $path);

			array_shift($parts);

			$dst = implode(DS, $parts);

			FileInfo::copy(PATH_SOURCES . DS . $path, PATH_BUILD . DS . $num . DS . $dst);

			$zip->addFile(PATH_SOURCES . DS . $path, $dst);
		}

		// copy XML
		$xmlPath = $num . DS . 'admin' . DS . 'hallowelt.xml';

		FileInfo::copy(PATH_SOURCES . DS . $xmlPath, PATH_BUILD . DS . $xmlPath);

		$zip->addFile(PATH_SOURCES . DS . $xmlPath, 'hallowelt.xml');

		echo sprintf('ZIPed: %d files, Status: %d' . BR, $zip->numFiles, $zip->status);

		$zip->close();
	}
	?>

	<h1 style="color: green;">Success !</h1>

	<p>
		<b>The files are in: <?php echo PATH_BUILD; ?> </b>
	</p>

	<p>
		<small>Just in case: This is @license GPL and made by <a class="kuku"
		                                                         href="http://joomlacode.org/gf/project/elkuku">El
				KuKu</a> <tt>=;)</tt>
		</small>
	</p>

	</body>
	</html>

<?php

/**
 * ########################################################################################
 * #############################  FUNCTIONS  ##############################################
 * ########################################################################################
 */

/**
 * Get the projects.
 *
 * @return array
 */
function getProjects()
{
	$paths = array();

	foreach (new DirectoryIterator(ROOT_PATH . DS . 'sources') as $fileinfo)
	{
		if (!$fileinfo->isDot()
			&& '.svn' != $fileinfo->getFilename())
		{
			$path = ROOT_PATH . DS . 'sources' . DS . $fileinfo->getFilename();

			if (file_exists($path))
			{
				$paths[$fileinfo->getFilename()] = $path;
			}
		}
	}

	return $paths;
}

/**
 * Get the project list.
 *
 * @param   string  $projectDir  The project directory.
 *
 * @return array|bool
 */
function getSyms($projectDir)
{
	$filename = PATH_SOURCES . DS . $projectDir . DS . 'links';

	if (!file_exists($filename))
		return false;

	$lines = file($filename);
	$links = array();

	foreach ($lines as $lNo => $line)
	{
		$line = trim($line);

		// Strip blanks and comments
		if (false == $line
			|| strpos($line, '#') === 0)
			continue;

		$links[] = trim(str_replace('/', DS, $line));
	}

	return $links;
}
