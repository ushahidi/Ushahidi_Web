<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage user settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Settings_Controller extends Admin_Controller {

	/**
	 * @var Cache
	 */
	protected $cache;

	public function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';

		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("settings"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->cache = Cache::instance();
	}

	/**
	 * Site Settings
	 */
	public function site()
	{
		$this->template->content = new View('admin/settings/site');
		$this->template->content->title = Kohana::lang('ui_admin.settings');
		$this->template->js = new View('admin/settings/site_js');

		// setup and initialize form field names
		$form = array(
			'site_name' => '',
			'site_tagline' => '',
			'banner_image' => '',
			'delete_banner_image' => '',
			'site_email' => '',
			'alerts_email' =>  '',
			'site_language' => '',
			'site_timezone' => '',
			'site_message' => '',
			'site_copyright_statement' => '',
			'site_submit_report_message' => '',
			'site_contact_page' => '',
			'items_per_page' => '',
			'items_per_page_admin' => '',
			'blocks_per_row' => '',
			'allow_alerts' => '',
			'allow_reports' => '',
			'allow_comments' => '',
			'allow_feed' => '',
			'allow_stat_sharing' => '',
			'cache_pages' => '',
			'cache_pages_lifetime' => '',
			'private_deployment' => '',
			'manually_approve_users' => '',
			'require_email_confirmation' => '',
			'checkins' => '',
			'google_analytics' => '',
			'twitter_hashtags' => '',
			'api_akismet' => ''
		);
		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('site_name', 'required', 'length[3,250]');
			$post->add_rules('site_tagline', 'length[3,250]');
			$post->add_rules('site_email', 'email', 'length[4,100]');
			//$post->add_rules('alerts_email','required', 'email', 'length[4,100]');
			//$post->add_rules('site_message', 'standard_text');
			$post->add_rules('site_copyright_statement', 'length[4,600]');
			$post->add_rules('site_language','required', 'length[5, 5]');
			//$post->add_rules('site_timezone','required', 'between[10,50]');
			$post->add_rules('site_contact_page','required','between[0,1]');
			$post->add_rules('items_per_page','required','between[5,50]');
			$post->add_rules('items_per_page_admin','required','between[5,50]');
			$post->add_rules('blocks_per_row','required','numeric');
			$post->add_rules('allow_alerts','required','between[0,1]');
			$post->add_rules('allow_reports','required','between[0,1]');
			$post->add_rules('allow_comments','required','between[0,2]');
			$post->add_rules('allow_feed','required','between[0,1]');
			$post->add_rules('allow_stat_sharing','required','between[0,1]');
			$post->add_rules('cache_pages','required','between[0,1]');
			$post->add_rules('cache_pages_lifetime','required','in_array[60,300,600,900,1800]');
			$post->add_rules('private_deployment','required','between[0,1]');
			$post->add_rules('manually_approve_users','required','between[0,1]');
			$post->add_rules('require_email_confirmation','required','between[0,1]');
			$post->add_rules('checkins','required','between[0,1]');
			$post->add_rules('google_analytics','length[0,20]');
			$post->add_rules('twitter_hashtags','length[0,500]');
			$post->add_rules('api_akismet','length[0,100]', 'alpha_numeric');

			// Add rules for file upload
			$files = Validation::factory($_FILES);
			$files->add_rules('banner_image', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[250K]');

			// Test to see if things passed the rule checks
			if ($post->validate() AND $files->validate(FALSE))
			{
				// Yes! everything is valid
				Settings_Model::save_all($post);

				// Deal with banner image now

				// Check if deleting or updating a new image (or doing nothing)
				if (isset($post->delete_banner_image) AND $post->delete_banner_image == 1)
				{
					// Delete old badge image
					ORM::factory('media')->delete(Settings_Model::get_setting('site_banner_id'));

					// Remove from DB table
					Settings_Model::save_setting('site_banner_id', NULL);
				}
				else
				{
					// We aren't deleting, so try to upload if we are uploading an image
					$filename = upload::save('banner_image');
					if ($filename)
					{
						$new_filename = "banner_".time();
						$file_type = strrev(substr(strrev($filename),0,4));

						// Large size
						$l_name = $new_filename.$file_type;
						Image::factory($filename)->save(Kohana::config('upload.directory', TRUE).$l_name);

						// Medium size
						$m_name = $new_filename."_m".$file_type;
						Image::factory($filename)->resize(80,80,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE).$m_name);

						// Thumbnail
						$t_name = $new_filename."_t".$file_type;
						Image::factory($filename)->resize(60,60,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE).$t_name);

						// Name the files for the DB
						$media_link = $l_name;
						$media_medium = $m_name;
						$media_thumb = $t_name;

						// Okay, now we have these three different files on the server, now check to see
						//   if we should be dropping them on the CDN

						if (Kohana::config("cdn.cdn_store_dynamic_content"))
						{
							$media_link = cdn::upload($media_link);
							$media_medium = cdn::upload($media_medium);
							$media_thumb = cdn::upload($media_thumb);

							// We no longer need the files we created on the server. Remove them.
							$local_directory = rtrim(Kohana::config('upload.directory', TRUE), '/').'/';
							unlink($local_directory.$l_name);
							unlink($local_directory.$m_name);
							unlink($local_directory.$t_name);
						}

						// Remove the temporary file
						unlink($filename);

						// Save banner image in the media table
						$media = new Media_Model();
						$media->media_type = 1; // Image
						$media->media_link = $media_link;
						$media->media_medium = $media_medium;
						$media->media_thumb = $media_thumb;
						$media->media_date = date("Y-m-d H:i:s",time());
						$media->save();

						// Save new banner image in settings
						Settings_Model::save_setting('site_banner_id', $media->id);
					}
				}

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;

				// Action::site_settings_modified - Site settings have changed
				Event::run('ushahidi_action.site_settings_modified');

				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				if (is_array($files->errors()) AND count($files->errors()) > 0)
				{
					// Error with file upload
					$errors = arr::overwrite($errors, $files->errors('settings'));
				}
				else
				{
					// Error with other form filed
					$errors = arr::overwrite($errors, $post->errors('settings'));
				}

				$form_error = TRUE;
			}
		}
		else
		{
			$settings = Settings_Model::get_array();

			$form = array(
				'site_name' => $settings['site_name'],
				'site_tagline' => $settings['site_tagline'],
				'site_banner_id' => $settings['site_banner_id'],
				'site_email' => $settings['site_email'],
				'alerts_email' => $settings['alerts_email'],
				'site_message' => $settings['site_message'],
				'site_copyright_statement' => $settings['site_copyright_statement'],
				'site_submit_report_message' => $settings['site_submit_report_message'],
				'site_language' => $settings['site_language'],
				'site_timezone' => $settings['site_timezone'],
				'site_contact_page' => $settings['site_contact_page'],
				'items_per_page' => $settings['items_per_page'],
				'items_per_page_admin' => $settings['items_per_page_admin'],
				'blocks_per_row' => $settings['blocks_per_row'],
				'allow_alerts' => $settings['allow_alerts'],
				'allow_reports' => $settings['allow_reports'],
				'allow_comments' => $settings['allow_comments'],
				'allow_feed' => $settings['allow_feed'],
				'allow_stat_sharing' => $settings['allow_stat_sharing'],
				'cache_pages' => $settings['cache_pages'],
				'cache_pages_lifetime' => $settings['cache_pages_lifetime'],
				'private_deployment' => $settings['private_deployment'],
				'manually_approve_users' => $settings['manually_approve_users'],
				'require_email_confirmation' => $settings['require_email_confirmation'],
				'checkins' => $settings['checkins'],
				'google_analytics' => $settings['google_analytics'],
				'twitter_hashtags' => $settings['twitter_hashtags'],
				'api_akismet' => $settings['api_akismet']
			);
		}

		// Get banner image
		if (($site_banner_id = Settings_Model::get_setting('site_banner_id')) !== NULL)
		{
			$banner = ORM::factory('media')->find($site_banner_id);
			$this->template->content->banner = url::convert_uploaded_to_abs($banner->media_link);
			$this->template->content->banner_m = url::convert_uploaded_to_abs($banner->media_medium);
			$this->template->content->banner_t = url::convert_uploaded_to_abs($banner->media_thumb);
		}
		else
		{
			$this->template->content->banner = NULL;
			$this->template->content->banner_m = NULL;
			$this->template->content->banner_t = NULL;
		}


		$this->template->colorpicker_enabled = TRUE;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->items_per_page_array = array('5'=>'5 Items','10'=>'10 Items','20'=>'20 Items','30'=>'30 Items','50'=>'50 Items');
		$blocks_per_row_array = array();
		for ($i=1; $i <= 21; $i++)
		{
			$blocks_per_row_array[$i] = $i;
		}
		$this->template->content->blocks_per_row_array = $blocks_per_row_array;
		$this->template->content->yesno_array = array(
			'1'=>utf8::strtoupper(Kohana::lang('ui_main.yes')),
			'0'=>utf8::strtoupper(Kohana::lang('ui_main.no')));

		$this->template->content->comments_array = array(
			'1'=>utf8::strtoupper(Kohana::lang('ui_main.yes')." - ".Kohana::lang('ui_admin.approve_auto')),
			'2'=>utf8::strtoupper(Kohana::lang('ui_main.yes')." - ".Kohana::lang('ui_admin.approve_manual')),
			'0'=>utf8::strtoupper(Kohana::lang('ui_main.no')));

		$this->template->content->cache_pages_lifetime_array = array(
			'60'=>'1 '.Kohana::lang('ui_admin.minute'),
			'300'=>'5 '.Kohana::lang('ui_admin.minutes'),
			'600'=>'10 '.Kohana::lang('ui_admin.minutes'),
			'900'=>'15 '.Kohana::lang('ui_admin.minutes'),
			'1800'=>'30 '.Kohana::lang('ui_admin.minutes'));

		//Generate all timezones
		$site_timezone_array = array();
		$site_timezone_array[0] = Kohana::lang('ui_admin.server_time');
		foreach (timezone_identifiers_list() as $timezone)
		{
			$site_timezone_array[$timezone] = $timezone;
		}
		$this->template->content->site_timezone_array = $site_timezone_array;


		// Generate Available Locales
		$locales = ush_locale::get_i18n(TRUE);
		$this->template->content->locales_array = $locales;
		$this->cache->set('locales', $locales, array('locales'), 604800);
	}

	/**
	* Map Settings
	*/
	public function index($saved = false)
	{
		// Display all maps
		$this->template->api_url = Kohana::config('settings.api_url_all');

		// Current Default Country
		$current_country = Kohana::config('settings.default_country');

		$this->template->content = new View('admin/settings/main');
		$this->template->content->title = Kohana::lang('ui_admin.settings');

		// setup and initialize form field names
		$form = array(
			'default_map' => '',
			'api_google' => '',
			'api_live' => '',
			'default_country' => '',
			'multi_country' => '',
			'default_lat' => '',
			'default_lon' => '',
			'default_zoom' => '',
			'default_map_all' => '',
			'allow_clustering' => '',
			'default_map_all_icon' => '',
			'default_map_all_icon_id' => '',
			'delete_default_map_all_icon' => '',
			'enable_timeline' => ''
		);
		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved');

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = Validation::factory($_POST)
			    ->pre_filter('trim', TRUE)
			    ->add_rules('default_country', 'required', 'numeric', 'length[1,4]')
			    ->add_rules('multi_country', 'numeric', 'length[1,1]')
			    ->add_rules('default_map', 'required', 'length[0,100]')
			    ->add_rules('default_zoom','required','between[0,21]')		// Validate for maximum and minimum zoom values
			    ->add_rules('default_lat','required','between[-85,85]')		// Validate for maximum and minimum latitude values
			    ->add_rules('default_lon','required','between[-180,180]')		// Validate for maximum and minimum longitude values
			    ->add_rules('allow_clustering','required','between[0,1]')
			    ->add_rules('default_map_all','required', 'alpha_numeric', 'length[6,6]')
			    ->add_rules('api_google', 'length[0,200]')
			    ->add_rules('api_live', 'length[0,200]')
				->add_rules('enable_timeline', 'numeric', 'length[1,1]');
			

			// Add rules for file upload
			$files = Validation::factory($_FILES);
			$files->add_rules('default_map_all_icon', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[250K]');

			// Test to see if things passed the rule checks
			if ($post->validate() AND $files->validate(FALSE))
			{
				// Save all the settings
				Settings_Model::save_all($post);
				
				// E.Kala 20th April 2012
				// Ghetto workaround prevent resetting og Bing Maps API Key
				// Soon to be addressed conclusively
				if (isset($post['api_live']) AND ! empty($post['api_live']))
				{
					Settings_Model::save_setting('api_live', $post->api_live);
				}

				
				// Deal with default category icon now

				// Check if deleting or updating a new image (or doing nothing)
				if( isset($post->delete_default_map_all_icon) AND $post->delete_default_map_all_icon == 1)
				{
					// Delete old badge image
					ORM::factory('media')->delete(Settings_Model::get_setting('default_map_all_icon_id'));

					// Remove from DB table
					Settings_Model::save_setting('default_map_all_icon_id', NULL);

				}
				else
				{
					// We aren't deleting, so try to upload if we are uploading an image
					$filename = upload::save('default_map_all_icon');
					if ($filename)
					{
						$new_filename = "default_map_all_".time();
						$file_type = strrev(substr(strrev($filename),0,4));

						// Large size
						$l_name = $new_filename.$file_type;
						Image::factory($filename)->save(Kohana::config('upload.directory', TRUE).$l_name);

						// Medium size
						$m_name = $new_filename."_m".$file_type;
						Image::factory($filename)->resize(32,32,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE).$m_name);

						// Thumbnail
						$t_name = $new_filename."_t".$file_type;
						Image::factory($filename)->resize(16,16,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE).$t_name);

						// Name the files for the DB
						$media_link = $l_name;
						$media_medium = $m_name;
						$media_thumb = $t_name;

						// Okay, now we have these three different files on the server, now check to see
						//   if we should be dropping them on the CDN

						if (Kohana::config("cdn.cdn_store_dynamic_content"))
						{
							$media_link = cdn::upload($media_link);
							$media_medium = cdn::upload($media_medium);
							$media_thumb = cdn::upload($media_thumb);

							// We no longer need the files we created on the server. Remove them.
							$local_directory = rtrim(Kohana::config('upload.directory', TRUE), '/').'/';
							unlink($local_directory.$l_name);
							unlink($local_directory.$m_name);
							unlink($local_directory.$t_name);
						}

						// Remove the temporary file
						unlink($filename);

						// Save image in the media table
						$media = new Media_Model();
						$media->media_type = 1; // Image
						$media->media_link = $media_link;
						$media->media_medium = $media_medium;
						$media->media_thumb = $media_thumb;
						$media->media_date = date("Y-m-d H:i:s",time());
						$media->save();

						// Save new image in settings
						Settings_Model::save_setting('default_map_all_icon_id', $media->id);
					}
				}
				

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;

				// Action::map_settings_modified - Map settings have changed
				Event::run('ushahidi_action.map_settings_modified');

				// Redirect to reload everything over again
				url::redirect('admin/settings/index/saved');

			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
			}
		}
		else
		{
			// Retrieve Current Settings
			$settings = Settings_Model::get_settings(array_keys($form));

			$form = array(
				'default_map' => $settings['default_map'],
				'api_google' => $settings['api_google'],
				'api_live' => $settings['api_live'],
				'default_country' => $settings['default_country'],
				'multi_country' => $settings['multi_country'],
				'default_lat' => $settings['default_lat'],
				'default_lon' => $settings['default_lon'],
				'default_zoom' => $settings['default_zoom'],
				'allow_clustering' => $settings['allow_clustering'],
				'default_map_all' => $settings['default_map_all'],
				'default_map_all_icon_id' => $settings['default_map_all_icon_id'],
				'enable_timeline' => $settings['enable_timeline'],
			);
		}

		// Get default category image
		if (($default_map_all_icon_id = Settings_Model::get_setting('default_map_all_icon_id')) !== NULL)
		{
			$icon = ORM::factory('media')->find($default_map_all_icon_id);
			$this->template->content->default_map_all_icon = url::convert_uploaded_to_abs($icon->media_link);
			$this->template->content->default_map_all_icon_m = url::convert_uploaded_to_abs($icon->media_medium);
			$this->template->content->default_map_all_icon_t = url::convert_uploaded_to_abs($icon->media_thumb);
		}
		else
		{
			$this->template->content->default_map_all_icon = NULL;
			$this->template->content->default_map_all_icon_m = NULL;
			$this->template->content->default_map_all_icon_t = NULL;
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Get Countries
		$countries = array();
		foreach (ORM::factory('country')->orderby('country')->find_all() as $country)
		{
			// Create a list of all categories
			$this_country = $country->country;
			if (strlen($this_country) > 35)
			{
				$this_country = substr($this_country, 0, 30) . "...";
			}
			$countries[$country->id] = $this_country;
		}
		$this->template->content->countries = $countries;

		// Zoom Array for Slider
		$default_zoom_array = array();

		for ($i=Kohana::config('map.minZoomLevel'); $i<Kohana::config('map.minZoomLevel')+Kohana::config('map.numZoomLevels') ; $i++)
		{
			$default_zoom_array[$i] = $i;
		}
		$this->template->content->default_zoom_array = $default_zoom_array;

		// Get Map API Providers
		$layers = map::base();
		$map_array = array();
		foreach ($layers as $layer)
		{
			$map_array[$layer->name] = $layer->title;
		}
		$this->template->content->map_array = $map_array;
		
		$this->template->content->yesno_array = array(
			'1'=>utf8::strtoupper(Kohana::lang('ui_main.yes')),
			'0'=>utf8::strtoupper(Kohana::lang('ui_main.no')));

		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/settings/settings_js');
		$this->template->js->default_map = $form['default_map'];
		$this->template->js->default_zoom = $form['default_zoom'];
		$this->template->js->default_lat = $form['default_lat'];
		$this->template->js->default_lon = $form['default_lon'];
		$this->template->js->all_maps_json = $this->_generate_settings_map_js();
	}


	/**
	 * Handles SMS Settings
	 */
	public function sms()
	{
		$this->template->content = new View('admin/settings/sms');
		$this->template->content->title = Kohana::lang('ui_admin.settings');

		// setup and initialize form field names
		$form = array(
			'sms_provider' => '',
			'sms_no1' => '',
			'sms_no2' => '',
			'sms_no3' => ''
		);
		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('sms_provider', 'length[1,100]');
			$post->add_rules('sms_no1', 'numeric', 'length[1,30]');
			$post->add_rules('sms_no2', 'numeric', 'length[1,30]');
			$post->add_rules('sms_no3', 'numeric', 'length[1,30]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid
				Settings_Model::save_all($post);

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
			}
		}
		else
		{
			$settings = Settings_Model::get_settings(array_keys($form))
			;
			// Retrieve Current Settings
			$form = array(
				'sms_provider' => $settings['sms_provider'],
				'sms_no1' => $settings['sms_no1'],
				'sms_no2' => $settings['sms_no2'],
				'sms_no3' => $settings['sms_no3']
			);
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		$this->template->content->sms_provider_array = array_merge(
			array("" => "-- Select One --"),
			plugin::get_sms_providers()
			);
	}


	/**
	 * Email Settings
	 */
	public function email()
	{
		$this->template->content = new View('admin/settings/email');
		$this->template->content->title = Kohana::lang('ui_admin.settings');

		// setup and initialize form field names
		$form = array(
			'email_username' => '',
			'email_password' => '',
			'email_port' => '',
			'email_host' => '',
			'email_servertype' => '',
			'email_ssl' => ''
		);
		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('email_username', 'required', 'length[3,50]');
			$post->add_rules('email_password', 'length[3,100]');
			$post->add_rules('email_port', 'numeric[1,100]','length[1,20]');
			$post->add_rules('email_host','required', 'length[3,100]');
			$post->add_rules('email_servertype','required','length[3,100]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid
				Settings_Model::save_all($post);

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
			}
		}
		else
		{
			// Retrieve Current Settings
			$settings = Settings_Model::get_settings(array_keys($form));

			$form = array(
				'email_username' => $settings['email_username'],
				'email_password' => $settings['email_password'],
				'email_port' => $settings['email_port'],
				'email_host' => $settings['email_host'],
				'email_servertype' => $settings['email_servertype'],
				'email_ssl' => $settings['email_ssl']
			);
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->email_ssl_array = array('1'=>Kohana::lang('ui_admin.yes'),'0'=>Kohana::lang('ui_admin.no'));

		// Javascript Header
		$this->template->js = new View('admin/settings/email_js');
	}

		/**
	 * Clean URLs settings
	 */
	public function cleanurl() {

		// We cannot allow cleanurl settings to be changed if MHI is enabled since it modifies a file in the config folder
		if (Kohana::config('config.enable_mhi') == TRUE)
		{
			throw new Kohana_User_Exception('Access Error', "Please contact the administrator in order to use this feature.");
		}

		$this->template->content = new View('admin/settings/cleanurl');
		$this->template->content->title = Kohana::lang('ui_admin.settings');

		// setup and initialize form field names
		$form = array
		(
			'enable_clean_url' => '',
		);

		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('enable_clean_url','required','between[0,1]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				$this->_configure_index_page($post->enable_clean_url);

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
			}

		}
		else
		{

			$yes_or_no = $this->_check_clean_url_on_ushahidi() == TRUE ? 1 : 0;
			// initialize form
			$form = array
			(
				'enable_clean_url' => $yes_or_no,
			);
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->yesno_array = array('1'=>utf8::strtoupper(Kohana::lang('ui_main.yes')),'0'=>utf8::strtoupper(Kohana::lang('ui_main.no')));
		$this->template->content->is_clean_url_enabled = $this->_check_for_clean_url();

	}

	/**
	 * HTTPS settings
	 */
	public function https()
	{
		// We cannot allow cleanurl settings to be changed if MHI is enabled since it modifies a file in the config folder
		if (Kohana::config('config.enable_mhi') == TRUE)
		{
			throw new Kohana_User_Exception('Access Error', "Please contact the administrator in order to use this feature.");
		}

		$this->template->content = new View('admin/settings/https');
		$this->template->content->title = Kohana::lang('ui_admin.settings');

		// setup and initialize form field names
		$form = array
		(
			'enable_https' => '',
		);

		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('enable_https','required','between[0,1]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				$this->_configure_https_mode($post->enable_https);

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());
			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
			}

		}
		else
		{

			$yes_or_no = $this->_is_https_enabled() == TRUE ? 1 : 0;
			// initialize form
			$form = array
			(
				'enable_https' => $yes_or_no,
			);
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->yesno_array = array('1'=>utf8::strtoupper(Kohana::lang('ui_main.yes')),'0'=>utf8::strtoupper(Kohana::lang('ui_main.no')));
		$this->template->content->is_https_capable = $this->_is_https_capable();
	}


	/**
	 * Retrieves cities listing using GeoNames Service
	 *
	 * @param int $country_id The id of the country to retrieve cities for
	 * Returns a JSON response
	 */
	public function update_cities($country_id = 0)
	{
		$this->template = "";
		$this->auto_render = FALSE;

		// Get country ISO code from DB
		$country = ORM::factory('country', (int) $country_id);

		// No. of cities fetched
		$entries = 0;

		// Default payload to be returned to client as a JSON object
		$status_response = array(
			"status" => "error",

			// Default response message
			"response" => sprintf("%d %s %s", $entries, Kohana::lang('ui_admin.cities_loaded'), 
			                        Kohana::lang('ui_admin.country_not_found'))
		);

		if ($country->loaded)
		{
			$iso = strtoupper($country->iso);

			// Base URL for the Geonames API endpoint
			$base_url = "http://api.geonames.org/";

			// Get the country info
			$country_url = $base_url."countryInfoJSON?country=%s&username=ushahididev";

			$client = new HttpClient(sprintf($country_url, $iso));
			if (($response = $client->execute()) !== FALSE)
			{
				// Decode the JSON
				$response = json_decode($response, TRUE);

				// Geonames returned an error
				if ( ! array_key_exists('geonames', $response))
				{
					echo json_encode($status_response);
					exit;
				}

				// Get the south,east, north and west bounds
				$country_info = $response['geonames'][0];

				// Fetch the city names

				// 
				// TODO: EK <emmanuel(at)ushahidi.com
				// The maximum no. of cities + the geonames username
				// should be configurable parameters. Right now, I've set the upper
				// limit for the cities to 1000
				// 
				$cities_url = $base_url."citiesJSON?north=%s&south=%s&east=%s&west=%s&username=ushahididev&maxRows=1000";

				// Add the bounding box values
				$cities_url = sprintf($cities_url, $country_info['north'], $country_info['south'],
				    $country_info['east'], $country_info['west']);

				// Fetch the cities
				$cities_client = new HttpClient($cities_url);
				if (($response = $cities_client->execute()) !== FALSE)
				{
					// Decode the JSON response
					$response = json_decode($response, TRUE);

					$cities = array_key_exists('geonames', $response) 
					    ? $response['geonames']
					    : array();

					// Only proceed if cities are returned
					if (count($cities) > 0)
					{
						// Set the city count for the country
						$country->cities = count($cities);
						$country->save();

						// Delete all the cities for the current country
						ORM::factory('city')
						    ->where('country_id', $country->id)
						    ->delete_all();

						// Manually construct the query (DB library can't do bulk inserts)
						$query = sprintf("INSERT INTO %scity (`country_id`, `city`, `city_lat`, `city_lon`) VALUES ", $this->table_prefix);

						$values = array();
						// Create a database expression and use that to sanitize values
						$values_expr = new Database_Expression("(:countryid, :city, :lat, :lon)");

						// Add the freshly fetched cities
						foreach ($cities as $city)
						{
							$values_expr->param(':countryid', $country->id);
							$values_expr->param(':city', $city['name']);
							$values_expr->param(':lat', $city['lat']);
							$values_expr->param(':lon', $city['lng']);
							$values[] = $values_expr->compile();
						}

						$query .= implode(",", $values);

						// Batch insert
						Database::instance()->query($query);

						$entries = count($cities);
					}

					// Set the response payload
					$status_response['status'] = "success";
					$status_response['response'] = sprintf("%d %s", $entries,
					    Kohana::lang('ui_admin.cities_loaded'));
				}
				else
				{
					// Geonames timed out
					$status_response['response'] = Kohana::lang('ui_admin.geonames_timeout');
				}
			}
			else
			{
				// Geonames timeout
				$status_response['response'] = Kohana::lang('ui_admin.geonames_timeout');
			}
		}

		echo json_encode($status_response);
	}


	/**
	 * Check if clean url can be enabled on the server so
	 * Ushahidi can cough it.
	 *
	 * @return boolean
	 */

	private function _check_for_clean_url() {

		$url = url::base()."help";

		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($curl_handle);

		$return_code = curl_getinfo($curl_handle,CURLINFO_HTTP_CODE);
		curl_close($curl_handle);

		return ($return_code ==	 404)? FALSE : TRUE;
	}

	/**
	 * Removes / Adds index.php from / to index page variable in application/config.config.php file
	 *
	 * @param $yes_or_no
	 */
	private function _configure_index_page( $yes_or_no ) {

		$config_file = @file('application/config/config.php');
		$handle = @fopen('application/config/config.php', 'w');

		if(is_array($config_file) )
		{
			foreach ($config_file as $line_number => $line)
			{
				if ($yes_or_no == 1)
				{
					if( strpos(" ".$line,"\$config['index_page'] = 'index.php';") != 0 )
					{
						fwrite($handle, str_replace("index.php","",$line ));

						// Set the 'index_page' property in the configuration
						Kohana::config_set('core.index_page', '');
					}
					else
					{
						fwrite($handle, $line);
					}

				}
				else
				{
					if( strpos(" ".$line,"\$config['index_page'] = '';") != 0 )
					{
						fwrite($handle, str_replace("''","'index.php'",$line ));

						// Set the 'index_page' property in the configuration
						Kohana::config_set('core.index_page', 'index.php');
					}
					else
					{
						fwrite($handle, $line);
					}
				}
			}
		}
	}

	/**
	 * Check if clean URL is enabled on Ushahidi
	 */
	private function _check_clean_url_on_ushahidi()
	{
		$config_file = @file_get_contents('application/config/config.php');

		return (strpos( $config_file,"\$config['index_page'] = 'index.php';") != 0 )
			? FALSE
			: TRUE;
	}

	private function _generate_settings_map_js()
	{
		$map_layers = array();
		$layers = map::base();

		foreach ($layers as $layer)
		{
			$map_layers[$layer->name] = array();
			$map_layers[$layer->name]['title'] = $layer->title;
			$map_layers[$layer->name]['openlayers'] = $layer->openlayers;
			if (isset($layer->api_signup))
			{
				$map_layers[$layer->name]['api_signup'] = $layer->api_signup;
			}
			else
			{
				$map_layers[$layer->name]['api_signup'] = "";
			}
		}

		return json_encode($map_layers);
	}

	/**
	 * Check if SSL is currently enabled on the instance
	 */
	private function _is_https_enabled()
	{
		$config_file = @file_get_contents('application/config/config.php');

		return (strpos( $config_file,"\$config['site_protocol'] = 'http';") != 0 )
			? FALSE
			: TRUE;
	}

	/**
	 * Check if the Webserver is HTTPS capable
	 */
	private function _is_https_capable()
	{
		// Get the current site protocol
		$protocol = Kohana::config('core.site_protocol');

		// Build an SSL URL
		$url = ($protocol == 'https')? url::base() : str_replace('http://', 'https://', url::base());

		$url .= 'index.php';

		// Initialize cURL
		$ch = curl_init();

		// Set cURL options
		curl_setopt($ch, CURLOPT_URL, $url);

		// Disable following any "Location:" sent as part of the HTTP header
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);

		// Return the output of curl_exec() as a string instead of outputting it directly
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// Suppress header information from the output
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		// Allow connection to HTTPS
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		// Perform cURL session
		curl_exec($ch);

		// Get the cURL error number
		$error_no = curl_errno($ch);

		// Get the return code
		$http_return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close the cURL handle
		curl_close($ch);

		// Check if the cURL session succeeded
		return (($error_no > 0 AND $error_no != 60) OR $http_return_code == 404)
			? FALSE
			: TRUE;
	}

	/**
	 * Configures the HTTPS mode for the Ushahidi instance
	 *
	 * @param int $yes_or_no
	 */
	private function _configure_https_mode($yes_or_no)
	{
		$config_file = @file('application/config/config.php');
		$handle = @fopen('application/config/config.php', 'w');

		if(is_array($config_file) AND $handle)
		{
			foreach ($config_file as $line_number => $line)
			{
				if ($yes_or_no == 1)
				{
					if( strpos(" ".$line,"\$config['site_protocol'] = 'http';") != 0 )
					{
						fwrite($handle, str_replace("http", "https", $line ));

						// Enable HTTPS on the config
						Kohana::config_set('core.site_protocol', 'https');
					}
					else
					{
						fwrite($handle, $line);
					}
				}
				else
				{
					if( strpos(" ".$line,"\$config['site_protocol'] = 'https';") != 0 )
					{
						fwrite($handle, str_replace("https", "http", $line ));

						// Enable HTTP on the config
						Kohana::config_set('core.site_protocol', 'http');
					}
					else
					{
						fwrite($handle, $line);
					}
				}
			}
		}

	}
}
