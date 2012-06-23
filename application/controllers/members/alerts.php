<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alerts Controller.
 * This controller will take care of adding and editing reports in the Member section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Members
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Alerts_Controller extends Members_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'alerts';
	}
	
	/**
	 * Lists all the alerts
	 */
	public function index()
	{
		$this->template->content = new View('members/alerts');
		$this->template->content->title = Kohana::lang('ui_admin.my_alerts');
		
		// Is this an Inbox or Outbox Filter?
		if (!empty($_GET['type']))
		{
			$type = $_GET['type'];

			if ($type == '1')
			{ // SMS
				$filter = 'alert_type=1';
			}
			elseif ($type == '2')
			{ // EMAIL
				$filter = 'alert_type=2';
			}
			else
			{ // ALL
				$filter = '1=1';
			}
		}
		else
		{
			$type = "0";
			$filter = '1=1';
		}
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('alert_id.*','required','numeric');

			if ($post->validate())
			{
				if ($post->action == 'd')	//Delete Action
				{
					foreach($post->alert_id as $item)
					{
						$update = ORM::factory('alert')
							->where('user_id', $this->user->id)
							->find($item);
						if ($update->loaded)
						{
							$alert_id = $update->id;
							$update->delete();

							// Delete Media
							ORM::factory('alert_category')->where('alert_id',$alert_id)->delete_all();
						}
					}
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}
		}
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'	 => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'	 => ORM::factory('alert')
				->where("user_id", $this->user->id)
				->where($filter)
				->count_all()
			));

		$alerts = ORM::factory('alert')
			->where("user_id", $this->user->id)
			->where($filter)
			->orderby('id', 'asc')
			->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
			
		$this->template->content->alerts = $alerts;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->type = $type;

		// Total Messages
		$this->template->content->total_items = $pagination->total_items;
		
		// Javascript Header
		$this->template->js = new View('members/alerts_js');
	}	
}
