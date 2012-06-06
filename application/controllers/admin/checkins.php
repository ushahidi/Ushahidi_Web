<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Checkins Controller.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Checkins_Controller extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->template->this_page = 'checkins';
        
        // If user doesn't have access, redirect to dashboard
        if ( ! $this->auth->has_permission("checkin_admin"))
        {
            url::redirect(url::site().'admin/dashboard');
        }
    }

    /**
    * Lists the checkins
    */
    function index()
    {
		$this->template->content = new View('admin/checkins/main');
    	$this->template->content->title = Kohana::lang('ui_admin.checkins');
    	
    	// check, has the form been submitted?
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = '';
        $filter = '1=1';
        
        
        // Form submission wizardry
        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
            $post = Validation::factory($_POST);

                //  Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
            $post->add_rules('message_id.*','required','numeric');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                if( $post->action == 'd' )              // Delete Action
                {
                    foreach($post->checkin_id as $checkin_id)
                    {
                        // Delete Checkin
                        ORM::factory('checkin')->delete($checkin_id);
                    }
                    
                    $form_saved = TRUE;
                    $form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
                }
            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('checkin'));
                $form_error = TRUE;
            }
        }
        
        
        
        
        // Pagination
        $pagination = new Pagination(array(
            'query_string'   => 'page',
            'items_per_page' => $this->items_per_page,
            'total_items'    => ORM::factory('checkin')
            								->join('users','checkin.user_id','users.id','INNER')
                                            ->where($filter)
                                            ->count_all()
        ));
        
        $checkins = ORM::factory('checkin')
        						->join('users','checkin.user_id','users.id','INNER')
                                ->where($filter)
                                ->orderby('checkin_date','desc')
                                ->find_all($this->items_per_page, $pagination->sql_offset);
        
        $this->template->content->checkins = $checkins;
        $this->template->content->pagination = $pagination;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->total_items = $pagination->total_items;
        
        // Javascript Header
        $this->template->js = new View('admin/checkins/checkins_js');
	}

}
