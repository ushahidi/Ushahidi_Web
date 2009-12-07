<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to add/ remove categories
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Manage Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Manage_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';
		
		// If this is not a super-user account, redirect to dashboard
		if (!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
        {
             url::redirect('admin/dashboard');
		}
	}
	
	/*
	Add Edit Categories
	*/
	function index()
	{	
		$this->template->content = new View('admin/categories');
		$this->template->content->title = 'Categories';
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
			'category_id'      => '',
			'parent_id'      => '',
			'category_title'      => '',
	        'category_description'    => '',
	        'category_color'  => '',
			'category_image'  => ''
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
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('parent_id','required','numeric');
				$post->add_rules('category_title','required', 'length[3,80]');
				$post->add_rules('category_description','required');
				$post->add_rules('category_color','required', 'length[6,6]');
				$post->add_rules('category_image', 'upload::valid', 
					'upload::type[gif,jpg,png]', 'upload::size[50K]');
				$post->add_callbacks('parent_id', array($this,'parent_id_chk'));
			}
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				$category_id = $post->category_id;
				$category = new Category_Model($category_id);
				
				if( $post->action == 'd' )
				{ // Delete Action
					$category->delete( $category_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
			
				}
				else if( $post->action == 'v' )
				{ // Show/Hide Action
	            	if ($category->loaded==true)
					{
						if ($category->category_visible == 1) {
							$category->category_visible = 0;
						}
						else {
							$category->category_visible = 1;
						}
						$category->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				}
				else if( $post->action == 'i' )
				{ // Delete Image/Icon Action
	            	if ($category->loaded==true)
					{
						$category_image = $category->category_image;
						if (!empty($category_image)
						&& file_exists(Kohana::config('upload.directory', TRUE).$category_image))
							unlink(Kohana::config('upload.directory', TRUE) . $category_image);
						$category->category_image = null;
						$category->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				} 
				else if( $post->action == 'a' )
				{ // Save Action				
					$category->parent_id = $post->parent_id;
					$category->category_title = $post->category_title;
					$category->category_description = $post->category_description;
					$category->category_color = $post->category_color;
					$category->save();
					
					// Upload Image/Icon
					$filename = upload::save('category_image');
					if ($filename)
					{
						$new_filename = "category_".$category->id."_".time();

						// Resize Image to 32px if greater
						Image::factory($filename)->resize(32,32,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE) . $new_filename.".png");

						// Remove the temporary file
						unlink($filename);
						
						// Delete Old Image
						$category_old_image = $category->category_image;
						if (!empty($category_old_image)
							&& file_exists(Kohana::config('upload.directory', TRUE).$category_old_image))
							unlink(Kohana::config('upload.directory', TRUE).$category_old_image);
						
						// Save
						$category->category_image = $new_filename.".png";
						$category->save();
					}
					
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
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
                            'total_items'    => ORM::factory('category')
													->where('parent_id','0')
													->count_all()
                        ));

        $categories = ORM::factory('category')
						->where('parent_id','0')
                        ->orderby('category_title', 'asc')
                        ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
                            $pagination->sql_offset);
		 $parents_array = ORM::factory('category')
            ->where('parent_id','0')
            ->select_list('id', 'category_title');
        // add none to the list
        $parents_array[0] = "--- Top Level Category ---";
		
		$this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->categories = $categories;
		
		$this->template->content->parents_array = $parents_array;

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

        // Javascript Header
        $this->template->colorpicker_enabled = TRUE;
        $this->template->js = new View('admin/categories_js');
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
	        'organization_id'      => '',
			'organization_name'      => '',
	        'organization_description'    => '',
	        'organization_website'  => '',
			'organization_email'  => '',
			'organization_phone1'  => '',
			'organization_phone2'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{ // Add some rules, the input field, followed by a list of checks, carried out in order
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
					$form_action = "DELETED";
				
				}
				else if( $post->action == 'v' )			
				{ // Show/Hide Action
	            	if ($organization->loaded==true)
					{
						if ($organization->organization_active == 1) {
							$organization->organization_active = 0;
						}
						else {
							$organization->organization_active = 1;
						}
						$organization->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				}
				else if( $post->action == 'a' ) 		
				{ // Save Action
					$organization->organization_name = $post->organization_name;
					$organization->organization_description = $post->organization_description;
					$organization->organization_website = $post->organization_website;
					$organization->organization_email = $post->organization_email;
					$organization->organization_phone1 = $post->organization_phone1;
					$organization->organization_phone2 = $post->organization_phone2;
					$organization->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}       
	        }
	        else
			{ // No! We have validation errors, we need to show the form again, with the errors
				
	             // repopulate the form fields
		         $form = arr::overwrite( $form, $post->as_array() ); 

               // populate the error fields, if any
                $errors = arr::overwrite($errors, 
					$post->errors('organization'));
                $form_error = TRUE;
            }
        }
		
        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'    =>
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
	        'page_id'      => '',
			'page_title'      => '',
			'page_tab'      => '',
	        'page_description'    => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
	         //  Add some filters
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
					$form_action = "DELETED";
				
				}
				else if( $post->action == 'v' )			
				{ // Show/Hide Action
	            	if ($page->loaded==true)
					{
						if ($page->page_active == 1) {
							$page->page_active = 0;
						}
						else {
							$page->page_active = 1;
						}
						$page->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				}
				else if( $post->action == 'a' ) 		
				{ // Save Action
					$page->page_title = $post->page_title;
					$page->page_tab = $post->page_tab;
					$page->page_description = $post->page_description;
					$page->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}       
	        }
	        else
			{ // No! We have validation errors, we need to show the form again, with the errors
				
	             // repopulate the form fields
		         $form = arr::overwrite( $form, $post->as_array() ); 

               // populate the error fields, if any
                $errors = arr::overwrite($errors, 
					$post->errors('page'));
                $form_error = TRUE;
            }
        }
		
        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'    =>
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
	        'feed_id'      => '',
			'feed_name'      => '',
	        'feed_url'    => '',
	        'feed_active'  => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		if( $_POST ) 
		{
			$post = Validation::factory( $_POST );
			
			 //  Add some filters
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
				if ( $post->action == 'd' ) { 					// Delete Action
					if ($feed->loaded==true)
					{
						ORM::factory('feed_item')->where('feed_id',$feed_id)->delete_all();
					}
					$feed->delete( $feed_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
				} else if($post->action == 'v') {				// Active/Inactive Action
					if ($feed->loaded==true)
					{
						if ($feed->feed_active == 1) {
							$feed->feed_active = 0;
						}
						else {
							$feed->feed_active = 1;
						}
						$feed->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				}else if( $post->action == 'r' ) { 
					$this->_parse_feed();
				} else {										// Save Action
					// SAVE Feed
					$feed->feed_name = $post->feed_name;
					$feed->feed_url = $post->feed_url;
					$feed->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
				}
				
			} else {
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
                            'total_items'    => ORM::factory('feed')->count_all()
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
	function feeds_items($feed_id = NULL)
	{
		$this->template->content = new View('admin/feeds_items');
		
		if ( isset($feed_id)  && !empty($feed_id) )
		{
			$filter = " feed_id = '" . $feed_id . "' ";
		}
		else
		{
			$filter = " 1=1";
		}
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'   => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('feed_item')
				->where($filter)
				->count_all()
		));

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
		$this->template->content->title = 'Layers';
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
			'layer_id'      => '',
			'layer_name'      => '',
	        'layer_url'    => '',
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
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('layer_name','required', 'length[3,80]');
				$post->add_rules('layer_color','required', 'length[6,6]');
				$post->add_rules('layer_url','url');
				$post->add_rules('layer_file', 'upload::valid','upload::type[kml,kmz]');
				if ( empty($_POST['layer_url']) && empty($_FILES['layer_file']['name'])
				 	&& empty($_POST['layer_file_old']) )
				{
					$post->add_error('layer_url', 'atleast');
				}
				if ( !empty($_POST['layer_url']) && 
					(!empty($_FILES['layer_file']['name']) || !empty($_POST['layer_file_old'])) )
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
					if (!empty($layer_file)
					&& file_exists(Kohana::config('upload.directory', TRUE).$layer_file))
						unlink(Kohana::config('upload.directory', TRUE) . $layer_file);
						
					$layer->delete( $layer_id );
					$form_saved = TRUE;
					$form_action = "DELETED";
			
				}
				else if( $post->action == 'v' )
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
						$form_action = "MODIFIED";
					}
				}
				else if( $post->action == 'i' )
				{ // Delete KMZ/KML Action
	            	if ($layer->loaded==true)
					{
						$layer_file = $layer->layer_file;
						if (!empty($layer_file)
						&& file_exists(Kohana::config('upload.directory', TRUE).$layer_file))
							unlink(Kohana::config('upload.directory', TRUE) . $layer_file);
						$layer->layer_file = null;
						$layer->save();
						$form_saved = TRUE;
						$form_action = "MODIFIED";
					}
				} 
				else if( $post->action == 'a' )
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
							
							if ( $ext_file_name && 
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
					$form_action = "ADDED/EDITED";
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
                            'total_items'    => ORM::factory('layer')
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
		$this->template->content->title = 'Reporter Levels';
		
		// setup and initialize form field names
		$form = array
		(
			'level_id'      => '',
			'level_title'      => '',
			'level_description'    => '',
			'level_weight'  => ''
		);
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			//  Add some filters
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
					$form_action = "DELETED";
			
				}
				else if( $post->action == 'a' ) 		// Save Action
				{		
					// SAVE Category
					$level->level_title = $post->level_title;
					$level->level_description = $post->level_description;
					$level->level_weight = $post->level_weight;
					$level->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
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
                            'total_items'    => ORM::factory('level')->count_all()
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
	
	/*
	Add Edit Reporters
	*/
	function reporters()
	{	
		$this->template->content = new View('admin/reporters');
		$this->template->content->title = 'Reporters';

		// setup and initialize form field names
		$form = array
		(
			'reporter_id' => '',    
			'service_id' => '',
			'level_id' => '',
			'service_userid' => '',
			'service_account' => '',
			'reporter_first' => '',
			'reporter_last' => '',
			'reporter_email' => '',
			'reporter_phone' => '',
			'reporter_ip' => '',
			'reporter_date' => ''
		);
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

			// check, has the form been submitted, if so, setup validation
			if ($_POST)
			{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			    //  Add some filters
			$post->pre_filter('trim', TRUE);

			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('service_id','required');				
				// we also require either service_userid or service_account, not necessarily both
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				$reporter_id = $post->reporter_id;
				$reporter = new Reporter_Model($reporter_id);
				
				if( $post->action == 'd' )				// Delete Action
				{
					$reporter->delete( $reporter_id );
					$form_saved = TRUE;
					$form_action = "DELETED";

				}
				else if( $post->action == 'a' ) 		// Save Action
				{		
					// SAVE Reporter    
					$reporter->service_id = $post->service_id;
					$reporter->level_id = $post->level_id;
					/*$reporter->service_userid = $post->service_userid;
					$reporter->service_account = $post->service_account;*/
					/*$reporter->reporter_first = $post->reporter_first;
					$reporter->reporter_last = $post->reporter_last;
					$reporter->reporter_email = $post->reporter_email;
					$reporter->reporter_phone = $post->reporter_phone;
					$reporter->reporter_ip = $post->reporter_ip;
					$reporter->reporter_date = $post->reporter_date;*/
					
					$reporter->save();
					$form_saved = TRUE;
					$form_action = "ADDED/EDITED";
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
		                    'total_items'    => ORM::factory('reporter')->count_all()
		                ));

		$reporters = ORM::factory('reporter')
		                ->orderby('reporter_first', 'asc')
		                ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
		                    $pagination->sql_offset);

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->reporters = $reporters;

		// Level and Service Arrays
		$this->template->content->level_array = Level_Model::get_array();
		$this->template->content->service_array = Service_Model::get_array();

	}	
	/**
	 * setup simplepie
	 */
	private function _setup_simplepie( $feed_url )
	{
		$data = new SimplePie();
	
		// Convert To GeoRSS feed
		$geocoder = new Geocoder();
		$georss_feed = $geocoder->geocode_feed($feed_url);
	
		$data->set_raw_data( $georss_feed );
		$data->enable_cache(false);
		$data->enable_order_by_date(true);
		$data->init();
		$data->handle_content_type();

		return $data;
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
				$feed_data = $this->_setup_simplepie( $feed->feed_url );

				foreach($feed_data->get_items(0,50) as $feed_data_item)
				{
					$title = $feed_data_item->get_title();
					$link = $feed_data_item->get_link();
					$description = $feed_data_item->get_description();
					$date = $feed_data_item->get_date();
					$latitude = $feed_data_item->get_latitude();
					$longitude = $feed_data_item->get_longitude();
					
					// Make Sure Title is Set (Atleast)
					if (isset($title) && !empty($title ))
					{
						// We need to check for duplicates!!!
						// Maybe combination of Title + Date? (Kinda Heavy on the Server :-( )
						$dupe_count = ORM::factory('feed_item')->where('item_title',$title)->where('item_date',date("Y-m-d H:i:s",strtotime($date)))->count_all();

						if ($dupe_count == 0)
						{
							// Does this feed have a location??
							$location_id = 0;
							// STEP 1: SAVE LOCATION
							if ($latitude && $longitude)
							{
								$location = new Location_Model();
								$location->location_name = "Unknown";
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
							if (isset($description) && !empty($description))
							{
								$newitem->item_description = $description;
							}
							if (isset($link) && !empty($link))
							{
								$newitem->item_link = $link;
							}
							if (isset($date) && !empty($date))
							{
								$newitem->item_date = date("Y-m-d H:i:s",strtotime($date));
							}
							// Set todays date
							else
							{
								$newitem->item_date = date("Y-m-d H:i:s",time());
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
		
		if (!$parent_exists->loaded)
		{ // Parent Category Doesn't Exist
			$post->add_error( 'parent_id', 'exists');
		}
		
		if (!empty($category_id) && $category_id == $parent_id)
		{ // Category ID and Parent ID can't be the same!
			$post->add_error( 'parent_id', 'same');
		}
	}
}
