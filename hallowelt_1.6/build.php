<?php
/**
 * @version SVN: $Id$
 * @package    HalloWelt 1.6
 * @subpackage Base
 * @author     Created on 26-Oct-2010
 * @license    GNU/GPL
 */

require_once 'fileinfo.php';

define('DS', DIRECTORY_SEPARATOR);

define('BR', '<br />');

define('ROOT_PATH', str_replace('/', DS, dirname($_SERVER['SCRIPT_FILENAME'])));
//var_dump(ROOT_PATH);
define('PATH_SOURCES', ROOT_PATH.DS.'sources');
define('PATH_BUILD', ROOT_PATH.DS.'builds');

?>
<html>
<head>
<title>HW Builder</title>
<style>
b.src {
	color: blue;
}

b.dest {
	color: maroon;
}

body {
	background-image: -moz-radial-gradient(50% 50% 360deg, circle cover, #949494, #C9C9C9,
		#C7C7C7 75%, #888999 100%);
}

a:link,a:visited {
	color: blue;
	font-weight: bold;
	text-decoration: none;
	display: block;
	padding: 0.3em;
}

a:hover {
	background-color: #ffc;
}

a.create {
	color: green;
}

a.remove {
	color: red;
}

a.kuku {
	display: inline;
	color: black;
}

tr.row:hover {
	background-color: #eee;
}

td.cell {
	text-align: center;
}

th.cell {
	text-align: left;
}
</style>
</head>
<body>
	<h1>HW Builder</h1>

	<p>
		<b class="dest">ROOT: <?php echo ROOT_PATH; ?> </b>
	</p>
	<?php

	if( ! class_exists('ZipArchive'))
	{
	    exit('ba');
	}


	$projects = getProjects();

	foreach ($projects as $num => $projectPath)
	{
	    echo '<h1>Project: '.$num.'</h1>';

	    $symlinkList = getSyms($num);

	    if( ! $symlinkList)
	    {
	        echo 'LinkList not found :(';

	        continue;
	    }

	    $zip = new ZipArchive;
	    $filename = PATH_BUILD.DS.'hallowelt_teil_'.$num.'.zip';

	    if(file_exists($filename))
	    {
	        unlink($filename);
	    }

	    if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
	        exit("cannot open <$filename>\n");
	    }


	    FileInfo::deleteDir(PATH_BUILD.DS.$num);

	    foreach($symlinkList as $path)
	    {
	        $parts = explode(DS, $path);
	        array_shift($parts);
	        $dst = implode(DS, $parts);

	        FileInfo::copy(PATH_SOURCES.DS.$path, PATH_BUILD.DS.$num.DS.$dst);

	        $zip->addFile(PATH_SOURCES.DS.$path, $dst);
	    }

	    //-- copy XML
	    $xmlPath = $num.DS.'admin'.DS.'hallowelt.xml';
	    FileInfo::copy(PATH_SOURCES.DS.$xmlPath, PATH_BUILD.DS.$xmlPath);
	    $zip->addFile(PATH_SOURCES.DS.$xmlPath, 'hallowelt.xml');

	    echo sprintf(BR.'ZIP: %d files, Status: %d'.BR, $zip->numFiles, $zip->status);
	    $zip->close();

	    echo '<hr />';
	}
	?>
	<p>
		<small>Just in case: This is @license GPL &bull; <a class="kuku"
			href="http://joomlacode.org/gf/project/elkuku">El KuKu</a> <tt>=;)</tt>
		</small>
	</p>

</body>
</html>

	<?php
	//########################################################################################
	//#############################  FUNCTIONS  ##############################################
	//########################################################################################

	function getProjects()
	{
	    $paths = array();

	    $dir = new DirectoryIterator(ROOT_PATH.DS.'sources');

	    foreach ($dir as $fileinfo) {
	        if (!$fileinfo->isDot()) {
	            //        var_dump($fileinfo->getFilename());
	            $path = ROOT_PATH.DS.'sources'.DS.$fileinfo->getFilename();

	            if(file_exists($path))
	            {
	                $paths[$fileinfo->getFilename()] = $path;
	            }
	        }
	    }

	    //var_dump($paths);
	    return $paths;
	}

	function getSyms($projectDir)
	{
	    $filename = PATH_SOURCES.DS.$projectDir.DS.'links';

	    if( ! file_exists($filename))
	    {
	        return false;
	    }

	    $lines = file(PATH_SOURCES.DS.$projectDir.DS.'links');

	    $syms = array();
	    $base = '';

	    foreach($lines as $lNo => $line)
	    {
	        $line = trim($line);

	        //-- Strip blanks and comments
	        if(false == $line
	        || strpos($line, '#') === 0)
	        continue;

	        $links[] = trim(str_replace('/', DS, $line));
	    }//foreach

	    return $links;
	}//function

