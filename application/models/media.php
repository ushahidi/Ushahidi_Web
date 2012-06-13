<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Media files: photos, videos of incidents or locations
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Media_Model extends ORM
{
	protected $belongs_to = array('location', 'incident', 'message', 'badge');
	
	// Database table name
	protected $table_name = 'media';

	/**
	 * Delete Photo
	 * @param int $id The unique id of the photo to be deleted
	 */
	public static function delete_photo($id)
	{
		$photo = ORM::factory('media', $id);
		$photo_large = $photo->media_link;
		$photo_medium = $photo->media_medium;
		$photo_thumb = $photo->media_thumb;

		if (file_exists(Kohana::config('upload.directory', TRUE).$photo_large))
		{
			unlink(Kohana::config('upload.directory', TRUE).$photo_large);
		}
		elseif (Kohana::config("cdn.cdn_store_dynamic_content") AND valid::url($photo_large))
		{
			cdn::delete($photo_large);
		}

		if (file_exists(Kohana::config('upload.directory', TRUE).$photo_medium))
		{
			unlink(Kohana::config('upload.directory', TRUE).$photo_medium);
		}
		elseif (Kohana::config("cdn.cdn_store_dynamic_content") AND valid::url($photo_medium))
		{
			cdn::delete($photo_medium);
		}

		if (file_exists(Kohana::config('upload.directory', TRUE).$photo_thumb))
		{
			unlink(Kohana::config('upload.directory', TRUE).$photo_thumb);
		}
		elseif (Kohana::config("cdn.cdn_store_dynamic_content") AND valid::url($photo_thumb))
		{
			cdn::delete($photo_thumb);
		}

		// Finally Remove from DB
		$photo->delete();
	}
}
