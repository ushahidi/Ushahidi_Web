<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Archive library gzip driver.
 *
 * $Id: Gzip.php 3138 2008-07-17 14:40:27Z Shadowhand $
 *
 * @package    Archive
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Archive_Gzip_Driver implements Archive_Driver {

	public function create($paths, $filename = FALSE)
	{
		$archive = new Archive('tar');

		foreach ($paths as $set)
		{
			$archive->add($set[0], $set[1]);
		}

		$gzfile = gzencode($archive->create());

		if ($filename == FALSE)
		{
			return $gzfile;
		}

		if (substr($filename, -7) !== '.tar.gz')
		{
			// Append tar extension
			$filename .= '.tar.gz';
		}

		// Create the file in binary write mode
		$file = fopen($filename, 'wb');

		// Lock the file
		flock($file, LOCK_EX);

		// Write the tar file
		$return = fwrite($file, $gzfile);

		// Unlock the file
		flock($file, LOCK_UN);

		// Close the file
		fclose($file);

		return (bool) $return;
	}

	public function add_data($file, $name, $contents = NULL)
	{
		return FALSE;
	}

} // End Archive_Gzip_Driver Class