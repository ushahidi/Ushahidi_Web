<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 24 - Henry Addo 2010-09-27
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

require_once('ApiActions.php');

class AdminCategory
{
    
    private $data;
    private $items;
    private $table_prefix;
    private $api_actions;
    private $response_type;
    private $domain;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->response_type = '';
        $this->domain = $this->api_actions->_get_domain();
    }

    /**
     * Add new category 
     *
     * @param string response_type - XML or JSON
     *
     * @return Array
     */
    public function _add_category($response_type)
    {
        $ret_value = $this->_submit_categories();
		
        if($ret_value == 0 )
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
				"error" => $this->api_actions->_get_error_msg(0)
			);
			
		} 
        else if( $ret_value == 1 ) 
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
				"error" => $this->api_actions->
                    _get_error_msg(003,'',$this->error_messages)
			);
		} 
        else 
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
				"error" => $this->api_actions->_get_error_msg(004)
			);
		}

		if($response_type == 'json')
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($reponse);
		} 
        else 
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_XML($reponse, array());
		}

		return $this->ret_json_or_xml;
    }
    
    /**
     * Edit existing category
     *
     * @param int category_id - the category id to be edited.
     * @param string response_type - XML or JSON
     * 
     * @return array
     */
    public function _edit_category($category_id,$response_type)
    {
        // setup and initialize form field names
		$form = array
	    (
			'category_id'      => '',
			'parent_id'      => '',
			'category_title'      => '',
	        'category_description'    => '',
	        'category_color'  => '',
			'category_image'  => ''
	    );

        	// copy the form as errors, so the errors will be stored 
        //with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$parents_array = array();
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't 
            //overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			// Add some rules, the input field, followed by a list 
            //of checks, carried out in order
			$post->add_rules('parent_id','required','numeric');
			$post->add_rules('category_title','required', 
                        'length[3,80]');
			$post->add_rules('category_description','required');
			$post->add_rules('category_color','required', 
                        'length[6,6]');
			$post->add_rules('category_image', 'upload::valid', 
					'upload::type[gif,jpg,png]', 'upload::size[50K]');
			$post->add_callbacks('parent_id', array($this,
                        'parent_id_chk'));

            // Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				$category_id = $post->category_id;
				$category = new Category_Model($category_id);
                
                // Save Action				
				$category->parent_id = $post->parent_id;
				$category->category_title = $post->category_title;
				$category->category_description = 
                $post->category_description;
				$category->category_color = $post->category_color;
				$category->save();
				
                //optional
                if(!empty($post->category_image))
                {
				    // Upload Image/Icon
					$filename = upload::save('category_image');
					if ($filename)
					{
					    $new_filename = "category_".
                                $category->id."_".time();

						// Resize Image to 32px if greater
						Image::factory($filename)->resize(32,32,
                            Image::HEIGHT)->save(Kohana::config('upload.directory',
                                TRUE) . $new_filename.".png");

						// Remove the temporary file
						unlink($filename);
						
						// Delete Old Image
						$category_old_image = $category->category_image;
						if (!empty($category_old_image)
							&& file_exists(Kohana::config(
                                'upload.directory', TRUE).
                                $category_old_image))
							unlink(Kohana::config('upload.directory', TRUE).
                                $category_old_image);
						
						    // Save
						$category->category_image = $new_filename.".png";
						$category->save();

                    }
                }
            }
             // No! We have validation errors, we need to show the form
            //again, with the errors
	        else
			{

                // populate the error fields, if any
                $errors = arr::overwrite($errors, 
                        $post->errors('category'));
                foreach($errors as $error_item => $error_description)
                {
                    if( !is_array($error_description))
                    {
                        $this->error_messages .= $error_description;
                        
                        if($error_description != end($errors))
                        {
                            $this->error_messages .= " - ";
                        }
                    }
                }
                
                $ret_value = 1; // validation error
            }
    
        }
        else
        {   
            $ret_value = 2;
        }

      	return $this->response($ret_value);
    }

    /**
     * Delete existing category
     *
     * @param int category_id - the category id to be deleted.
     *
     * @return array
     */
    public function _del_category($category_id)
    {
         // setup and initialize form field names
		$form = array
	    (
			'category_id'      => '',
	    );

        	// copy the form as errors, so the errors will be stored 
        //with keys corresponding to the form field names
	    $errors = $form;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't 
            //overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			//if ($post->action == 'a')		// Add Action
			//{
			// Add some rules, the input field, followed by a list 
            //of checks, carried out in order
			$post->add_rules('category_id','required','numeric');

            // Test to see if things passed the rule checks
	        if ($post->validate())
	        {
			    $category_id = $post->category_id;
			    $category = new Category_Model($category_id);
				
			    if( $post->action == 'd' )
			    { // Delete Action
				    $category->delete( $category_id );
			
			    }
            }
        }

    }

    /**
     * Reponse
     * @param int ret_value
     */
    public function responses($ret_value)
    {
        if($ret_value == 0 )
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
				"error" => $this->api_actions->_get_error_msg(0)
			);
			
		} 
        else if( $ret_value == 1 ) 
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
				"error" => $this->api_actions->
                    _get_error_msg(003,'',$this->error_messages)
			);
		} 
        else 
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
				"error" => $this->api_actions->_get_error_msg(004)
			);
		}

		if($response_type == 'json')
        {
			= $this->api_actions->
                _array_as_JSON($reponse);
		} 
        else 
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_XML($reponse, array());
		}

		return $this->ret_json_or_xml;

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

    /**
     * Submit categories details
     *
     * @return int
     */
    private function _submit_categories()
    {
        	
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
	    
		// copy the form as errors, so the errors will be stored 
        //with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$parents_array = array();
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        // Instantiate Validation, use $post, so we don't 
            //overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list 
                //of checks, carried out in order
				$post->add_rules('parent_id','required','numeric');
				$post->add_rules('category_title','required', 
                        'length[3,80]');
				$post->add_rules('category_description','required');
				$post->add_rules('category_color','required', 
                        'length[6,6]');
				$post->add_rules('category_image', 'upload::valid', 
					'upload::type[gif,jpg,png]', 'upload::size[50K]');
				$post->add_callbacks('parent_id', array($this,
                            'parent_id_chk'));
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
					}
				}
				else if( $post->action == 'i' )
				{ // Delete Image/Icon Action
	            	if ($category->loaded==true)
					{
						$category_image = $category->category_image;
						if (!empty($category_image)
						&& file_exists(Kohana::config('upload.directory',
                                TRUE).$category_image))
							unlink(Kohana::config('upload.directory',
                                        TRUE) . $category_image);
						$category->category_image = null;
						$category->save();
						$form_saved = TRUE;
					}
				} 
				else if( $post->action == 'a' )
				{ // Save Action				
					$category->parent_id = $post->parent_id;
					$category->category_title = $post->category_title;
					$category->category_description = 
                        $post->category_description;
					$category->category_color = $post->category_color;
					$category->save();
				
                    //optional
                    if(!empty($post->category_image))
                    {
					    // Upload Image/Icon
					    $filename = upload::save('category_image');
					    if ($filename)
					    {
						    $new_filename = "category_".
                                $category->id."_".time();

						    // Resize Image to 32px if greater
						    Image::factory($filename)->resize(32,32,
                                Image::HEIGHT)
							    ->save(Kohana::config('upload.directory',
                                        TRUE) . $new_filename.".png");

						    // Remove the temporary file
						    unlink($filename);
						
						    // Delete Old Image
						    $category_old_image = $category->category_image;
						    if (!empty($category_old_image)
							    && file_exists(Kohana::config(
                                    'upload.directory', TRUE).
                                    $category_old_image))
							    unlink(Kohana::config('upload.directory', TRUE).
                                    $category_old_image);
						
						    // Save
						    $category->category_image = $new_filename.".png";
						    $category->save();
					    }
                    }
					
					$form_saved = TRUE;
					
					// Empty $form array
					array_fill_keys($form, '');
				}
	        }
            // No! We have validation errors, we need to show the form
            //again, with the errors
	        else
			{

                // populate the error fields, if any
                $errors = arr::overwrite($errors, 
                        $post->errors('category'));
                foreach($errors as $error_item => $error_description)
                {
                    if( !is_array($error_description))
                    {
                        $this->error_messages .= $error_description;
                        
                        if($error_description != end($errors))
                        {
                            $this->error_messages .= " - ";
                        }
                    }
                }
                
                return 1; // validation error
            }
        }
        else
        {
            return 2; // Not sent by post method.
        }

    }


}
