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
	private static $cdn = NULL;

	/**
	 * Connect to the right CDN provider library
	 */
	private static function connection()
	{
		if (self::$cdn == NULL)
		{
			try
			{
				if (Kohana::config("cdn.cdn_provider") == 'cloudfiles')
				{
					// Okay, configured properly to use a supported provider.
					self::$cdn = new Cloudfiles;
				}
				else
				{
					return FALSE;
				}
			}
			catch (Exception $e)
			{
				Kohana::log('error', Kohana::config("settings.subdomain")." CDN ERROR: ".$e->getMessage());
				return FALSE;
			}
		}
	}

	/**
	 * Uploads a file to the CDN
	 *
	 * @param string $filename Name of the file to be uploaded
	 * @return string
	 */
	public static function upload($filename)
	{
		try
		{
			self::connection();

			// Upload to the CDN and return new filename
			return self::$cdn->upload($filename);
		}
		catch (Exception $e)
		{
			Kohana::log('error', Kohana::config("settings.subdomain")." CDN ERROR: ".$e->getMessage());
			return FALSE;
		}
	}


	/**
	 * Deletes an item from the CDN
	 *
	 * @param string $url URI for the item to delete
	 */
	public static function delete($url)
	{
		try
		{
			self::connection();
			return self::$cdn->delete($url);
		}
		catch (Exception $e)
		{
			Kohana::log('error', Kohana::config("settings.subdomain")." CDN ERROR: ".$e->getMessage());
			return FALSE;
		}
	}


	/**
	 * Upgrades files over time based on users hitting the site
	 *
	 * @return bool TRUE if the upgrade is successful, FALSE otherwise
	 */
	public static function gradual_upgrade()
	{
		if (Kohana::config('cdn.cdn_store_dynamic_content') == FALSE)
		{
			return FALSE;
		}

		try
		{
			self::connection();

			if ( ! self::$cdn)
				throw new Kohana_Exception('CDN provider not specified');

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
				$uploaded_flag = false;
				foreach ($media_links as $row)
				{
					// Upload files to the CDN
					$new_large = self::$cdn->upload($row->media_link);
					$new_medium = self::$cdn->upload($row->media_medium);
					$new_thumb = self::$cdn->upload($row->media_thumb);

					// Update the entry for the media file in the DB
					$db->update('media',
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

					$uploaded_flag = true;
				}

				// If we didn't have any more user uploaded images to upload, move on to category images
				if($uploaded_flag == false)
				{
					$query = 'SELECT id, category_image, category_image_thumb '
							. 'FROM '.$table_prefix.'category '
							. 'WHERE category_image NOT LIKE \'http%\' LIMIT 1';

					// Fetch
					$category_images = $db->query($query);
					foreach ($category_images as $row)
					{
						// Upload files to the CDN
						$new_category_image = self::$cdn->upload($row->category_image);
						$new_category_image_thumb = self::$cdn->upload($row->category_image_thumb);

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

						$uploaded_flag = true;
					}
				}

				// If we are done with category images, move on to KML layers
				if($uploaded_flag == false)
				{
					// Grab a KML file we have locally that isn't linking to an external URL
					$query = 'SELECT id, layer_file '
							. 'FROM '.$table_prefix.'layer '
							. 'WHERE layer_url = \'\' LIMIT 1';
					$layers = $db->query($query);
					foreach ($layers as $row)
					{
						$layer_file = $row->layer_file;

						// Upload the file to the CDN
						$layer_url = cdn::upload($layer_file);

						// We no longer need the files we created on the server. Remove them.
						$local_directory = rtrim(Kohana::config('upload.directory', TRUE), '/').'/';
						unlink($local_directory.$layer_file);

						$layer = new Layer_Model($row->id);
						$layer->layer_url = $layer_url;
						$layer->layer_file = '';
						$layer->save();
					}

				}

				// Garbage collection
				unset ($db, $media_links, $category_images);

				return TRUE;
			}
		}
		catch (Exception $e)
		{
			Kohana::log('error', Kohana::config("settings.subdomain")." CDN ERROR: ".$e->getMessage());

			return FALSE;
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