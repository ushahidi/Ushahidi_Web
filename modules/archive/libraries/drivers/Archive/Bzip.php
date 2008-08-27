<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Archive library bzip driver.
 *
 * $Id: Bzip.php 3138 2008-07-17 14:40:27Z Shadowhand $
 *
 * @package    Archive
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Archive_Bzip_Driver implements Archive_Driver {

	public function create($paths, $filename = FALSE)
	{
		$archive = new Archive('tar');

		foreach ($paths as $set)
		{
			$archive->add($set[0], $set[1]);
		}

		$gzfile = bzcompress($archive->create());

		if ($filename == FALSE)
		{
			return $gzfile;
		}

		if (substr($filename, -8) !== '.tar.bz2')
		{
			// Append tar extension
			$filename .= '.tar.bz2';
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

} // End Archive_Bzip_Driver Class