<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to add/ remove categories, forms, pages,
 * news feeds, layers and managing the scheduler and public listings
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

	public function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';

		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}

	/**
	 * Add Edit Categories
	 */
	public function index()
	{
		$this->template->content = new View('admin/categories');
		$this->template->content->title = Kohana::lang('ui_admin.categories');

		// Locale (Language) Array
		$locales = locale::get_i18n();

		// Setup and initialize form field names
		$form = array
		(
			'action' => '',
			'category_id' => '',
			'parent_id' => '',
			'category_title' => '',
			'category_description' => '',
			'category_color' => '',
			'category_image' => '',
			'category_image_thumb' => ''
		);

		// Add the different language form keys for fields
		foreach ($locales as $lang_key => $lang_name)
		{
			$form['category_title_'.$lang_key] = '';
		}

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$parents_array = array();

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Fetch the post data
			$post_data = array_merge($_POST, $_FILES);
			
			// Extract category-specific  information
			$category_data = arr::extract($post_data, 'parent_id', 'category_title', 'category_description', 'category_color');
			
			// Extract category image and category languages for independent validation
			$secondary_data = arr::extract($post_data, 'category_image', 'category_title_lang', 'action');
			
			// Setup validation for the secondary data
			$post = Validation::factory($secondary_data)
						->pre_filter('trim', TRUE);
						
			// Add validation for the add/edit action
			if ($post->action == 'a')
			{
				$post->add_rules('category_image', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[50K]');

				// Add the different language form keys for fields
				foreach ($locales as $lang_key => $lang_name)
				{
					$post->add_rules('category_title_lang['.$lang_key.']','length[3,80]');
				}
			}
			
			// Category instance for the operation
			$category = (! empty($_POST['category_id']) AND Category_Model::is_valid_category($_POST['category_id']))
				? new Category_Model($_POST['category_id'])
				: new Category_Model();
			
			
			// Check the specified action
			if ($post->action == 'a')
			{
				// Test to see if things passed the rule checks
				if ($category->validate($category_data) AND $post->validate())
				{
					// Save the category
					$category->save();
					
					// Get the category localization
					$languages = ($category->loaded) ? Category_Lang_Model::category_langs($category->id) : FALSE;
					
					$category_lang = (isset($languages[$category->id])) ? $languages[$category->id] : FALSE;
					
					// Save localizations
					foreach ($post->category_title_lang as $lang_key => $localized_category_name)
					{
						$cl = (isset($category_lang[$lang_key]['id']))
							? ORM::factory('category_lang',$category_lang[$lang_key]['id'])
							: ORM::factory('category_lang');
						
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
				else
				{
					// Validation failed

					// Repopulate the form fields
					$form = arr::overwrite($form, array_merge($category_data->as_array(), $post->as_array()));

					// populate the error fields, if any
					$errors = arr::overwrite($errors, 
						array_merge($category_data->errors('category'), $post->errors('category')));

					$form_error = TRUE;
				}
				
			}
			elseif ($post->action == 'd')
			{
				// Delete action
				if ($category->loaded)
				{
					ORM::factory('category_lang')
						->where(array('category_id' => $category->id))
						->delete_all();
						
					// @todo Delete the category image
					
					// Delete category itself - except if it is trusted
					ORM::factory('category')
						->where('category_trusted != 1')
						->delete($category->id);
						
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
			elseif( $post->action == 'v')
			{ 
				// Show/Hide Action
				if ($category->loaded)
				{
					$category->category_visible = ($category->category_visible == 1)? 0 : 1;
					$category->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
				}
			}
			elseif( $post->action == 'i')
			{ 
				// Delete Image/Icon Action
				if ($category->loaded)
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

					$category->category_image = NULL;
					$category->category_image_thumb = NULL;
					$category->save();
					
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
				}

			}
		}

		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items' => ORM::factory('category')
			->where('parent_id','0')
			->count_all()
		));

		$categories = ORM::factory('category')
						->with('category_lang')
						->where('parent_id','0')
						->orderby('category_position', 'asc')
						->orderby('category_title', 'asc')
						->find_all($this->items_per_page, $pagination->sql_offset);

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
		$this->template->tablerowsort_enabled = TRUE;
		$this->template->js = new View('admin/categories_js');
		$this->template->form_error = $form_error;

		$this->template->content->locale_array = $locales;
		$this->template->js->locale_array = $locales;
	}
	
	/**
	 * Sorts categories
	 */
	public function category_sort()
	{
		$this->auto_render = FALSE;
		$this->template = "";
		
		if ($_POST)
		{
			if (isset($_POST['categories'])
				AND ! empty($_POST['categories'])
				)
			{
				$categories = array_map('trim', explode(',', $_POST['categories']));
				$i = 0;
				$parent_id = 0;
				foreach ($categories as $category_id)
				{
					if ($category_id)
					{
						$category = ORM::factory('category', $category_id);
						if ($category->loaded)
						{
							if ($i == 0 AND $category->parent_id != 0)
							{ // ERROR!!!!!!!! WHY ARE YOU TRYING TO PLACE A SUBCATEGORY ABOVE A CATEGORY???
								echo "ERROR";
								return;
							}
							
							if ($category->parent_id == 0)
							{
								// Set Parent ID
								$parent_id = $category->id;
							}
							else
							{
								if ($parent_id)
								{
									$category->parent_id = $parent_id;
								}
							}							
							
							$category->category_position = $i;
							$category->save();
						}
					}
					
					$i++;
				}
			}
		}
	}
	
	/**
	 * Manage Public Listing for External Applications
	 */
	public function publiclisting()
	{
		$this->template->content = new View('admin/publiclisting');
		
		$settings = ORM::factory('settings', 1);
		
		$this->template->content->encoded_stat_id = base64_encode($settings->stat_id);
		$this->template->content->encoded_stat_key = base64_encode($settings->stat_key);
	}


	/**
	 * Add Edit Pages
	 */
	public function pages()
	{
		$this->template->content = new View('admin/pages');

		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'page_id' => '',
			'page_title' => '',
			'page_tab' => '',
			'page_description' => ''
		);
		
		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			$page = (isset($_POST['page_id']) AND Page_Model::is_valid_page($_POST['page_id']))
				? ORM::factory('page', $_POST['page_id'])
				: new Page_Model();
				
			
			$post = array_merge($_POST, $_FILES);
			$post = array_merge($post, array("id"=>$page->id));
			Event::run('ushahidi_action.page_submit', $post);
			
			// Check for the specified action
			if ($_POST['action'] == 'a')
			{
				// Manually extract the data to be validated from $_POST
				$data = arr::extract($_POST, 'page_id', 'page_title', 'page_description', 'page_tab');
				
				if ($page->validate($data))
				{
					$page->save();
					Event::run('ushahidi_action.page_edit', $page);
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
					array_fill_keys($form, '');
				}
				else
				{
					// Repopulate the form fields
					$form = arr::overwrite($form, $data->as_array());

					// Populate the error fields, if any
					$errors = arr::overwrite($errors, $data->errors('page'));
					$form_error = TRUE;
				}
			}
			elseif ($_POST['action'] == 'd')
			{ 
				// Delete action
				if ($page->loaded)
				{
					$page->delete();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}

			}
			elseif ($_POST['action'] == 'v')
			{ 
				// Show/Hide Action
				if ($page->loaded)
				{
					$page->page_active = ($page->page_active == 1)? 0 : 1;
					$page->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
				}
			}
		}

		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items' => ORM::factory('page')->count_all()
		));

		$pages = ORM::factory('page')
					->orderby('page_title', 'asc')
					->find_all($this->items_per_page, $pagination->sql_offset);

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


	/**
	 * Add Edit News Feeds
	 */
	public function feeds()
	{
		$this->template->content = new View('admin/feeds');

		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'feed_id' => '',
			'feed_name' => '',
			'feed_url' => '',
			'feed_active' => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		if( $_POST )
		{
			// Feed_Model instance
			$feed = (isset($_POST['feed_id']) AND Feed_Model::is_valid_feed($_POST['feed_id']))
						? new Feed_Model($_POST['feed_id'])
						: new Feed_Model();
			
			if ($_POST['action'] == 'a')		// Add Action
			{
				// Manually extract the data to be validated
				$data = arr::extract($_POST, 'feed_name', 'feed_url');
				
				// Test validation
				if ($feed->validate($data))
				{
					$feed->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
				else
				{
					// Repopulate the form fields
					$form = arr::overwrite($form, $data->as_array());

					// Populate the error fields, if any
					$errors = arr::overwrite($errors, $data->errors('feeds'));
					$form_error = TRUE;
				}
			}
			elseif ($_POST['action'] == 'd')
			{
				// Delete Action
				if ($feed->loaded == TRUE)
				{
					ORM::factory('feed_item')->where('feed_id', $feed->id)->delete_all();
					$feed->delete();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
			elseif($_POST['action'] == 'v')
			{
				// Active/Inactive Action
				if ($feed->loaded == TRUE)
				{
					$feed->feed_active =  ($feed->feed_active == 1) ? 0 : 1;
					$feed->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
				}
			}
			elseif ($_POST['action'] == 'r')
			{
				$this->_parse_feed();
			}
		}

		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items'	 => ORM::factory('feed')->count_all()
		));

		$feeds = ORM::factory('feed')
					->orderby('feed_name', 'asc')
					->find_all($this->items_per_page, $pagination->sql_offset);

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

	/**
	 * View/Edit News Feed Items
	 */
	public function feeds_items()
	{
		$this->template->content = new View('admin/feeds_items');
		
		// Check if the last segment of the URI is numeric and grab it
		$feed_id = is_numeric($this->uri->last_segment())
					? $this->uri->last_segment()
					: "";
		
		// SQL filter
		$filter = (isset($feed_id)  AND !empty($feed_id))
					? " feed_id = '" . $feed_id . "' "
					: " 1=1";

		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// Check for form submission
		if ( $_POST )
		{
			$post = Validation::factory($_POST);

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
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items' => ORM::factory('feed_item')
							->where($filter)
							->count_all()
		));

		$feed_items = ORM::factory('feed_item')
						->where($filter)
						->orderby('item_date','desc')
						->find_all($this->items_per_page, $pagination->sql_offset);

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

	/**
	 * Add Edit Layers (KML, KMZ, GeoRSS)
	 */
	public function layers()
	{
		$this->template->content = new View('admin/layers');
		$this->template->content->title = Kohana::lang('ui_admin.layers');

		// Setup and initialize form field names
		$form = array
		(
			'action' => '',
			'layer_id' => '',
			'layer_name' => '',
			'layer_url'	=> '',
			'layer_file' => '',
			'layer_color' => ''
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$parents_array = array();

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Fetch the submitted data
			$post_data = array_merge($_POST, $_FILES);
			
			// Layer instance for the actions
			$layer = (isset($post_data['layer_id']) AND Layer_Model::is_valid_layer($post_data['layer_id']))
						? new Layer_Model($post_data['layer_id'])
						: new Layer_Model();
						
			// Check for action
			if ($post_data['action'] == 'a')
			{
				// Manually extract the primary layer data
				$layer_data = arr::extract($post_data, 'layer_name', 'layer_color', 'layer_url', 'layer_file_old');
				
				// Grab the layer file to be uploaded
				$layer_data['layer_file'] = isset($post_data['layer_file']['name'])? $post_data['layer_file']['name'] : NULL;
				
				// Extract the layer file for upload validation
				$other_data = arr::extract($post_data, 'layer_file');
				
				// Set up validation for the layer file
				$post = Validation::factory($other_data)
						->pre_filter('trim', TRUE)
						->add_rules('layer_file', 'upload::valid','upload::type[kml,kmz]');
				
				// Test to see if validation has passed
				if ($layer->validate($layer_data) AND $post->validate())
				{
					// Success! SAVE
					$layer->save();
					
					$path_info = upload::save("layer_file");
					if ($path_info)
					{
						$path_parts = pathinfo($path_info);
						$file_name = $path_parts['filename'];
						$file_ext = $path_parts['extension'];

						if (strtolower($file_ext) == "kmz")
						{ 
							// This is a KMZ Zip Archive, so extract
							$archive = new Pclzip($path_info);
							if (TRUE == ($archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)))
							{
								foreach ($archive_files as $file)
								{
									$ext_file_name = $file['filename'];
								}
							}

							if ($ext_file_name AND $archive->extract(PCLZIP_OPT_PATH, Kohana::config('upload.directory')) == TRUE)
							{ 
								// Okay, so we have an extracted KML - Rename it and delete KMZ file
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
					array_fill_keys($form, '');
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
				else
				{
					// Validation failed

					// Repopulate the form fields
					$form = arr::overwrite($form, array_merge($layer_data->as_array(), $post->as_array()));

					// Ropulate the error fields, if any
					$errors = arr::overwrite($errors, array_merge($layer_data->errors('layer'), $post->errors('layer')));
					$form_error = TRUE;
				}
				
			}
			elseif ($post_data['action'] == 'd')
			{
				// Delete action
				if ($layer->loaded)
				{
					// Delete KMZ file if any
					$layer_file = $layer->layer_file;
					if ( ! empty($layer_file) AND file_exists(Kohana::config('upload.directory', TRUE).$layer_file))
					{
						unlink(Kohana::config('upload.directory', TRUE) . $layer_file);
					}

					$layer->delete();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
			elseif ($post_data['action'] == 'v')
			{
				// Show/Hide Action
				if ($layer->loaded == TRUE)
				{
					$layer->layer_visible =  ($layer->layer_visible == 1)? 0 : 1;
					$layer->save();
					
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
				}
			}
			elseif ($post_data['action'] == 'i')
			{
				// Delete KML/KMZ action
				if ($layer->loaded == TRUE)
				{
					$layer_file = $layer->layer_file;
					if ( ! empty($layer_file) AND file_exists(Kohana::config('upload.directory', TRUE).$layer_file))
					{
						unlink(Kohana::config('upload.directory', TRUE) . $layer_file);
					}

					$layer->layer_file = null;
					$layer->save();
					
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
				}
			}
		}

		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items' => ORM::factory('layer')->count_all()
		));

		$layers = ORM::factory('layer')
					->orderby('layer_name', 'asc')
					->find_all($this->items_per_page, $pagination->sql_offset);

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

	/**
	 * Add Edit Reporter Levels
	 */
	public function levels()
	{
		$this->template->content = new View('admin/levels');
		$this->template->content->title = Kohana::lang('ui_admin.reporter_levels');

		// setup and initialize form field names
		$form = array
		(
			'level_id' => '',
			'level_title' => '',
			'level_description' => '',
			'level_weight' => ''
		);
		
		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Level_Model instance for the opertation
			$level = (isset($_POST['level_id']) AND Level_Model::is_valid_level($_POST['level_id']))
						? new Level_Model($_POST['level_id'])
						: new Level_Model();

			if ($_POST['action'] == 'a')
			{
				// Manually extract the data to be validated
				$data = arr::extract($_POST, 'level_title', 'level_description', 'level_weight');
				
				if ($level->validate($data))
				{
					$level->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
				// No! We have validation errors, we need to show the form again, with the errors
				else
				{
					// Repopulate the form fields
					$form = arr::overwrite($form, $data->as_array());

					// Ropulate the error fields, if any
					$errors = arr::overwrite($errors, $data->errors('level'));
					$form_error = TRUE;
				}
			}
			elseif ($_POST['action'] == 'd')
			{
				if ($level->loaded)
				{
					// Levels are tied to reporters, therefore nullify 
					// @todo Reporter_Model::delink_level($level_id)
					$level->delete();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
		}

		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items' => ORM::factory('level')->count_all()
		));

		$levels = ORM::factory('level')
					->orderby('level_weight', 'asc')
					->find_all($this->items_per_page, $pagination->sql_offset);

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
				if ($feed_count > $max_feeds)
				{
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
}
