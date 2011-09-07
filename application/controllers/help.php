<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to list/view Organizations
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Help_Controller extends Main_Controller 
{
    function __construct()
    {
        parent::__construct();

        // Javascript Header
        $this->themes->validator_enabled = TRUE;
    }

    /**
     * Displays all Organizations.
     */
    public function index() 
    {
        $this->template->header->this_page = Kohana::lang('ui_admin.help');
        $this->template->content = new View('help');
        $items_per_page = (int) Kohana::config('settings.items_per_page');
        
        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => $items_per_page,
            'total_items' => ORM::factory('organization')->where('organization_active', '1')->count_all()
        ));

        $organizations = ORM::factory('organization')
                            ->where('organization_active', '1')
                            ->orderby('organization_name', 'asc')
                            ->find_all($items_per_page, $pagination->sql_offset);
        
        $this->template->content->organizations = $organizations;
        $this->template->content->pagination = $pagination;
        
        //Only display stats when there are reports to display
        if ($pagination->total_items > 0)
        {
            $this->template->content->pagination_stats = "(Showing " 
                .(($pagination->sql_offset/$items_per_page) + 1)
                ." of ".ceil($pagination->total_items/$items_per_page)
                ." pages)"; 
        }
        else
        {
            $this->template->content->pagination_stats = "";
        }
        
        // Rebuild Header Block
        $this->template->header->header_block = $this->themes->header_block();
    }
    
     /**
     * Displays a organization
     * @param boolean $id If id is supplied, an organization with that id will be
     * retrieved.
     */
    public function view($id = FALSE)
    {
        $this->template->header->this_page = Kohana::lang('ui_admin.help');
        $this->template->content = new View('help_view');
        
        if (!$id)
        {
            url::redirect('main');
        }
        else
        {
            $organization = ORM::factory('organization', $id);
            
            if ($organization->loaded == FALSE) // Not Found
            {
                url::redirect('main');
            }
            
            // Comment Post?
            // setup and initialize form field names
            $form = array
            (
                'name' => '',
                'email' => '',
                'phone' => '',
                'message' => '',
                'captcha' => ''
            );
            $captcha = Captcha::factory(); 
            $errors = $form;
            $form_error = FALSE;
            
            // Check, has the form been submitted, if so, setup validation
            if ($_POST)
            {
                // Instantiate Validation, use $post, so we don't overwrite 
                // $_POST fields with our own things
                $post = Validation::factory($_POST);

                //  Add some filters
                $post->pre_filter('trim', TRUE);
        
                // Add some rules, the input field, followed by a list of checks,
                // carried out in order
                $post->add_rules('name', 'required', 'length[3, 100]');
                $post->add_rules('email', 'required', 'email', 'length[4, 100]');
                $post->add_rules('phone', 'length[3, 100]');
                $post->add_rules('message', 'required');
                $post->add_rules('captcha', 'required', 'Captcha::valid');
                
                // Test to see if things passed the rule checks
                if ($post->validate())
                {
                    // Yes! everything is valid - Send Message
                    if (!empty($organization->organization_email)) {
                        $to = $organization->organization_email;
                        $from = $post->email;
                        $subject = Kohana::lang('ui_admin.sender').": ".Kohana::config('settings.site_name');
                        $message = "";
                        $message.= Kohana::lang('ui_admin.name').": ".$post->name."\n";
                        $message.= Kohana::lang('ui_admin.email').": ".$post->email."\n";
                        $message.= Kohana::lang('ui_admin.phone').": ".$post->phone."\n\n";
                        $message.= Kohana::lang('ui_admin.message').":\n".$post->message."\n";

                        email::send($to, $from, $subject, $message, FALSE);
                    }
                    
                    // Redirect
                    url::redirect('help/view/' . $id);
                }

                // No! We have validation errors, we need to show the form again,
                // with the errors
                else   
                {
                    // repopulate the form fields
                    $form = arr::overwrite($form, $post->as_array());

                    // populate the error fields, if any
                    $errors = arr::overwrite($errors, $post->errors('message'));
                    $form_error = TRUE;
                }
            }
            
            $this->template->content->organization_id = $organization->id;
            $this->template->content->organization_name = $organization->organization_name;
            $this->template->content->organization_description = nl2br($organization->organization_description);
            $this->template->content->organization_website = text::auto_link($organization->organization_website);
            $this->template->content->organization_email = $organization->organization_email;
            $this->template->content->organization_phone1 = $organization->organization_phone1;
            $this->template->content->organization_phone2 = $organization->organization_phone2;

            // Forms
            $this->template->content->form = $form;
            $this->template->content->captcha = $captcha;
            $this->template->content->errors = $errors;
            $this->template->content->form_error = $form_error;
            
            // Javascript Header
            $this->themes->js = new View('help_view_js');
        }
        
        // Rebuild Header Block
        $this->template->header->header_block = $this->themes->header_block();
    }
}
