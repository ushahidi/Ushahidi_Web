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
	
	public function _index()
	{
	    if(isset($_POST['basic_db_info'])) {
	        $this->_process_install();
	    
	    } else {
	        header("Location:.");
	    }        
	}
	
	public function _process_install()
	{
	    global $form, $install;
	        
	    $status = $install->_install( 
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
 }
 $process = new Process();
?>
