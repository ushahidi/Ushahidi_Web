<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages Controller.
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

class Messages_Controller extends Admin_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'messages';

		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("messages"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}

	/**
	 * Lists the messages.
	 * @param int $service_id
	 */
	public function index($service_id = 1)
	{
		// If a table prefix is specified
		$db_config = Kohana::config('database.default');
		$table_prefix = $db_config['table_prefix'];

		$this->template->content = new View('admin/messages/main');

		// Get Title
		$service = ORM::factory('service', $service_id);
		$this->template->content->title = $service->service_name;

		// Display Reply to Option?
		$this->template->content->reply_to = TRUE;
		if ( ! Kohana::config("settings.sms_provider"))
		{
			// Hide Reply to option
			$this->template->content->reply_to = FALSE;
		}

		// Is this an Inbox or Outbox Filter?
		if (!empty($_GET['type']))
		{
			$type = $_GET['type'];

			if ($type == '2')
			{ 
				// OUTBOX
				$filter = 'message.message_type = 2';
			}
			else
			{
				// INBOX
				$type = "1";
				$filter = 'message.message_type = 1';
			}
		}
		else
		{
			$type = "1";
			$filter = 'message.message_type = 1';
		}
        
		// Do we have a reporter ID?
		if (isset($_GET['rid']) AND !empty($_GET['rid']))
		{
			$filter .= ' AND message.reporter_id=\''.intval($_GET['rid']).'\'';
		}
        
		// ALL / Trusted / Spam
		$level = '0';
		if (isset($_GET['level']) AND !empty($_GET['level']))
		{
			$level = $_GET['level'];
			if ($level == 4)
			{
				$filter .= " AND ( ".$table_prefix."reporter.level_id = '4' OR "
				    . $table_prefix."reporter.level_id = '5' ) "
				    . "AND ( ".$table_prefix."message.message_level != '99' ) ";
			}
			elseif ($level == 2)
			{
				$filter .= " AND ( ".$table_prefix."message.message_level = '99' ) ";
			}
		}

		// Check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
        
		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('message_id.*','required','numeric');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{   
				if( $post->action == 'd' )              // Delete Action
				{
					foreach($post->message_id as $item)
					{
						// Delete Message
						$message = ORM::factory('message')->find($item);
						$message->message_type = 3; // Tag As Deleted/Trash
						$message->save();
					}

					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				elseif ($post->action == 'n')
				{
					// Not Spam
					foreach($post->message_id as $item)
					{
						// Update Message Level
						$message = ORM::factory('message')->find($item);
						if ($message->loaded)
						{
							$message->message_level = '1';
							$message->save();
						}
					}

					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.modified'));
				}
				elseif ($post->action == 's')
				{
					// Spam
					foreach ($post->message_id as $item)
					{
						// Update Message Level
						$message = ORM::factory('message')->find($item);
						if ($message->loaded)
						{
							$message->message_level = '99';
							$message->save();
						}
					}

					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.modified'));
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('message'));
				$form_error = TRUE;
			}
		}       
        
		// Pagination
		$pagination = new Pagination(array(
		'query_string'   => 'page',
		'items_per_page' => $this->items_per_page,
		'total_items'    => ORM::factory('message')
		    ->join('reporter','message.reporter_id','reporter.id')
		    ->where($filter)
		    ->where('service_id', $service_id)
		    ->count_all()
		));

		$messages = ORM::factory('message')
		    ->join('reporter','message.reporter_id','reporter.id')
		    ->where('service_id', $service_id)
		    ->where($filter)
		    ->orderby('message_date','desc')
		    ->find_all($this->items_per_page, $pagination->sql_offset);
            
		// Get Message Count
		// ALL
		$this->template->content->count_all = ORM::factory('message')
		    ->join('reporter','message.reporter_id','reporter.id')
		    ->where('service_id', $service_id)
		    ->where('message_type', 1)
		    ->count_all();
            
		// Trusted
		$this->template->content->count_trusted = ORM::factory('message')
		    ->join('reporter','message.reporter_id','reporter.id')
		    ->where('service_id', $service_id)
		    ->where('message_type', 1)
		    ->where("message.message_level != '99' AND ( ".$table_prefix."reporter.level_id = '4' OR ".$table_prefix."reporter.level_id = '5' )")
		    ->count_all();
        
		// Spam
		$this->template->content->count_spam = ORM::factory('message')
		    ->join('reporter','message.reporter_id','reporter.id')
		    ->where('service_id', $service_id)
		    ->where('message_type', 1)
		    ->where("message.message_level = '99'")
		    ->count_all();

		//Reporters
		$this->template->content->count_reporters = ORM::factory('reporter')
		    ->where('service_id', $service_id)
		    ->count_all();

		$this->template->content->messages = $messages;
		$this->template->content->service_id = $service_id;
		$this->template->content->services = ORM::factory('service')->find_all();
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        
		$levels = ORM::factory('level')->orderby('level_weight')->find_all();
		$this->template->content->levels = $levels;

		// Total Reports
		$this->template->content->total_items = $pagination->total_items;

		// Message Type Tab - Inbox/Outbox
		$this->template->content->type = $type;
		$this->template->content->level = $level;

		// Javascript Header
		$this->template->js = new View('admin/messages/messages_js');
	}

	/**
	 * Send A New Message Using Default SMS Provider
	 */
	public function send()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		// Setup and initialize form field names
		$form = array(
			'to_id' => '',
			'message' => ''
		);

		//  Copy the form as errors, so the errors will be stored with keys
		//  corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('to_id', 'required', 'numeric');
			$post->add_rules('message', 'required', 'length[1,160]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid
				$reply_to = ORM::factory('message', $post->to_id);

				if ($reply_to->loaded == true)
				{
					// Yes! Replyto Exists
					// This is the message we're replying to

					$sms_to = $reply_to->message_from;
					//checks if the number is encrypted
					if (preg_match("/([a-zA-Z])(\D)/", $sms_to))
					{
						$this->decrypter = new Encrypt;
						$sms_to = $this->decrypter->decode($sms_to);
					}
					else
					{
						$sms_to = $sms_to;
					}

					// Load Users Settings
					$settings = Settings_Model::get_array();
					if ( !empty($settings))
					{
						// Get SMS Numbers
						if ( ! empty($settings['sms_no1']))
						{
							$sms_from = $settings['sms_no1'];
						}
						elseif ( ! empty($settings['sms_no2']))
						{
							$sms_from = $settings['sms_no2'];
						}
						elseif ( ! empty($settings['sms_no3']))
						{
							$sms_from = $settings['sms_no3'];
						}
						else
						{
							// User needs to set up an SMS number
							$sms_from = "000";
						}

						// Send Message
						$response = sms::send($sms_to, $sms_from, $post->message);

						// Message Went Through??
						if ($response === TRUE)
						{
							$message = ORM::factory('message');
							$message->parent_id = $post->to_id;  // The parent message
							$message->message_from = $sms_from;
							$message->message_to = $sms_to;
							$message->message = $post->message;
							$message->message_type = 2;          // This is an outgoing message
							$message->reporter_id = $reply_to->reporter_id;
							$message->message_date = date("Y-m-d H:i:s",time());
							$message->save();

							echo json_encode(array(
								"status" => "sent", 
								"message" => Kohana::lang('ui_admin.message_sent')
							));
						}                        
						else
						{
							// Message Failed 
							echo json_encode(array(
								"status" => "error", 
								"message" => Kohana::lang('ui_admin.error_msg')." - " . $response
							));
						}
					}
					else
					{
						echo json_encode(array(
							"status" => "error",
							"message" => Kohana::lang('ui_admin.error_msg').Kohana::lang('ui_admin.check_sms_settings')
						));
					}
				}
				else
				{
					// Send_To Mobile Number Doesn't Exist
					echo json_encode(array(
						"status" => "error", 
						"message" => Kohana::lang('ui_admin.error_msg').Kohana::lang('ui_admin.check_number')
					));
				}
			}
			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('messages'));

				echo json_encode(array(
					"status" => "error",
					"message" => Kohana::lang('ui_admin.error_msg').Kohana::lang('ui_admin.check_message_valid')
				));
			}
		}
	}

	/**
	 * Setup simplepie
	 * @param string $raw_data
	 */
	private function _setup_simplepie($raw_data)
	{
		$data = new SimplePie();
		$data->set_raw_data( $raw_data );
		$data->enable_cache(false);
		$data->enable_order_by_date(true);
		$data->init();
		$data->handle_content_type();
		return $data;
	}

}
