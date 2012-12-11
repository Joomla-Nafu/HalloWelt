<?php
/**
 * @package     HalloWelt
 * @subpackage  Base
 * @author      Joomla-wiki.de <kontakt@joomla-wiki.de>
 * @copyright   2012 Joomla-wiki.de
 * @license     GNU/GPL http://gnu.org
 */

defined('ROOT_PATH') || die('This file should not be accessed directly :(');

/**
 * FileInfo class.
 *
 * @package    HalloWelt
 * @copyright  2012 Joomla-wiki.de
 * @since      1
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
	 * @param   string  $base   Base path
	 * @param   string  $path   Path relative to base path
	 * @param   string  $alias  Optional alias
	 *
	 * @return FileInfo
	 */
	public static function getInfo($base, $path, $alias = '')
	{
		$alias = ($alias) ? $alias : $path;

		$obj = self::getInstance();
		$obj->path = $base . DS . $path;
		$obj->aliasPath = $base . DS . $alias;

		$obj->isDir = is_dir($obj->path);
		$obj->isFile = is_file($obj->path);
		$obj->isLink = is_link($obj->aliasPath);
		$obj->link = ($obj->isLink) ? $base . DS . $alias : '';
		$obj->exists = ($obj->isDir || $obj->isFile);

		return $obj;
	}

	/**
	 * Get an instance.
	 *
	 * @return FileInfo
	 */
	private function getInstance()
	{
		return new FileInfo;
	}

	/**
	 * Copies a file.
	 *
	 * @param   string  $src   The path to the source file
	 * @param   string  $dest  The path to the destination file
	 *
	 * @return boolean
	 */
	public static function copy($src, $dest)
	{
		if (!file_exists($src))
		{
			echo 'file not found: ' . $src;

			return false;
		}

		$tmpDest = str_replace(ROOT_PATH . DS, '', $dest);

		$parts = explode(DS, $tmpDest);
		array_pop($parts);

		$p = ROOT_PATH;

		foreach ($parts as $part)
		{
			if (!$part)
				continue;

			$p .= DS . $part;

			if (!is_dir($p))
			{
				mkdir($p);
			}
		}

		copy($src, $dest);
	}

	/**
	 * Delete a directory.
	 *
	 * @param   string  $directory  The directory.
	 * @param   bool    $empty      Delete also empty directories.
	 *
	 * @return bool
	 */
	public static function deleteDir($directory, $empty = false)
	{
		// If the path has a slash at the end we remove it here
		if (substr($directory, -1) == '/')
		{
			$directory = substr($directory, 0, -1);
		}

		// If the path is not valid or is not a directory ...
		if (!file_exists($directory) || !is_dir($directory))
		{
			// ... we return false and exit the function
			return false;

			// ... if the path is not readable
		}
		elseif (!is_readable($directory))
		{
			// ... we return false and exit the function
			return false;

			// ... else if the path is readable
		}
		else
		{
			// We open the directory
			$handle = opendir($directory);

			// And scan through the items inside
			while (false !== ($item = readdir($handle)))
			{
				// If the filepointer is not the current directory
				// Or the parent directory
				if ($item != '.' && $item != '..')
				{
					// We build the new path to delete
					$path = $directory . '/' . $item;

					// If the new path is a directory
					if (is_dir($path))
					{
						// We call this function with the new path
						self::deleteDir($path);

						// If the new path is a file
					}
					else
					{
						// We remove the file
						unlink($path);
					}
				}
			}

			// Close the directory
			closedir($handle);

			// If the option to empty is not set to true
			if ($empty == false)
			{
				// Try to delete the now empty directory
				if (!rmdir($directory))
				{
					// Return false if not possible
					return false;
				}
			}

			// Return success
			return true;
		}
	}
}
