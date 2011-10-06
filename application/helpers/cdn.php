<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * CDN helper class to pass data to the proper provier library
 *
 * @package    Ushahidi
 * @category   Helpers
 * @author     Ushahidi Team
 * @copyright  (c) 2011 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class cdn_Core {

	/**
	 * Reference to the CDN provider library
	 * @var mixed
	 */
	private static $cdn = (Kohana::config('cdn.cdn_provider') == 'cloudfiles')
							? new Cloudfiles()
							: FALSE;
	
	/**
	 * Uploads a file to the CDN
	 *
	 * @param string $filename Name of the file to be uploaded
	 * @return string
	 */
	public static function upload($filename)
	{
		// Upload to the CDN and return new filename	
		return self::$cdn->upload($filename);
	}

	
	/**
	 * Deletes an item from the CDN
	 *
	 * @param string $url URI for the item to delete
	 */
	public static function delete($url)
	{
		return self::$cdn->delete($url);
	}
	
	
	/**
	 * Upgrades files over time based on users hitting the site
	 *
	 * @return bool TRUE if the upgrade is successful, FALSE otherwise
	 */
	public static function gradual_upgrade()
	{
		if ( ! self::$cdn)
			throw new Kohana_Exception('CDN provider not specified')
			
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Select at random since admin may not want every user to initiate a CDN upload
		if (rand(1, intval(Kohana::config('cdn.cdn_gradual_upgrade'))) == 1)
		{
			$query = 'SELECT id, media_link, media_medium, media_thumb '
					. 'FROM '.$table_prefix.'media '
					. 'WHERE media_type = 1 AND media_link NOT LIKE \'http%\' LIMIT 1';
			
			// Database instance for fetch and update operations
			$db = Database::instance();
			
			$media_links = $db->query($query);
			foreach ($media_links as $row)
			{
				// Upload files to the CDN
				$new_large = self::$cdn->upload($row->media_link);
				$new_medium = self::$cdn->upload($row->media_medium);
				$new_thumb = self::$cdn->upload($row->media_thumb);
				
				// Update the entry for the media file in the DB
				$db->update('media_link', 
					// Columns to update and their new values
					array(
						'media_link' => $new_large, 
						'media_medium'=>$new_medium, 
						'media_thumb'=>$new_thumb
					),
					// WHERE clause to update on
					array('id' => $row->id)
				);
					
				// Remove old files
				$local_directory = Kohana::config('upload.directory', TRUE);
				$local_directory = rtrim($local_directory, '/').'/';
				unlink($local_directory.$row->media_link);
				unlink($local_directory.$row->media_medium);
				unlink($local_directory.$row->media_thumb);
			}
			
			// Since category images are so small, we might as well have them go ahead and upload one of those
			$query = 'SELECT id, category_image, category_image_thumb '
					. 'FROM '.$table_prefix.'category '
					. 'WHERE category_image NOT LIKE \'http%\' LIMIT 1';
			
			// Fetch		
			$category_images = $db->query($query);
			foreach ($category_images as $row)
			{
				// Upload files to the CDN
				$new_category_image = $this->cdn->upload($row->category_image);
				$new_category_image_thumb = $this->cdn->upload($row->category_image_thumb);
				
				// Update the entry for the media file in the DB
				$db->update('category', 
					// Columns to be updated
					array(
						'category_image' => $new_category_image, 
						'category_image_thumb' => $new_category_image_thumb
					), 
					// WHERE clause to update on
					array('id' => $row->id)
				);
				
				// Remove old files
				$local_directory = Kohana::config('upload.directory', TRUE);
				$local_directory = rtrim($local_directory, '/').'/';
				unlink($local_directory.$row['category_image']);
				unlink($local_directory.$row['category_image_thumb']);
			}
			
			// Garbage collection
			unset ($db, $media_links, $category_images);
			
			return TRUE;
		}
		return FALSE;
	}
	
	
	/**
	 * Javascript call to the upgrade
	 *
	 * @return string
	 */
	public static function cdn_gradual_upgrade_js()
	{
		return '<script async type="text/javascript">
					$.ajax({
						type: "GET",
						url: "'.url::site().'main/cdn_gradual_upgrade"
					});
				</script>';
	}
}