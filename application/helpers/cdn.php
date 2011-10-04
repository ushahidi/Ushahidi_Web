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
	
	protected $cdn;
	
	public function __construct()
	{
		// We are only configured to use
		if(Kohana::config("cdn.cdn_provider") == 'cloudfiles')
		{
			// Okay, configured properly to use a supported provider.
			$this->cdn = new Cloudfiles;
		}else{
			return FALSE;
		}
	}
	
	public function upload($filename)
	{
		// Upload to the CDN and return new filename	
		return $this->cdn->upload($filename);
	}
	
	public function delete($url)
	{
		return $this->cdn->delete($url);
	}
	
	// This function is used to upgrade files over time based on users hitting the site
	public function gradual_upgrade()
	{
		// Select at random since admin may not want every user to initiate a CDN upload
		if(rand(1,(int)Kohana::config('cdn.cdn_gradual_upgrade')) == 1)
		{
			$query = 'SELECT id, media_link, media_medium, media_thumb FROM media WHERE media_type = 1 AND media_link NOT LIKE \'http%\' LIMIT 1;';
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result))
			{
				
				// Upload files to the CDN
				$new_large = $this->cdn->upload($row['media_link']);
				$new_medium = $this->cdn->upload($row['media_medium']);
				$new_thumb = $this->cdn->upload($row['media_thumb']);
				
				// Update the entry for the media file in the DB
				mysql_query('UPDATE media SET media_link = \''.$new_large.'\', media_medium = \''.$new_medium.'\', media_thumb = \''.$new_thumb.'\' WHERE id = '.$row['id']);
				
				// Remove old files
				$local_directory = Kohana::config('upload.directory', TRUE);
				$local_directory = rtrim($local_directory, '/').'/';
				unlink($local_directory.$row['media_link']);
				unlink($local_directory.$row['media_medium']);
				unlink($local_directory.$row['media_thumb']);
			}
			
			// Since category images are so small, we might as well have them go ahead and upload one of those
			$query = 'SELECT id, category_image, category_image_thumb FROM category WHERE category_image NOT LIKE \'http%\' LIMIT 1;';
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result))
			{
				// Upload files to the CDN
				$new_category_image = $this->cdn->upload($row['category_image']);
				$new_category_image_thumb = $this->cdn->upload($row['category_image_thumb']);
				
				// Update the entry for the media file in the DB
				mysql_query('UPDATE category SET category_image = \''.$new_category_image.'\', category_image_thumb = \''.$new_category_image_thumb.'\' WHERE id = '.$row['id']);
				
				// Remove old files
				$local_directory = Kohana::config('upload.directory', TRUE);
				$local_directory = rtrim($local_directory, '/').'/';
				unlink($local_directory.$row['category_image']);
				unlink($local_directory.$row['category_image_thumb']);
			}
			
			return true;
		}
		return false;
	}
	
	// javascript that calls the upgrader
	static function cdn_gradual_upgrade_js()
	{
		return '<script async type="text/javascript">
					$.ajax({
						type: "GET",
						url: "'.url::base().'main/cdn_gradual_upgrade"
					});
				</script>';
	}
}