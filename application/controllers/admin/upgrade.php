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
        
}
