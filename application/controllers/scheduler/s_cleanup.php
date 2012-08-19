<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cleaup Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Scheduler
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class S_Cleanup_Controller extends Controller {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Call the various cleanup functions;
	 */
	public function index()
	{

		$this->remove_orphan_images();
		$this->remove_old_logs();

		return TRUE;
	}

	/**
	 * Deletes old log files older than # days defined in config
	 */
	public function remove_old_logs()
	{
		$days_old = Kohana::config('config.log_cleanup_days_old');

		// First check if we should even be doing this
		if ($days_old == FALSE OR ! is_int($days_old))
		{
			return FALSE;
		}

		$dir = Kohana::log_directory();

		if (is_dir($dir)
			AND is_writable($dir)
			AND $dh = opendir($dir))
		{
			$oldest_allowed = date('Y-m-d',mktime(0, 0, 0, date("m"), date("d")-$days_old, date("Y")));

			while ( ($file = readdir($dh)) !== false )
			{
				// If it's a hidden or system file, skip it
				if ($file{0} == '.')
				{
					continue;
				}

				// Strip off the file extension so we can just evaluate the date
				$date = str_ireplace('.log'.EXT, '', $file);

				if ($date <= $oldest_allowed)
				{
					// This file needs to be deleted.
					unlink($dir.$file);
				}
			}
			closedir($dh);
		}

		//$filename = $dir.date('Y-m-d').'.log'.EXT;
		//var_dump($filename);
	}

	/**
	 * This function helps cleanup orphaned images in the upload directory
	 * Orphans are usually a result of new reports that are never completed
	 */
	public function remove_orphan_images()
	{

		// open the images directory and create it if it's not there.
		if( ! is_dir(Kohana::config('upload.relative_directory')))
		{
			mkdir(Kohana::config('upload.relative_directory'), 0755);
		}

		$dhandle = opendir(Kohana::config('upload.relative_directory'));

		// define an array to hold the files
		$files = array();

		// Image Extensions
		$img_extensions = array(".jpg", ".gif", ".png");

		if ($dhandle)
		{
			// Get all the media files from the database so we can check if the file isn't orphaned

			$images = ORM::factory("media")->find_all();

			// Turn this into an array that we can easily check against

			$image_list = array();
			foreach($images as $image)
			{
				if($image->media_link != NULL) $image_list[] = $image->media_link;
				if($image->media_medium != NULL) $image_list[] = $image->media_medium;
				if($image->media_thumb != NULL) $image_list[] = $image->media_thumb;
			}

			// Get all the categroy image files from the database to add to the list

			$category_images = ORM::factory("category")->find_all();
			foreach($category_images as $image)
			{
				if($image->category_image != NULL) $image_list[] = $image->category_image;
				if($image->category_image_thumb != NULL) $image_list[] = $image->category_image_thumb;
			}

			// loop through all of the files
			while (false !== ($fname = readdir($dhandle)))
			{
				// if the file is not this file, and does not start with a '.' or '..',
				// then store it for later display
				if (($fname != '.') && ($fname != '..') AND
					($fname != basename($_SERVER['PHP_SELF'])))
				{
					// Get all the files in this directory
					if ( ! is_dir( "./$fname" ))
					{
						// Grab the extension of the file
						$extension = strtolower( substr($fname, strrpos ($fname, '.')) );
						if ( in_array($extension, $img_extensions) )
						{
							if( ! in_array($fname, $image_list))
							{
								// This is an orphan... so delete it
								$orphan = Kohana::config('upload.relative_directory')."/".$fname;
								//echo '-- '.$orphan.'<br/><br/>';
								@unlink($orphan);
							}
						}
					}
				}
			}

			// close the directory
			closedir($dhandle);
		}
	}
}
