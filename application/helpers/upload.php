<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Upload helper class for working with the global $_FILES
 * array and Validation library.
 *
 * $Id: upload.php 3264 2008-09-23 19:03:14Z David Kobia $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class upload_Core {

	/**
	 * Save an uploaded file to a new location.
	 *
	 * @param   mixed    name of $_FILE input or array of upload data
	 * @param   string   new filename
	 * @param   string   new directory
	 * @param   integer  chmod mask
	 * @return  string   full path to new file
	 */
	public static function save($file, $filename = NULL, $directory = NULL, $chmod = 0644)
	{
		// Load file data from FILES if not passed as array
		$file = is_array($file) ? $file : $_FILES[$file];

		if ($filename === NULL)
		{
			// Use the default filename, with a timestamp pre-pended
			$filename = time().(is_array($file['name']) ? $file['name'][0] : $file['name']);
		}

		if (Kohana::config('upload.remove_spaces') === TRUE)
		{
			// Remove spaces from the filename
			$filename = preg_replace('/\s+/', '_', $filename);
		}

		if ($directory === NULL)
		{
			// Use the pre-configured upload directory
			$directory = Kohana::config('upload.directory', TRUE);
		}

		// Make sure the directory ends with a slash
		$directory = rtrim($directory, '/').'/';

		if ( ! is_dir($directory) AND Kohana::config('upload.create_directories') === TRUE)
		{
			// Create the upload directory
			mkdir($directory, 0777, TRUE);
		}

		if ( ! is_writable($directory))
			throw new Kohana_Exception('upload.not_writable', $directory);
		
		// loop through if tmp_name returns an array
		if( is_array( $file['tmp_name'] ) ) {
			$i = 0;
			$filenames = array();
			foreach( $file['tmp_name'] as $tmp_name ) { 
				if (is_uploaded_file($tmp_name ) AND 
				move_uploaded_file($tmp_name, $filename = 
					$directory.$file['name'][$i] ) ) 
				{		
					if ($chmod !== FALSE)
					{
						// Set permissions on filename
						chmod( $filename, $chmod );
					}
					
					// Add $filename to $filenames array
					$filenames[] = $filename;
				}
				$i++;
			}
			
			// Return new file path array
			return $filenames;
		}
		else
		{
			if (is_uploaded_file($file['tmp_name']) AND move_uploaded_file($file['tmp_name'], $filename = $directory.$filename))
			{
				if ($chmod !== FALSE)
				{
					// Set permissions on filename
					chmod($filename, $chmod);
				}

				// Return new file path
				return $filename;
			}
		}

		return FALSE;
	}

	/* Validation Rules */

	/**
	 * Tests if input data is valid file type, even if no upload is present.
	 *
	 * @param   array  $_FILES item
	 * @return  bool
	 */
	public static function valid($file)
	{
		if (is_array($file))
		{
			// Is this a multi-upload array?
			if (is_array($file['name']))
			{
				for ($i=0; $i <= count($file['name']) ; $i++) 
				{ 
					if (isset($file['error'][$i])
						AND isset($file['name'][$i])
						AND isset($file['type'][$i])
						AND isset($file['tmp_name'][$i])
						AND isset($file['size'][$i]))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			}
			// No - this is a single upload
			else
			{
				return (isset($file['error'])
					AND isset($file['name'])
					AND isset($file['type'])
					AND isset($file['tmp_name'])
					AND isset($file['size']));
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Tests if input data has valid upload data.
	 *
	 * @param   array    $_FILES item
	 * @return  bool
	 */
	public static function required(array $file)
	{
		if (is_array($file['name']))
		{
			for ($i=0; $i <= count($file['name']) ; $i++) 
			{ 
				if (isset($file['tmp_name'][$i])
					AND isset($file['error'][$i])
					AND is_uploaded_file($file['tmp_name'][$i])
					AND (int) $file['error'][$i] === UPLOAD_ERR_OK)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		// This is a single upload
		else
		{
			return (isset($file['tmp_name'])
				AND isset($file['error'])
				AND is_uploaded_file($file['tmp_name'])
				AND (int) $file['error'] === UPLOAD_ERR_OK);
		}
	}

	/**
	 * Validation rule to test if an uploaded file is allowed by extension.
	 *
	 * @param   array    $_FILES item
	 * @param   array    allowed file extensions
	 * @return  bool
	 */
	public static function type(array $file, array $allowed_types)
	{
		if (is_array($file['name']))
		{
			for ($i=0; $i <= count($file['name']) ; $i++) 
			{ 
				if ((int) $file['error'][$i] !== UPLOAD_ERR_OK)
				{
					return TRUE;
				}
				
				// Get the default extension of the file
				$extension = strtolower(substr(strrchr($file['name'][$i], '.'), 1));

				// Get the mime types for the extension
				$mime_types = Kohana::config('mimes.'.$extension);

				// Make sure there is an extension, that the extension is allowed, and that mime types exist
				if ( ! empty($extension) AND in_array($extension, $allowed_types) AND is_array($mime_types))
				{
					return TRUE;
				}
				else
				{
					return false;
				}
			}
		}
		// This is a single upload
		else
		{
			if ((int) $file['error'] !== UPLOAD_ERR_OK)
				return TRUE;

			// Get the default extension of the file
			$extension = strtolower(substr(strrchr($file['name'], '.'), 1));

			// Get the mime types for the extension
			$mime_types = Kohana::config('mimes.'.$extension);

			// Make sure there is an extension, that the extension is allowed, and that mime types exist
			return ( ! empty($extension) AND in_array($extension, $allowed_types) AND is_array($mime_types));
		}
	}

	/**
	 * Validation rule to test if an uploaded file is allowed by file size.
	 * File sizes are defined as: SB, where S is the size (1, 15, 300, etc) and
	 * B is the byte modifier: (B)ytes, (K)ilobytes, (M)egabytes, (G)igabytes.
	 * Eg: to limit the size to 1MB or less, you would use "1M".
	 *
	 * @param   array    $_FILES item
	 * @param   array    maximum file size
	 * @return  bool
	 */
	public static function size(array $file, array $size)
	{
		if (is_array($file['name']))
		{
			for ($i=0; $i <= count($file['name']) ; $i++) 
			{
				if ((int) $file['error'][$i] !== UPLOAD_ERR_OK)
				{
					return TRUE;
				}

				// Only one size is allowed
				$size = strtoupper($size[0]);

				if ( ! preg_match('/[0-9]++[BKMG]/', $size))
				{
					return FALSE;
				}

				// Make the size into a power of 1024
				switch (substr($size, -1))
				{
					case 'G': $size = intval($size) * pow(1024, 3); break;
					case 'M': $size = intval($size) * pow(1024, 2); break;
					case 'K': $size = intval($size) * pow(1024, 1); break;
					default:  $size = intval($size);                break;
				}

				// Test that the file is under or equal to the max size
				if ($file['size'][$i] <= $size)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		// This is a single upload
		else
		{
			if ((int) $file['error'] !== UPLOAD_ERR_OK)
				return TRUE;

			// Only one size is allowed
			$size = strtoupper($size[0]);

			if ( ! preg_match('/[0-9]++[BKMG]/', $size))
				return FALSE;

			// Make the size into a power of 1024
			switch (substr($size, -1))
			{
				case 'G': $size = intval($size) * pow(1024, 3); break;
				case 'M': $size = intval($size) * pow(1024, 2); break;
				case 'K': $size = intval($size) * pow(1024, 1); break;
				default:  $size = intval($size);                break;
			}

			// Test that the file is under or equal to the max size
			return ($file['size'] <= $size);
		}
	}

} // End upload
