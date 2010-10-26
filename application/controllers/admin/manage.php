<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to add/ remove categories
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Admin Manage Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

/**
 * Feed type value for news
 */
define('FEED_TYPE_TEXT', 'text');

/**
 * Feed type value for videos
 */
define('FEED_TYPE_VIDEO', 'video');

/**
 * Feed type value for photos
 */
define('FEED_TYPE_PHOTO', 'photo');

class Manage_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';

		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}

	/*
	Add Edit Categories
	*/
	function index()
	{
		$this->template->content = new View('admin/categories');
		$this->template->content->title = Kohana::lang('ui_admin.categories');

		// Locale (Language) Array
		$locales = locale::get_i18n();

		// Setup and initialize form field names
		$form = array
		(
			'action' => '',
			'category_id'	   => '',
			'parent_id'		 => '',
			'category_title'	  => '',
			'category_description'	  => '',
			'category_color'  => '',
			'category_image'  => '',
			'category_image_thumb'  => ''
		);

		// Add the different language form keys for fields
		foreach($locales as $lang_key => $lang_name){
			$form['category_title_'.$lang_key] = '';
		}

		// copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$parents_array = array();

		// Check, has the form been submitted, if so, setup validation

		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things

			$post = Validation::factory(array_merge($_POST,$_FILES));

			 //	 Add some filters

			$post->pre_filter('trim', TRUE);

			// Add Action

			if ($post->action == 'a')
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('parent_id','required','numeric');
				$post->add_rules('category_title','required', 'length[3,80]');
				$post->add_rules('category_description','required');
				$post->add_rules('category_color','required', 'length[6,6]');
				$post->add_rules('category_image', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[50K]');

				$post->add_callbacks('parent_id', array($this,'parent_id_chk'));

				// Add the different language form keys for fields
				foreach($locales as $lang_key => $lang_name){
					$post->add_rules('category_title_lang['.$lang_key.']','length[3,80]');
				}
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$category_id = $post->category_id;
				$category = new Category_Model($category_id);

				// Grab languages if they already exist

				$category_lang = Category_Lang_Model::category_langs($category->id);
				if(isset($category_lang[$category->id]))
				{
					$category_lang = $category_lang[$category->id];
				}else{
					$category_lang = FALSE;
				}

				if( $post->action == 'd' )
				{ // Delete Action

					// Delete localizations

					ORM::factory('category_lang')
						->where(array('category_id' => $category_id))
						->delete_all();

					// Delete category itself

					ORM::factory('category')
						->where('category_trusted != 1')
						->delete($category_id);

					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				elseif( $post->action == 'v' )
				{ // Show/Hide Action
					if ($category->loaded==true)
					{
						if ($category->category_visible == 1) {
							$category->category_visible = 0;
						}
						else
						{
							$category->category_visible = 1;
						}

						$category->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				elseif( $post->action == 'i' )
				{ // Delete Image/Icon Action

					if ($category->loaded==true)
					{
						$category_image = $category->category_image;
						$category_image_thumb = $category->category_image_thumb;

						if ( ! empty($category_image)
							 AND file_exists(Kohana::config('upload.directory', TRUE).$category_image))
						{
							unlink(Kohana::config('upload.directory', TRUE) . $category_image);
						}

						if ( ! empty($category_image_thumb)
							 AND file_exists(Kohana::config('upload.directory', TRUE).$category_image_thumb))
						{
							unlink(Kohana::config('upload.directory', TRUE) . $category_image_thumb);
						}

						$category->category_image = null;
						$category->category_image_thumb = null;
						$category->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}

				}
				elseif( $post->action == 'a' )
				{
					// Save Action
					$category->parent_id = $post->parent_id;
					$category->category_title = $post->category_title;
					$category->category_description = $post->category_description;
					$category->category_color = $post->category_color;
					$category->save();

					// Save Localizations
					foreach($post->category_title_lang as $lang_key => $localized_category_name){

						if(isset($category_lang[$lang_key]['id']))
						{
							// Update
							$cl = ORM::factory('category_lang',$category_lang[$lang_key]['id']);
						}else{
							// Add New
							$cl = ORM::factory('category_lang');
						}
 						$cl->category_title = $localized_category_name;
 						$cl->locale = $lang_key;
 						$cl->category_id = $category->id;
						$cl->save();
					}

					// Upload Image/Icon
					$filename = upload::save('category_image');
					if ($filename)
					{
						$new_filename = "category_".$category->id."_".time();

						// Resize Image to 32px if greater
						Image::factory($filename)->resize(32,32,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE) . $new_filename.".png");
						// Create a 16x16 version too
						Image::factory($filename)->resize(16,16,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE) . $new_filename."_16x16.png");

						// Remove the temporary file
						unlink($filename);

						// Delete Old Image
						$category_old_image = $category->category_image;
						if ( ! empty($category_old_image)
							AND file_exists(Kohana::config('upload.directory', TRUE).$category_old_image))
							unlink(Kohana::config('upload.directory', TRUE).$category_old_image);

						// Save
						$category->category_image = $new_filename.".png";
						$category->category_image_thumb = $new_filename."_16x16.png";
						$category->save();
					}

					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));

					// Empty $form array
					array_fill_keys($form, '');
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			   // populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('category'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
							'query_string' => 'page',
							'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
							'total_items'	 => ORM::factory('category')
													->where('parent_id','0')
													->count_all()
						));

		$categories = ORM::factory('category')
									->with('category_lang')
									->where('parent_id','0')
									->orderby('category_title', 'asc')
									->find_all((int) Kohana::config('settings.items_per_page_admin'),
												$pagination->sql_offset);

		$parents_array = ORM::factory('category')
									 ->where('parent_id','0')
									 ->select_list('id', 'category_title');

		// add none to the list
		$parents_array[0] = "--- Top Level Category ---";

		// Put "--- Top Level Category ---" at the top of the list
		ksort($parents_array);

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->categories = $categories;

		$this->template->content->parents_array = $parents_array;

		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/categories_js');
		$this->template->form_error = $form_error;

		$this->template->content->locale_array = $locales;
		$this->template->js->locale_array = $locales;
	}

	/*
	Add Edit Organizations
	*/
	function organizations()
	{
		$this->template->content = new View('admin/organizations');

		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'organization_id'	   => '',
			'organization_name'		 => '',
			'organization_description'	  => '',
			'organization_website'	=> '',
			'organization_email'  => '',
			'organization_phone1'  => '',
			'organization_phone2'  => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('organization_name','required', 'length[3,70]');
				$post->add_rules('organization_description','required');
				$post->add_rules('organization_website','required','url');
				$post->add_rules('organization_email', 'email', 'length[4,100]');
				$post->add_rules('organization_phone1', 'length[3,50]');
				$post->add_rules('organization_phone2', 'length[3,50]');
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$organization_id = $post->organization_id;

				$organization = new Organization_Model($organization_id);

				if( $post->action == 'd' )
				{ // Delete Action
					$organization->delete( $organization_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));

				}
				elseif( $post->action == 'v' )
				{
					// Show/Hide Action
					if ($organization->loaded==true)
					{
						if ($organization->organization_active == 1)
						{
							$organization->organization_active = 0;
						}
						else
						{
							$organization->organization_active = 1;
						}
						$organization->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				elseif( $post->action == 'a' )
				{ // Save Action
					$organization->organization_name = $post->organization_name;
					$organization->organization_description = $post->organization_description;
					$organization->organization_website = $post->organization_website;
					$organization->organization_email = $post->organization_email;
					$organization->organization_phone1 = $post->organization_phone1;
					$organization->organization_phone2 = $post->organization_phone2;
					$organization->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
			}
			else // No! We have validation errors, we need to show the form again, with the errors
			{
				 // repopulate the form fields
				 $form = arr::overwrite( $form, $post->as_array() );

			   // populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('organization'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
							'query_string' => 'page',
							'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
							'total_items'	 =>
							ORM::factory('organization')->count_all()
						));

		$organization = ORM::factory('organization')
						->orderby('organization_name', 'asc')
						->find_all((int) Kohana::config('settings.items_per_page_admin'),
							$pagination->sql_offset);

		$this->template->content->form = $form;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->organizations = $organization;
		$this->template->content->errors = $errors;

		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/organization_js');
	}


	/*
	Add Edit Pages
	*/
	function pages()
	{
		$this->template->content = new View('admin/pages');

		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'page_id'	   => '',
			'page_title'	  => '',
			'page_tab'		=> '',
			'page_description'	  => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{ // Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('page_title','required', 'length[3,150]');
				$post->add_rules('page_description','required');
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$page_id = $post->page_id;

				$page = new Page_Model($page_id);

				if( $post->action == 'd' )
				{ // Delete Action
					$page->delete( $page_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));

				}
				elseif( $post->action == 'v' )
				{ // Show/Hide Action
					if ($page->loaded==true)
					{
						if ($page->page_active == 1)
						{
							$page->page_active = 0;
						}
						else
						{
							$page->page_active = 1;
						}
						$page->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				elseif( $post->action == 'a' )
				{ // Save Action
					$page->page_title = $post->page_title;
					$page->page_tab = $post->page_tab;
					$page->page_description = $post->page_description;
					$page->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
			}
			else
			{ // No! We have validation errors, we need to show the form again, with the errors

				 // repopulate the form fields
				 $form = arr::overwrite( $form, $post->as_array() );

			   // populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('page'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
							'query_string' => 'page',
							'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
							'total_items'	 =>
							ORM::factory('page')->count_all()
						));

		$pages = ORM::factory('page')
						->orderby('page_title', 'asc')
						->find_all((int) Kohana::config('settings.items_per_page_admin'),
							$pagination->sql_offset);


		$this->template->content->form = $form;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->pages = $pages;
		$this->template->content->errors = $errors;

		// Javascript Header
		$this->template->editor_enabled = TRUE;
		$this->template->js = new View('admin/pages_js');
	}


	/*
	Add Edit News Feeds
	*/
	function feeds()
	{
		$this->template->content = new View('admin/feeds');

		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'feed_id'	   => '',
			'feed_name'		 => '',
			'feed_url'	  => '',
			'feed_active'  => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		if( $_POST )
		{
			//print_r($_POST);
			$post = Validation::factory( $_POST );

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('feed_name','required', 'length[3,70]');
				$post->add_rules('feed_url','required','url');
			}

			if( $post->validate() )
			{
				$feed_id = $post->feed_id;

				$feed = new Feed_Model($feed_id);
				if ( $post->action == 'd' )
				{					// Delete Action
					if ($feed->loaded==true)
					{
						ORM::factory('feed_item')->where('feed_id',$feed_id)->delete_all();
					}
					$feed->delete( $feed_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				elseif($post->action == 'v') // Active/Inactive Action
				{
					if ($feed->loaded==true)
					{
						if ($feed->feed_active == 1)
						{
							$feed->feed_active = 0;
						}
						else
						{
							$feed->feed_active = 1;
						}
						$feed->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				elseif( $post->action == 'r' )
				{
					$this->_parse_feed();
				}
				else // Save Action
				{
					// SAVE Feed
					$feed->feed_name = $post->feed_name;
					$feed->feed_url = $post->feed_url;
					$feed->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}

			}
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			   // populate the error fields, if any
				$errors = arr::overwrite($errors,
					$post->errors('feeds'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
							'query_string' => 'page',
							'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
							'total_items'	 => ORM::factory('feed')->count_all()
						));

		$feeds = ORM::factory('feed')
						->orderby('feed_name', 'asc')
						->find_all((int) Kohana::config('settings.items_per_page_admin'),
							$pagination->sql_offset);

		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->feeds = $feeds;
		$this->template->content->errors = $errors;

		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/feeds_js');
	}

	/*
	View/Edit News Feed Items
	*/
	/*
	Add Edit News Feeds
	*/
	function feeds_items()
	{
		$this->template->content = new View('admin/feeds_items');

		if ( isset($feed_id)  AND !empty($feed_id) )
		{
			$filter = " feed_id = '" . $feed_id . "' ";
		}
		else
		{
			$filter = " 1=1";
		}

		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		if ( $_POST )
		{
			$post = Validation::factory( $_POST );

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			if( $post->validate() )
			{
				$item_id = $this->input->post('item_id');

				ORM::factory('feed_item')->delete($item_id);

				$form_saved = TRUE;
				$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
			}
		}

		// Pagination
		$pagination = new Pagination(array(
				'query_string'	 => 'page',
				'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
				'total_items'	 => ORM::factory('feed_item')
												->where($filter)
												->count_all()
				)
			);


		$feed_items = ORM::factory('feed_item')
								->where($filter)
								->orderby('item_date','desc')
								->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

		$this->template->content->feed_items = $feed_items;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total Reports
		$this->template->content->total_items = $pagination->total_items;

		// Javascript Header
		$this->template->js = new View('admin/feeds_items_js');
	}

	/*
	Add Edit Layers (KML, KMZ, GeoRSS)
	*/
	function layers()
	{
		$this->template->content = new View('admin/layers');
		$this->template->content->title = Kohana::lang('ui_admin.layers');

		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'layer_id'		=> '',
			'layer_name'	  => '',
			'layer_url'	   => '',
			'layer_file'  => '',
			'layer_color'  => ''
		);

		// copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$parents_array = array();

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('layer_name','required', 'length[3,80]');
				$post->add_rules('layer_color','required', 'length[6,6]');
				$post->add_rules('layer_url','url');
				$post->add_rules('layer_file', 'upload::valid','upload::type[kml,kmz]');
				if ( empty($_POST['layer_url']) AND empty($_FILES['layer_file']['name'])
					AND empty($_POST['layer_file_old']) )
				{
					$post->add_error('layer_url', 'atleast');
				}
				if ( ! empty($_POST['layer_url']) AND
					( ! empty($_FILES['layer_file']['name']) OR !empty($_POST['layer_file_old'])) )
				{
					$post->add_error('layer_url', 'both');
				}
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$layer_id = $post->layer_id;
				$layer = new Layer_Model($layer_id);

				if( $post->action == 'd' )
				{ // Delete Action

					// Delete KMZ file if any
					$layer_file = $layer->layer_file;
					if ( ! empty($layer_file)
					AND file_exists(Kohana::config('upload.directory', TRUE).$layer_file))
						unlink(Kohana::config('upload.directory', TRUE) . $layer_file);

					$layer->delete( $layer_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));

				}
				elseif( $post->action == 'v' )
				{ // Show/Hide Action
					if ($layer->loaded==true)
					{
						if ($layer->layer_visible == 1) {
							$layer->layer_visible = 0;
						}
						else {
							$layer->layer_visible = 1;
						}
						$layer->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				elseif( $post->action == 'i' )
				{ // Delete KMZ/KML Action
					if ($layer->loaded==true)
					{
						$layer_file = $layer->layer_file;
						if ( ! empty($layer_file)
							AND file_exists(Kohana::config('upload.directory', TRUE).$layer_file))
						{
							unlink(Kohana::config('upload.directory', TRUE) . $layer_file);
						}

						$layer->layer_file = null;
						$layer->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				elseif( $post->action == 'a' )
				{ // Save Action
					$layer->layer_name = $post->layer_name;
					$layer->layer_url = $post->layer_url;
					$layer->layer_color = $post->layer_color;
					$layer->save();

					// Upload KMZ/KML
					$path_info = upload::save("layer_file");
					if ($path_info)
					{
						$path_parts = pathinfo($path_info);
						$file_name = $path_parts['filename'];
						$file_ext = $path_parts['extension'];

						if (strtolower($file_ext) == "kmz")
						{ // This is a KMZ Zip Archive, so extract
							$archive = new Pclzip($path_info);
							if ( TRUE == ($archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)) )
							{
								foreach ($archive_files as $file)
								{
									$ext_file_name = $file['filename'];
								}
							}

							if ( $ext_file_name AND
									$archive->extract(PCLZIP_OPT_PATH, Kohana::config('upload.directory')) == TRUE )
							{ // Okay, so we have an extracted KML - Rename it and delete KMZ file
								rename($path_parts['dirname']."/".$ext_file_name,
									$path_parts['dirname']."/".$file_name.".kml");

								$file_ext = "kml";
								unlink($path_info);
							}
						}

						$layer->layer_file = $file_name.".".$file_ext;
						$layer->save();
					}

					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			   // populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('layer'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
							'query_string' => 'page',
							'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
							'total_items'	 => ORM::factory('layer')
													->count_all()
						));

		$layers = ORM::factory('layer')
						->orderby('layer_name', 'asc')
						->find_all((int) Kohana::config('settings.items_per_page_admin'),
							$pagination->sql_offset);

		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->layers = $layers;

		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/layers_js');
	}

	/*
	Add Edit Reporter Levels
	*/
	function levels()
	{
		$this->template->content = new View('admin/levels');
		$this->template->content->title = Kohana::lang('ui_admin.reporter_levels');

		// setup and initialize form field names
		$form = array
		(
			'level_id'		=> '',
			'level_title'	   => '',
			'level_description'	   => '',
			'level_weight'	=> ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			//	Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('level_title','required', 'length[3,80]');
				$post->add_rules('level_description','required');
				$post->add_rules('level_weight','required');
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$level_id = $post->level_id;
				$level = new Level_Model($level_id);

				if( $post->action == 'd' )				// Delete Action
				{
					$level->delete( $level_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));

				}
				elseif( $post->action == 'a' )		   // Save Action
				{
					// SAVE Category
					$level->level_title = $post->level_title;
					$level->level_description = $post->level_description;
					$level->level_weight = $post->level_weight;
					$level->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('level'));
				$form_error = TRUE;
			}
		}

		// Pagination
		$pagination = new Pagination(array(
							'query_string' => 'page',
							'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
							'total_items'	 => ORM::factory('level')->count_all()
						));

		$levels = ORM::factory('level')
						->orderby('level_weight', 'asc')
						->find_all((int) Kohana::config('settings.items_per_page_admin'),
							$pagination->sql_offset);

		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->levels = $levels;
	}

	/**
	 * get the feed type of the feed item.
	 */
	private function _get_feed_type( $feed_item )
	{
		@$enclosures = $feed_item->get_enclosures();
		if($enclosures and ($enclosures[0]->medium == 'video' || strstr($enclosures[0]->type,'video')))
		{
			return FEED_TYPE_VIDEO;
		}
		if($enclosures and strstr($enclosures[0]->type,'image') || $enclosures[0]->medium == 'image')
		{
			return FEED_TYPE_PHOTO;
		}

		$categories = $feed_item->get_categories();
		if(!$categories || empty($categories))
		{
			return FEED_TYPE_TEXT;
		}
		// go through categories for the label having Report Type
		foreach($categories as $key => $category)
		{
			if ( ! empty($category->label))
			{
				$matched = strstr($category->label,'Report type');
				if ( ! empty($matched))
				{
					$split_array = split(':', $category->label);
					return trim($split_array[1]);
				}
			}
		}
		return FEED_TYPE_TEXT;
	}

	/**
	 * parse feed and send feed items to database
	 */
	private function _parse_feed()
	{
		// Max number of feeds to keep
		$max_feeds = 100;

		// Today's Date
		$today = strtotime('now');

		// Get All Feeds From DB
		$feeds = ORM::factory('feed')->find_all();
		foreach ($feeds as $feed)
		{
			$last_update = $feed->feed_update;

			// Has it been more than 24 hours since the last update?
			// Since its a manual refresh, we don't need to set a time
			if ( ((int)$today - (int)$last_update) > 0	)	// 86400 = 24 hours
			{
				// Parse Feed URL using Feed Helper
				$feed_data = feed::simplepie( $feed->feed_url );

				foreach ($feed_data->get_items(0,50) as $feed_data_item)
				{
					$title = $feed_data_item->get_title();
					$link = $feed_data_item->get_link();
					$description = $feed_data_item->get_description();
					$date = $feed_data_item->get_date();
					$latitude = $feed_data_item->get_latitude();
					$longitude = $feed_data_item->get_longitude();

					// Make Sure Title is Set (Atleast)
					if (isset($title) AND !empty($title ))
					{
						// We need to check for duplicates!!!
						// Maybe combination of Title + Date? (Kinda Heavy on the Server :-( )
						$dupe_count = ORM::factory('feed_item')->where('item_title',$title)->where('item_date',date("Y-m-d H:i:s",strtotime($date)))->count_all();

						if ($dupe_count == 0)
						{
							// Does this feed have a location??
							$location_id = 0;
							// STEP 1: SAVE LOCATION
							if ($latitude AND $longitude)
							{
								$location = new Location_Model();
								$location->location_name = Kohana::lang('ui_admin.unknown');
								$location->latitude = $latitude;
								$location->longitude = $longitude;
								$location->location_date = date("Y-m-d H:i:s",time());
								$location->save();
								$location_id = $location->id;
							}

							$newitem = new Feed_Item_Model();
							$newitem->feed_id = $feed->id;
							$newitem->location_id = $location_id;
							$newitem->item_title = $title;

							if (isset($description) AND !empty($description))
							{
								$newitem->item_description = $description;
							}
							if (isset($link) AND !empty($link))
							{
								$newitem->item_link = $link;
							}
							if (isset($date) AND !empty($date))
							{
								$newitem->item_date = date("Y-m-d H:i:s",strtotime($date));
							}
							// Set todays date
							else
							{
								$newitem->item_date = date("Y-m-d H:i:s",time());
							}

							if (isset($feed_type) AND ! empty($feed_type))
							{
								$newitem->feed_type = $feed_type;
							}

							$newitem->save();
						}
					}
				}

				// Get Feed Item Count
				$feed_count = ORM::factory('feed_item')->where('feed_id', $feed->id)->count_all();
				if ($feed_count > $max_feeds) {
					// Excess Feeds
					$feed_excess = $feed_count - $max_feeds;

					// Delete Excess Feeds
					foreach (ORM::factory('feed_item')
										->where('feed_id', $feed->id)
										->orderby('id', 'ASC')
										->limit($feed_excess)
										->find_all() as $del_feed)
					{
						$del_feed->delete($del_feed->id);
					}
				}

				// Set feed update date
				$feed->feed_update = strtotime('now');
				$feed->save();
			}
		}
	}

	/**
	 * Checks if parent_id for this category exists
	 * @param Validation $post $_POST variable with validation rules
	 */
	public function parent_id_chk(Validation $post)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('parent_id', $post->errors()))
			return;

		$category_id = $post->category_id;
		$parent_id = $post->parent_id;
		// This is a parent category - exit
		if ($parent_id == 0)
			return;

		$parent_exists = ORM::factory('category')
									->where('id', $parent_id)
									->find();

		if ( ! $parent_exists->loaded)
		{ // Parent Category Doesn't Exist
			$post->add_error( 'parent_id', 'exists');
		}

		if ( ! empty($category_id) AND $category_id == $parent_id)
		{ // Category ID and Parent ID can't be the same!
			$post->add_error( 'parent_id', 'same');
		}
	}
}
