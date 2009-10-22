<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages Controller.
 * View SMS Messages Received Via FrontlineSMS
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Messages Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Upgrade_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();

                $this->template->this_page = 'upgrade';

                // limit access to only superadmin
                if(!$this->auth->logged_in('superadmin'))
                {
                    url::redirect('admin/dashboard');
                }
	}

	/**
	 * Upgrade page.
     *
     */
	function index()
	{
		$this->template->content = new View('admin/upgrade');

	    // check, has the form been submitted?
	    $form_error = FALSE;
	    $form_saved = FALSE;
	    $form_action = "";
		
      	$this->template->content->title = "Upgrade Ushahidi";
      	
      	$upgrade = $this->_do_upgrade();
      	
      	if( count( $upgrade->errors ) == 0  ) {
      		$this->template->content = new View('admin/upgrade_status');
      		$this->template-content->logs = $upgrade->log;
      	}else{
      		$this->template->content = new View('admin/upgrade_status');
      		$this->template-content->errors = errors;
      	}
      	
	    $this->template->content->form_error = $form_error;
	    $this->template->content->form_saved = $form_saved;
	    $this->template->content->form_action = $form_action;
		
	}

  	/**
     * The status of the upgrade
     */
	function status() {
    	$this->template->content = new View('admin/upgrade/status');
	}

	private function _upgrade_tables() {
    	$db = Database;
    	$db_schema = file_get_contents('../sql/update.sql');

    	// get individual sql statement 
    	$sql_statements = explode( ';',$db_schema );
            
      	foreach( $sql_statements as $sql_statement ) {
        	$db->query($sql_statement);
    	}

	}
        
        /**
         * 
         * Downloads the latest ushahidi file.
         * Extracts the compressed folder.
         * Delete the folders that needs to be preserved.
         * Delete the downloaded ushahidi file.
         * Delete the extracted ushahidi file.
         * 
         */
	private function _do_upgrade() {
    	$upgrade = new Upgrade;
       	$url = "http://localhost/ushahidi.zip";
        $working_dir = "media/uploads/";
        $zip_file = "media/uploads/ushahidi.zip";
      	
      	//download the latest ushahidi
       	$latest_ushahidi = $upgrade->download_ushahidi($url);
                	
       	//download went successful
       	if($upgrade->success ) {
        	$upgrade->write_to_file($latest_ushahidi, $zip_file);
          	$upgrade->success = false;
       	}
        	
       	//extract compressed file
       	if( $upgrade->success ) {
        	$upgrade->unzip_ushahidi($zip_file, $working_dir);
        	$upgrade->success = false;
       	}

      	if( $upgrade->success ) {
       		$upgrade->remove_recursively($working_dir."ushahidi/application/config");
        	$upgrade->success = false;
      	}

       	if( $upgrade->success ) {
        	$upgrade->remove_recursively($working_dir."ushahidi/application/cache");
        	$upgrade->success = false;
       	}

       	if( $upgrade->success ) {
        	$upgrade->remove_recursively($working_dir."ushahidi/application/logs");
        	$upgrade->success = false;
       	}

      	if( $upgrade->success ) {
       		$upgrade->remove_recursively($working_dir."ushahidi/media/uploads");
     		$upgrade->success = false;
       	}
                
       	if( $upgrade->success ) {
        	$upgrade->copy_recursively($working_dir."ushahidi","../");
         	$upgrade->success = false;
       	}
        	
        return $upgrade;

        	
	}
}
