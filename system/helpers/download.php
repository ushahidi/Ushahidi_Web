<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Download helper class.
 *
 * $Id: download.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class download_Core {

	/**
	 * Force a download of a file to the user's browser. This function is
	 * binary-safe and will work with any MIME type that Kohana is aware of.
	 *
	 * @param   string  a file path or file name
	 * @param   mixed   data to be sent if the filename does not exist
	 * @param   string  suggested filename to display in the download
	 * @return  void
	 */
	public static function force($filename = NULL, $data = NULL, $nicename = NULL)
	{
		if (empty($filename))
			return FALSE;

		if (is_file($filename))
		{
			// Get the real path
			$filepath = str_replace('\\', '/', realpath($filename));

			// Set filesize
			$filesize = filesize($filepath);

			// Get filename
			$filename = substr(strrchr('/'.$filepath, '/'), 1);

			// Get extension
			$extension = strtolower(substr(strrchr($filepath, '.'), 1));
		}
		else
		{
			// Get filesize
			$filesize = strlen($data);

			// Make sure the filename does not have directory info
			$filename = substr(strrchr('/'.$filename, '/'), 1);

			// Get extension
			$extension = strtolower(substr(strrchr($filename, '.'), 1));
		}

		// Get the mime type of the file
		$mime = Kohana::config('mimes.'.$extension);

		if (empty($mime))
		{
			// Set a default mime if none was found
			$mime = array('application/octet-stream');
		}

		// Generate the server headers
		header('Content-Type: '.$mime[0]);
		header('Content-Disposition: attachment; filename="'.(empty($nicename) ? $filename : $nicename).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.sprintf('%d', $filesize));

		// More caching prevention
		header('Expires: 0');

		if (Kohana::user_agent('browser') === 'Internet Explorer')
		{
			// Send IE headers
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			// Send normal headers
			header('Pragma: no-cache');
		}

		// Clear the output buffer
		Kohana::close_buffers(FALSE);

		if (isset($filepath))
		{
			// Open the file
			$handle = fopen($filepath, 'rb');

			// Send the file data
			fpassthru($handle);

			// Close the file
			fclose($handle);
		}
		else
		{
			// Send the file data
			echo $data;
		}
	}

} // End download