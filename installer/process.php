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
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Admin Dashboard Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General 
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
		}else if(isset($_POST['advanced_db_info'])){
			$this->_proc_advanced_db_info();
		}else if(isset($_POST['advanced_general_settings'])){
			$this->_proc_general_settings();
		}else if(isset($_POST['basic_general_settings'])){ 
			$this->_proc_basic_general_settings();
		}else if(isset($_POST['advanced_mail_server_settings'])){ 
			$this->_proc_mail_server();
		}else if(isset($_POST['advanced_map_config'])){ 
			$this->_proc_map();
		}else if(isset($_POST['advanced_perm_pre_check'])){ 
			$this->_proc_advanced_pre_perm_check();
		}else if(isset($_POST['basic_perm_pre_check'])){ 
			$this->_proc_basic_pre_perm_check();		
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
			$_SESSION['basic_db_info'] = 'basic_general_settings'; 
			// send the database info to the next page for updating the settings table.
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['password'] = $_POST['password'];
			$_SESSION['host'] = $_POST['host'];
			$_SESSION['db_name'] = $_POST['db_name'];
			$_SESSION['table_prefix'] = $_POST['table_prefix'];
			
			header("Location:basic_general_settings.php");
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
		
		$status = $install->_general_settings( 
			$_POST['site_name'],
			$_POST['site_tagline'],
			$_POST['select_language'],
			$_POST['site_email'],
			$_POST['table_prefix'],
			$_POST['enable_clean_url']
		);
		
		//no errors
		if( $status == 0 ) {
			// make sure users get to the general setting from advanced db info page.
			$_SESSION['mail_server'] = 'mail_server';
			
			// set it up in case someone wants to go to the previous page.
			$_SESSION['site_name'] = $_POST['site_name'];
			$_SESSION['site_tagline'] = $_POST['site_tagline'];
			$_SESSION['select_language'] = $_POST['select_language'];
			$_SESSION['site_email'] = $_POST['site_email'];
			$_SESSION['table_prefix'] = $_POST['table_prefix'];
			 
			header("Location:advanced_mail_server_settings.php");
		}else if($status == 1 ) {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->get_error_array();
			
			header("Location:advanced_general_settings.php");
		}	
	}
	
	 /* 
	  * Process the general settings.
	  */
	public function _proc_basic_general_settings() {
		global $form, $install;
		
		$status = $install->_general_settings( 
			$_POST['site_name'],
			$_POST['site_tagline'],
			$_POST['select_language'],
			$_POST['site_email'],
			$_POST['table_prefix'],
			$_POST['enable_clean_url']
		);
		
		//no errors
		if( $status == 0 ) {
			// make sure users get to the general setting from advanced db info page.
			$_SESSION['basic_general_settings'] = 'basic_finished';
			
			// set it up in case someone want to goes the previous page.
			$_SESSION['site_name'] = $_POST['site_name'];
			$_SESSION['site_tagline'] = $_POST['site_tagline'];
			$_SESSION['select_language'] = $_POST['select_language'];
			$_SESSION['site_email'] = $_POST['site_email'];
			$_SESSION['table_prefix'] = $_POST['table_prefix'];
			
			header("Location:basic_finished.php");
		}else if($status == 1 ) {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->get_error_array();
			
			header("Location:basic_general_settings.php");
		}	
	}
	
	/**
	 * Process the mail server.
	 */
	public function _proc_mail_server() {
		global $form, $install;
		
		$status = $install->_mail_server( 
			$_POST['site_alert_email'],
			$_POST['mail_server_username'],
			$_POST['mail_server_pwd'],
			$_POST['mail_server_port'],
			$_POST['mail_server_host'],
			$_POST['select_mail_server_type'],
			$_POST['select_mail_server_ssl'],
			$_POST['table_prefix']
		);
		
		//no errors
		if( $status == 0 ) {
			// make sure users get to the general setting from advanced db info page.
			$_SESSION['map_settings'] = 'map_settings';
			
			// send the database info to the next page for updating the settings table.
			$_SESSION['site_alert_email'] = $_POST['site_alert_email'];
			$_SESSION['mail_server_username'] = $_POST['mail_server_username'];
			$_SESSION['mail_server_pwd'] = $_POST['mail_server_pwd'];
			$_SESSION['mail_server_port'] = $_POST['mail_server_port'];
			$_SESSION['mail_server_host'] = $_POST['mail_server_host'];
			$_SESSION['select_mail_server_type'] = $_POST['select_mail_server_type'];
			$_SESSION['select_mail_server_ssl'] = $_POST['select_mail_server_ssl'];
			$_SESSION['table_prefix'] = $_POST['table_prefix'];
			 
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
		
		$status = $install->_map_info( 
			$_POST['select_map_provider'],
			$_POST['map_provider_api_key'],
			$_POST['table_prefix']
		);
		
		//no errors
		if( $status == 0 ) {
			// make sure users get to the general setting from advanced db info page.
			$_SESSION['advanced_finished'] = 'advanced_map';
			
			// send the database info to the next page for updating the settings table.
			$_SESSION['select_map_provider'] = $_POST['select_map_provider'];
			$_SESSION['map_provider_api_key'] = $_POST['map_provider_api_key'];
			$_SESSION['table_prefix'] = $_POST['table_prefix'];
			 
			header("Location:advanced_finished.php");
		}else if($status == 1 ) {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->get_error_array();
			header("Location:advanced_map_configuration.php");
		}
	}
	
	/**
	 * Process the pre permission check for basic installer mode.
	 */
	public function _proc_basic_pre_perm_check() {
		global $install,$form;
		$status = $install->_check_writable_dir();
		$status += $install->_check_modules();
		if($status == 0 ) {
			$_SESSION['basic_db_info'] = 'basic_summary';
			header("Location:basic_db_info.php");
		}else {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->get_error_array();
			header("Location:basic_summary.php");
		}
	}
	
	/**
	 * Process the pre permission check for advanced installer mode.
	 * +Check for required PHP libraries
	 */
	public function _proc_advanced_pre_perm_check() {
		global $install, $form, $modules;
		$status = $install->_check_writable_dir();
		$status += $install->_check_modules_advanced();
		if($status == 0 ) {
			// make sure users get to the general setting from advanced db info page.
			$_SESSION['advanced_db_info'] = 'advanced_summary';
			
			header("Location:advanced_db_info.php");
		}else{
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->get_error_array();
			header("Location:advanced_summary.php");
		}
	}
	
 }
 $process = new Process();
?>
