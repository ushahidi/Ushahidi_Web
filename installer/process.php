<?php 
/**
 * simplify the task of processing form fields, redirecting to the pages.
 *
 * The Form class is meant to simplify the task of keeping
 * track of errors in user submitted forms and the form
 * field values that were entered correctly.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General 
 * Public License (LGPL)
 */
 require_once("install.php");
 require_once("form.php");
  
 class Process 
 {
    public function __construct()
	{
		$this->_index();		
	}
	
	/**
	 * Call up all the processors.
	 */
	public function _index()
	{
	    if(isset($_POST['basic_db_info'])) {
	        $this->_proc_basic_db_info();
	    }else if($_POST['advanced_db_info']){
	    	$this->_proc_advanced_db_info();
	    }else if( $_POST['advanced_general_settings']){
	    	$this->_proc_general_settings();
	    }else if($_POST['advanced_mail_server_settings']){ 
	    	$this->_proc_mail_server();
	    }else if($_POST['advanced_map_config']){ 
	    	$this->_proc_map();
	    } else {
	        header("Location:.");
	    }        
	}
	
	/**
	 * Process basic database info.
	 */
	public function _proc_basic_db_info()
	{
	    global $form, $install;
	        
	    $status = $install->_install_basic_info( 
	        $_POST['username'],
	        $_POST['password'],
	        $_POST['host'],
	        'mysql',
	        $_POST['db_name'],
	        $_POST['table_prefix'],
	        $_POST['base_path']
	    );
	    
	    //no errors
	    if( $status == 0 ) {
	    	$_SESSION['basic_finished'] = 'basic_db_info'; 
	        header("Location:basic_finished.php");
	    }else if($status == 1 ) {
	        $_SESSION['value_array'] = $_POST;
	        $_SESSION['error_array'] = $form->get_error_array();
	        header("Location:basic_db_info.php");
	    }
	           
	}
	
	/**
	 * Process advanced database info. 
	 */
	public function _proc_advanced_db_info(){
		global $form, $install;
		$status = $install->_install_db_info( 
	        $_POST['username'],
	        $_POST['password'],
	        $_POST['host'],
	        'mysql',
	        $_POST['db_name'],
	        $_POST['table_prefix'],
	        $_POST['base_path']
	    );
	    
	    //no errors
	    if( $status == 0 ) {
	    	// make sure users get to the general setting from advanced db info page.
	    	$_SESSION['general_settings'] = 'general_settings';
	    	
	  		// send the database info to the next page for updating the settings table.
	    	$_SESSION['username'] = $_POST['username'];
	    	$_SESSION['password'] = $_POST['password'];
	    	$_SESSION['host'] = $_POST['host'];
	    	$_SESSION['db_name'] = $_POST['db_name'];
	    	$_SESSION['table_prefix'] = $_POST['table_prefix']; 
	        header("Location:advanced_general_settings.php");
	    }else if($status == 1 ) {
	        $_SESSION['value_array'] = $_POST;
	        $_SESSION['error_array'] = $form->get_error_array();
	        header("Location:advanced_db_info.php");
	    }
	}
	
	/**
	 * Process the general settings.
	 */
	public function _proc_general_settings() {
		global $form, $install;
		
		$status = $install->_install_db_info( 
	        $_POST['site_name'],
	        $_POST['site_tagline'],
	        $_POST['select_language'],
	        $_POST['site_email']
	    );
	    
	    //no errors
	    if( $status == 0 ) {
	    	// make sure users get to the general setting from advanced db info page.
	    	$_SESSION['mail_server'] = 'mail_server';
	    	
	  		// set it up in case someone want to goes the previous page.
	    	$_SESSION['site_name'] = $_POST['username'];
	    	$_SESSION['password'] = $_POST['password'];
	    	$_SESSION['select_language'] = $_POST['select_language'];
	    	$_SESSION['site_email'] = $_POST['site_email'];
	    	 
	        header("Location:advanced_mail_server_settings.php");
	    }else if($status == 1 ) {
	        $_SESSION['value_array'] = $_POST;
	        $_SESSION['error_array'] = $form->get_error_array();
	        header("Location:advanced_general_settings.php");
	    }	
	}
	
	/**
	 * Process the mail server.
	 */
	public function _proc_mail_server() {
		global $form, $install;
		$status = $install->_install_db_info( 
	        $_POST['username'],
	        $_POST['password'],
	        $_POST['host'],
	        'mysql',
	        $_POST['db_name'],
	        $_POST['table_prefix'],
	        $_POST['base_path']
	    );
	    
	    //no errors
	    if( $status == 0 ) {
	    	// make sure users get to the general setting from advanced db info page.
	    	$_SESSION['map_settings'] = 'map_settings';
	    	
	  		// send the database info to the next page for updating the settings table.
	    	$_SESSION['username'] = $_POST['username'];
	    	$_SESSION['password'] = $_POST['password'];
	    	$_SESSION['host'] = $_POST['host'];
	    	$_SESSION['db_name'] = $_POST['db_name'];
	    	 
	        header("Location:advanced_map_configuration.php");
	    }else if($status == 1 ) {
	        $_SESSION['value_array'] = $_POST;
	        $_SESSION['error_array'] = $form->get_error_array();
	        header("Location:advanced_mail_server_settings.php");
	    }
	}
	
	/**
	 * Process the map details.
	 */
	public function _proc_map() {
		global $form, $install;
		$status = $install->_install_db_info( 
	        $_POST['username'],
	        $_POST['password'],
	        $_POST['host'],
	        'mysql',
	        $_POST['db_name'],
	        $_POST['table_prefix'],
	        $_POST['base_path']
	    );
	    
	    //no errors
	    if( $status == 0 ) {
	    	// make sure users get to the general setting from advanced db info page.
	    	$_SESSION['advanced_mail_server_settings'] = 'advanced_mail_server_settings';
	    	
	  		// send the database info to the next page for updating the settings table.
	    	$_SESSION['username'] = $_POST['username'];
	    	$_SESSION['password'] = $_POST['password'];
	    	$_SESSION['host'] = $_POST['host'];
	    	$_SESSION['db_name'] = $_POST['db_name'];
	    	 
	        header("Location:advanced_general_settings.php");
	    }else if($status == 1 ) {
	        $_SESSION['value_array'] = $_POST;
	        $_SESSION['error_array'] = $form->get_error_array();
	        header("Location:advanced_mail_server_settings.php");
	    }
	}
	
 }
 $process = new Process();
?>
