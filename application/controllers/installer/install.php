<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used for the main Admin panel
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Install_Controller extends Template_Controller
{
    public $auto_render = TRUE;
	
    // Main template
    public $template = 'installer/install';
	
    
	function __construct()
	{
		parent::__construct();		
	}
	
	function index()
	{
	    //database type Ushahidi supports
        $db_types = array( 
            "Mysql" => "mysql", 
            "Postgres" => "postgres"
        );
        
        $form = array(
            'username' => '',
            'password' => '',
            'host' => '',
            'select_db_type' => array(),
            'db_name' => '',
            'table_prefix' => '',
        );
        
        // Set up the validation object
        $_POST = Validation::factory($_POST)
            ->pre_filter('trim')
            ->add_rules('username', 'required')
            ->add_rules('password', 'required')
            ->add_rules('host',     'required')
            ->add_rules('db_name',  'required');
	   
		$errors = $form;
		$form_error = FALSE;
		if( $_POST->validate() ) 
		{
		     
		    // repopulate the form fields
            
        } else {
            // repopulate the form fields
            $form = arr::overwrite($form, $_POST->as_array());
			
            // populate the error fields, if any
            // We need to already have created an error message file, for Kohana to use
            // Pass the error message file name to the errors() method			
            $errors = arr::overwrite($errors, $_POST->errors('install'));
            $form_error = TRUE;        
        }
            
		$this->template->db_types = $db_types;
		$this->template->errors = $errors;
        $this->template->form = $form;
        $this->template->form_error = $form_error;
		//$this->template->this_page = 'installer/install';

	}
	
	

}
?>
