<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Archive library zip driver.
 *
 * $Id: Zip.php 2007 2008-02-09 06:37:51Z PugFish $
 *
 * @package    Archive
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Archive_Zip_Driver implements Archive_Driver {

	// Compiled directory structure
	protected $dirs = '';

	// Compiled archive data
	protected $data = '';

	// Offset location
	protected $offset = 0;

	public function create($paths, $filename = FALSE)
	{
		// Sort the paths to make sure that directories come before files
		sort($paths);

		foreach ($paths as $set)
		{
			// Add each path individually
			$this->add_data($set[0], $set[1], isset($set[2]) ? $set[2] : NULL);
		}

		// File data
		$data = implode('', $this->data);

		// Directory data
		$dirs = implode('', $this->dirs);

		$zipfile =
			$data.                              // File data
			$dirs.                              // Directory data
			"\x50\x4b\x05\x06\x00\x00\x00\x00". // Directory EOF
			pack('v', count($this->dirs)).      // Total number of entries "on disk"
			pack('v', count($this->dirs)).      // Total number of entries in file
			pack('V', strlen($dirs)).           // Size of directories
			pack('V', strlen($data)).           // Offset to directories
			"\x00\x00";                         // Zip comment length

		if ($filename == FALSE)
		{
			return $zipfile;
		}

		if (substr($filename, -3) != 'zip')
		{
			// Append zip extension
			$filename .= '.zip';
		}

		// Create the file in binary write mode
		$file = fopen($filename, 'wb');

		// Lock the file
		flock($file, LOCK_EX);

		// Write the zip file
		$return = fwrite($file, $zipfile);

		// Unlock the file
		flock($file, LOCK_UN);

		// Close the file
		fclose($file);

		return (bool) $return;
	}

	public function add_data($file, $name, $contents = NULL)
	{
		// Determine the file type: 16 = dir, 32 = file
		$type = (substr($file, -1) === '/') ? 16 : 32;

		// Fetch the timestamp, using the current time if manually setting the contents
		$timestamp = date::unix2dos(($contents === NULL) ? filemtime($file) : time());

		// Read the file or use the defined contents
		$data = ($contents === NULL) ? file_get_contents($file) : $contents;

		// Gzip the data, use substr to fix a CRC bug
		$zdata = substr(gzcompress($data), 2, -4);

		$this->data[] =
			"\x50\x4b\x03\x04".       // Zip header
			"\x14\x00".               // Version required for extraction
			"\x00\x00".               // General bit flag
			"\x08\x00".               // Compression method
			pack('V', $timestamp).    // Last mod time and date
			pack('V', crc32($data)).  // CRC32
			pack('V', strlen($zdata)).// Compressed filesize
			pack('V', strlen($data)). // Uncompressed filesize
			pack('v', strlen($name)). // Length of file name
			pack('v', 0).             // Extra field length
			$name.                    // File name
			$zdata;                   // Compressed data

		$this->dirs[] =
			"\x50\x4b\x01\x02".       // Zip header
			"\x00\x00".               // Version made by
			"\x14\x00".               // Version required for extraction
			"\x00\x00".               // General bit flag
			"\x08\x00".               // Compression method
			pack('V', $timestamp).    // Last mod time and date
			pack('V', crc32($data)).  // CRC32
			pack('V', strlen($zdata)).// Compressed filesize
			pack('V', strlen($data)). // Uncompressed filesize
			pack('v', strlen($name)). // Length of file name
			pack('v', 0).             // Extra field length
			// End "local file header"
			// Start "data descriptor"
			pack('v', 0).             // CRC32
			pack('v', 0).             // Compressed filesize
			pack('v', 0).             // Uncompressed filesize
			pack('V', $type).         // File attribute type
			pack('V', $this->offset). // Directory offset
			$name;                    // File name

		// Set the new offset
		$this->offset = strlen(implode('', $this->data));
	}

} // End Archive_Zip_Driver Class