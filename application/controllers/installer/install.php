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
    
    private $database_file;
	
	private $form;
    
    private $form_error;
    
    private $errors;
    
    private $general_errors;
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
        
        $this->form = array(
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
	
		$this->errors = $this->form;
		
		$this->form_error = FALSE;
		
		if( $_POST->validate() ) 
		{
		    // load database.template.php and work from it.
		    if(!file_exists('application/config/database.template.php')){
		        
		        // Not sure if there is a better way of doing this.
		        $this->general_errors = Kohana::lang('install.load_db_tpl');
		        $this->repopulate_form();
		    
		    } else {
		        $this->database_file = file('application/config/database.template.php');
		        $this->add_db_details();
		    }
            
        } else {
            $this->repopulate_form();        
        }
            
		$this->template->db_types = $db_types;
		$this->template->errors = $this->errors;
		$this->template->general_errors = $this->general_errors;
        $this->template->form = $this->form;
        $this->template->form_error = $this->form_error;
		//$this->template->this_page = 'installer/install';

	}
	
	/**
	 * adds the database details to the config/database.php 
	 */
	private function add_db_details( ) {
	    $database_file = $this->database_file;
	    $handle = fopen('application/config/database.php', 'w');
	    foreach( $database_file as $line_number => $line ) 
	    {
	        switch( trim(substr( $line,0,14 )) ) {
	            case "'type'     =": 
	                fwrite($handle, str_replace("'mysql'","'mysql'",$line ));
	                break;
	             
	            case "'user'     =":
	                fwrite($handle, str_replace("'username'","'root'",$line ));
	                break;
	            case "'pass'     =":
	                fwrite($handle, str_replace("'password'","'t8kax'",$line));
	                break;
	                
	            case "'host'     =":
	                fwrite($handle, str_replace("'localhost'","'localhost'",$line));
	                break;
	                 
	            case "'database' =":
	                fwrite($handle, str_replace("'db'","'usahidi'",$line));    
	                break;
	                
	            case "'table_prefix":
	                fwrite($handle, str_replace("''","'ush_'",$line));
	                break;
	                    
	            default:
	                fwrite($handle, $line);
	        }  
	    }
	    
	    fclose($handle);
	    //for security reasons change permission on the file to 666
	    //TODO look into the permission issues here - later in the day - after a good rest.
	    //chmod('application/config/database.php',0666);
	}
	
	/**
	 * imports sql file to the database.
	 */
	private function import_sql() {
	    
	}
	
	/**
	 * re-populate form fields
	 */
	private function repopulate_form() {
	    // repopulate the form fields
        $this->form = arr::overwrite($this->form, $_POST->as_array());
			
        // populate the error fields, if any
        // We need to already have created an error message file, for Kohana to use
        // Pass the error message file name to the errors() method			
        $this->errors = arr::overwrite($this->errors, $_POST->errors('install'));
        $this->form_error = TRUE;
	}

}
?>
