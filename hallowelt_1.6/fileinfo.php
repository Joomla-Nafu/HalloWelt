<?php
/**
 * File info class.
 *
 * @version SVN: $Id$
 * @package    HalloWelt 1.6
 * @subpackage Base
 * @author     Created on 26-Oct-2010
 * @license    GNU/GPL
 */
class FileInfo
{
    public $path = '';

    public $isFile = false;

    public $isDir = false;

    public $isLink = false;

    /**
     * Get the file info.
     *
     * @param string $base Base path
     * @param string $path Path relative to base path
     * @param string $alias Optional alias
     *
     * @return return_type
     */
    public static function getInfo($base, $path, $alias = '')
    {
        $alias =($alias) ? $alias : $path;

        $obj = self::getInstance();
        $obj->path = $base.DS.$path;
        $obj->aliasPath = $base.DS.$alias;

        $obj->isDir = is_dir($obj->path);
        $obj->isFile = is_file($obj->path);
        $obj->isLink = is_link($obj->aliasPath);
        $obj->link =($obj->isLink) ? $base.DS.$alias : '';
        $obj->exists =($obj->isDir || $obj->isFile);

        return $obj;
    }//function

    private function getInstance()
    {
        return new FileInfo;
    }//function

    /**
     * Copies a file
     *
     * @param	string	$src	The path to the source file
     * @param	string	$dest	The path to the destination file
     * @param	string	$path	An optional base path to prefix to the file names
     *
     * @return	boolean	True on success
     * @since	1.5
     */
    public static function copy($src, $dest)
    {
        if( ! file_exists($src))
        {
            echo 'file not found: '.$src;

            return false;
        }

        $tmpDest = str_replace(ROOT_PATH.DS, '', $dest);

        $parts = explode(DS, $tmpDest);
        array_pop($parts);

        $p = ROOT_PATH;

        foreach ($parts as $part)
        {
            if( ! $part)
            continue;

            $p .= DS.$part;

            if( ! is_dir($p))
            {
                mkdir($p);
            }
        }

        copy($src, $dest);
    }//function


public static function deleteDir($directory, $empty=false)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return false;

	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return false;

	// ... else if the path is readable
	}else{

		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (false !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path))
				{
					// we call this function with the new path
					self::deleteDir($path);

				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == false)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return false;
			}
		}
		// return success
		return true;
	}
}

}//class
