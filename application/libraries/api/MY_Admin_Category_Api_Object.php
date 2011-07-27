<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 25 - Emmanuel Kala 2010-10-26
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

class Admin_Category_Api_Object extends Api_Object_Core {
    
    public function __construct($api_service) 
    {
        parent::__construct($api_service);
    }

    /**
     * API service request handler
     */
    public function perform_task()
    {
    }

    /**
     * Handles admin category actions performed via the API service
     */
    public function category_action()
    {
        $action = ''; // Will hold the report action
        
        // Authenticate the user
        if ( ! $this->api_service->_login())
        {
            $this->set_error_message($this->response(2));
            return;
        }


        //Check if the action has been specified
        if ( ! $this->api_service->verify_array_index($this->request,'action'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001,'action')));
            
            return;
        }
        else
        {
            $action = $this->request['action'];
        }

        // Route category actions to their various handlers
        switch ($action)
        {
            // Delete category
            case "delete":
                // Check if the category id has been specified
                if ( ! $this->api_service->verify_array_index($this->request,'category_id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(
                            001, 'category_id')));
                    return;
                }
                else
                {
                    $this->_del_category();
                }
            break;
            
            // Edit category
            case "edit":
                // Check if the category id has been specified
                if ( ! $this->api_service->verify_array_index($this->request,'category_id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(
                            001, 'category_id')));
                    return;
                }
                else
                {
                    $this->_edit_category();
                }
            break;

            //Add category
            case "add":
                $this->_add_category();
            break;

            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)));
        }
    }
    
    /**
     * Add new category 
     *
     * @param string response_type - XML or JSON
     *
     * @return Array
     */
    private function _add_category()
    {        
        $ret_value = $this->_submit_categories();

        $this->response_data = $this->response($ret_value, 
            $this->error_messages);
    }
    
    /**
     * Edit existing category
     *
     * @param string response_type - XML or JSON
     * authenticated
     * 
     * @return array
     */
    private function _edit_category()
    {
        // Setup and initialize form field names
        $form = array
        (
            'category_id' => '',
            'parent_id' => '',
            'category_title' => '',
            'category_description' => '',
            'category_color' => '',
            'category_image' => ''
        );

        // Copy the form as errors, so the errors will be stored 
        // with keys corresponding to the form field names
        $errors = $form;
        $parents_array = array();
        
        $ret_value = 0;
        
        // Check, has the form been submitted, if so, setup validation
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
                
                // Update Action
                $category_id = $post->category_id;
                $category = new Category_Model($category_id);
                $category->parent_id = $post->parent_id;
                $category->category_title = $post->category_title;
                $category->category_description = 
                $post->category_description;
                $category->category_color = $post->category_color;
                $category->save();
                
                // Optional
                if ( ! empty($post->category_image))
                {
                    // Upload Image/Icon
                    $filename = upload::save('category_image');
                    
                    if ($filename)
                    {
                        $new_filename = "category_".$category->id."_".time();

                        // Resize Image to 32px if greater
                        Image::factory($filename)->resize(32,32,
                            Image::HEIGHT)->save(Kohana::config('upload.directory',
                            TRUE) . $new_filename.".png");

                        // Remove the temporary file
                        unlink($filename);
                    
                        // Delete Old Image
                        $category_old_image = $category->category_image;
                         
                        if ( ! empty($category_old_image)
                            AND file_exists(Kohana::config('upload.directory', TRUE). $category_old_image))
                        {
                            unlink(Kohana::config('upload.directory', TRUE).$category_old_image);
                        }
                    
                        // Save
                        $category->category_image = $new_filename.".png";
                        $category->save();

                    }
                }
            }
            // No! We have validation errors, we need to show the form
            // again, with the errors
            else
            {
                // Populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('category'));
                 
                foreach($errors as $error_item => $error_description)
                {
                    if( ! is_array($error_description))
                    {
                        $this->error_messages .= $error_description;
                        
                        if($error_description != end($errors))
                        {
                            $this->error_messages .= " - ";
                        }
                    }
                }
                 
                $ret_value = 1; // Validation error
            }
    
        }
        else
        {   
            $ret_value = 3;
        }

        // Set the response data
        $this->response_data = $this->response($ret_value, 
            $this->error_messages);
    }

    /**
     * Delete existing category
     *
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    private function _del_category()
    {
        // setup and initialize form field names
        $form = array
        (
            'category_id'   => '',
        );

        // copy the form as errors, so the errors will be stored 
        //with keys corresponding to the form field names
        $errors = $form;
        
        $ret_value = 0;
        
        // Check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't 
            //overwrite $_POST fields with our own things
            $post = Validation::factory(array_merge($_POST,$_FILES));
        
            //  Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list 
            //of checks, carried out in order
            $post->add_rules('category_id','required','numeric');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                $category_id = $post->category_id;
                $category = new Category_Model($category_id);
                $category->delete($category_id);

            } 
            else
            {
                // populate the error fields, if any
                $errors = arr::overwrite($errors, 
                $post->errors('category'));
                
                foreach($errors as $error_item => $error_description)
                {
                    if( ! is_array($error_description))
                    {
                        $this->error_messages .= $error_description;
                    
                        if ($error_description != end($errors))
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
            $ret_value = 3;
        }
    
        // Set the response data
        $this->response_data = $this->response($ret_value, $this->error_messages);

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
            
        if( isset($category_id) )
        {
            $category_id = $post->category_id;
        }

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
        
        if ( ! empty($category_id) && $category_id == $parent_id)
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
            'parent_id'      => '',
            'category_title'      => '',
            'category_description'    => '',
            'category_color'  => '',
            'category_image'  => ''
        );
        
        // copy the form as errors, so the errors will be stored 
        //with keys corresponding to the form field names
        $errors = $form;
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
                $category = new Category_Model();
                
                // Save Action              
                $category->parent_id = $post->parent_id;
                $category->category_title = $post->category_title;
                $category->category_description = 
                $post->category_description;
                $category->category_color = $post->category_color;
                $category->save();
                
                //optional
                if ( ! empty($post->category_image))
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
                        
                        if ( ! empty($category_old_image)
                            AND file_exists(Kohana::config(
                                    'upload.directory', TRUE).
                                    $category_old_image))
                                unlink(Kohana::config('upload.directory', TRUE).
                                    $category_old_image);
                        
                            // Save
                            $category->category_image = $new_filename.".png";
                            $category->save();
                    }
                }
                    
                // Empty $form array
                array_fill_keys($form, '');
                
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
                    if ( ! is_array($error_description))
                    {
                        $this->error_messages .= $error_description;
                        
                        if ($error_description != end($errors))
                        {
                            $this->error_messages .= " - ";
                        }
                    }
                }
                
                return 1; // Validation error
            }
        }
        else
        {
            return 3; // Not sent by post method.
        }

    }
}
