<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * This controller is used to manage API logging
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Api_Controller extends Admin_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->template->this_page = 'settings';
        if ( ! $this->auth->has_permission("manage"))
        {
            url::redirect(url::site().'admin/dashboard');
        }
    }
    
    /**
     * API Logging settings
     */
    public function index()
    {
        $this->template->content = new View('admin/settings/api/main');

        // Set up and initialize form field names
        $form = array
        (
            'api_default_record_limit' => '',
            'api_max_record_limit' => '',
            'api_max_requests_per_ip_address' => '',
            'api_max_requests_quota_basis' => ''
        );

        // Copy the form as errors, so the errors will be stored with keys
        // corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;

        // Check if the form has been submitted, if so setup validation
        if ($_POST)
        {
            $post = new Validation($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add validation rules
            // All values must be positive values; no (-ve) values are allowed
            $post->add_rules('api_default_record_limit', 'required', 'numeric', 'length[1,20]')
                 ->add_rules('api_max_record_limit', 'numeric', 'length[0,20]')
                 ->add_rules('api_max_requests_per_ip_address', 'depends_on[api_max_requests_quota_basis]', 'numeric', 'length[0,20]')
                 ->add_rules('api_max_requests_quota_basis', 'depends_on[api_max_requests_per_ip_address]', 'numeric', 'between[0,1]');

            // Test to see if rule checks have beens satisfied
            if ($post->validate() AND $post->action == 's')
            {
                // Check if the maximum record limit is less than the default
                if (isset($post->api_max_record_limit) AND strlen($post->api_max_record_limit > 0))
                {
                    if ((int) $post->api_default_record_limit > (int) $post->api_max_record_limit)
                    {
                        $errors[] = Kohana::lang('ui_admin.api_invalid_max_record_limit');
                        $form_error = TRUE;
                    }
                }
                
                // Proceed with saving if there's no form error
                if ( ! $form_error)
                {
                    // Everything is valid
                    $api_settings = new Api_Settings_Model(1);
                    
                    $api_settings->default_record_limit = ((int) $post->api_default_record_limit > 0)
                        ? $post->api_default_record_limit
                        : (int) Kohana::config('settings.items_per_api_request');
                        
                    $api_settings->max_record_limit = $post->api_max_record_limit;
                    $api_settings->max_requests_per_ip_address = $post->api_max_requests_per_ip_address;
                    
                    // Only set the quota basis if the max. no of API requests per IP has been specified
                    $api_settings->max_requests_quota_basis = ((int) $post->api_max_requests_per_ip_address > 0)
                        ? $post->api_max_requests_quota_basis
                        : NULL;
                    
                    $api_settings->modification_date = date("Y-m-d H:i:s", time());
                    $api_settings->save();
                
                    $form_saved = TRUE;
                
                    // Repopulate the form fields
                    $form = arr::overwrite($form, $post->as_array());
                }
            }
            // There are validation errors
            else
            {
                // Re-populate the form fields
                $form = arr::overwrite($form, $post->as_array());
                
                // Populate the error fields if any
                $errors = arr::overwrite($errors, $post->errors('api_settings'));
                $form_error = TRUE;
            }
        }
        else
        {
            // Retrieve current settings
            $api_settings = ORM::factory('api_settings', 1);
            
            $form = array
            (
                'api_default_record_limit' => ((int) $api_settings->default_record_limit > 0)
                    ? $api_settings->default_record_limit 
                    : Kohana::config('settings.items_per_api_request'),
                'api_max_record_limit' => $api_settings->max_record_limit,
                'api_max_requests_per_ip_address' => $api_settings->max_requests_per_ip_address,
                'api_max_requests_quota_basis' => $api_settings->max_requests_quota_basis
            );
        }

        // Set the form data
        $this->template->content->form = $form;
        
        // Set the form errors
        $this->template->content->errors = $errors;
        
        // Set the status of the form
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        
        // API request quota options (per day, month)
        $this->template->content->max_requests_quota_array = array(
            '' => '-- Select --',
            '0' => Kohana::lang('ui_main.day'), 
            '1' => Kohana::lang('ui_main.month')
        );
        
        // Javascript header
        $this->template->js = new View('admin/settings/api/api_js');
    }
    
    /**
     * Displays the API logs
     */
    public function log()
    {
        $this->template->content = new View('admin/settings/api/logs');
        $this->template->content->this_page='apilogs';
        $this->template->content->title = Kohana::lang('ui_main.api_logs');
        
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        
        // Check if the form has been submitted
        if ($_POST)
        {
            $post = Validation::factory($_POST);
            
            // Add some filters
            $post->pre_filter('trim', TRUE);
            
            // Add some rules
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');
            $post->add_rules('api_log_id.*', 'required', 'numeric');
            
            // Validate the submitted data against the validation rules
            if ($post->validate())
            {
                if ($post->action == 'd') // Delete action
                {
                    foreach ($post->api_log_id as $item)
                    {
                        $update = new Api_Log_Model($item);
                        if ($update->loaded == true)
                        {
                            $update->delete();
                        }
                    }
                    $form_action = "DELETED";
                }
                elseif ($post->action == 'x') // Delete all logs action
                {
                    ORM::factory('api_log')->delete_all();
                    $form_action = "DELETED";
                }
                elseif ($post->action == 'b')
                {
                    foreach ($post->api_log_id as $item)
                    {
                        $log_item = new Api_Log_Model($item);
                        if ($log_item->loaded == true)
                        {
                            // Get the IP Address associated with the specified api_log id
                            $log_ip_address = $log_item->api_ipaddress;
                            
                            // Check if the IP address has already been banned
                            $banned_count = ORM::factory('api_banned')
                                    ->where('banned_ipaddress', $log_ip_address)
                                    ->count_all();
                            
                            if ($banned_count == 0)
                            {
                                // Add the IP to the list of banned addresses                            
                                $api_banned = new Api_Banned_Model();
                                $api_banned->banned_ipaddress = $log_ip_address;
                                $api_banned->banned_date = date('Y-m-d H:i:s', time());
                                $api_banned->save();
                            }
                        }
                    }
                    $form_action = "BANNED";
                }
                $form_saved = TRUE;
            }
            else
            {
                $form_error = TRUE;
            }
        }
        // END form submission check

        // Set up pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'),
            'total_items' => ORM::factory('api_log')->count_all()
        ));
        
        // Fetch the api logs and page them
        $api_logs = $this->db->query('
                SELECT al.id, al.api_task, ab.id AS ban_id, al.api_parameters, al.api_records, al.api_ipaddress, al.api_date 
                FROM '.$this->table_prefix.'api_log al
                LEFT JOIN '.$this->table_prefix.'api_banned AS ab ON (ab.banned_ipaddress = al.api_ipaddress)
                ORDER BY al.api_date DESC
                LIMIT ?, ?', $pagination->sql_offset, $this->items_per_page
            );
        
        /*    
        $api_logs = ORM::factory('api_log')
                        ->orderby('api_date', 'asc')
                        ->find_all($this->items_per_page, $pagination->sql_offset);
        */
        
        // Set the total no. of items
        $this->template->content->total_items = ORM::factory('api_log')->count_all();
        
        // Set the form action
        $this->template->content->form_action = $form_action;
        
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->api_logs = $api_logs;
        $this->template->content->pagination = $pagination;
        
        // Javascript header
        $this->template->js = new View('admin/settings/api/logs_js');
    }
    
    /**
     * Displays the list of IP addresses that have been banned from access the API
     */
    public function banned()
    {
        $this->template->content = new View('admin/settings/api/banned');
        $this->template->content->this_page = 'apibanned';
        $this->template->content->title = Kohana::lang('ui_main.api_banned');
        
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        
        // Check if the form has been submitted
        if ($_POST)
        {
            $post = Validation::factory($_POST);
            
            // Add some filters
            $post->pre_filter('trim', TRUE);
            
            // Add some validation rules
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');
            $post->add_rules('api_banned_id.*', 'required', 'numeric');
            
            // Validate the submitted data against the validatieon rules
            if ($post->validate())
            {
                if ($post->action == 'd') // Uban action
                {
                    foreach ($post->api_banned_id as $item)
                    {
                        $update = new Api_Banned_Model($item);
                        if ($update->loaded == true)
                        {
                            $update->delete();
                        }
                    }
                    $form_action = "UNBANNED";
                }
                elseif ($post->action == 'x') // Unban all IP addresses
                {
                    ORM::factory('api_banned')->delete_all();
                    $form_action = "UNBANNED";
                }
                $form_saved = TRUE;
            }
            else // Validation failed
            {
                $form_error = TRUE;
            }
        }
        // END form submission check

        // Set up pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => $this->items_per_page,
            'total_items' => ORM::factory('api_banned')->count_all()
        ));
        
        // Fetch all the IP addresses banned from accessing the API
        $api_bans = ORM::factory('api_banned')
                        ->orderby('banned_date', 'desc')
                        ->find_all($this->items_per_page, $pagination->sql_offset);
        
        
        // Set the total no. of items
        $this->template->content->total_items = ORM::factory('api_banned')->count_all();
        
        // Set the form action
        $this->template->content->form_action = $form_action;
        
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->api_bans = $api_bans;
        $this->template->content->pagination = $pagination;
        
        // Javascript header
        $this->template->js = new View('admin/settings/api/banned_js');
    }
}
