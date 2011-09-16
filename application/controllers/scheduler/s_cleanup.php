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
	 * This function helps cleanup orphaned images in the upload directory
	 * Orphans are usually a result of new reports that are never completed
	 */
	public function index()
	{
		// open the images directory
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
								unlink($orphan);
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
