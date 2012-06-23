<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Checkins Controller.
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

class Checkins_Controller extends Members_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'checkins';
	}
	
	/**
	 * Lists the checkins.
	 * @param int $page
	 */
	public function index($page = 1)
	{
		$this->template->content = new View('members/checkins');
		$this->template->content->title = Kohana::lang('ui_admin.my_checkins');
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('checkin_id.*','required','numeric');

			if ($post->validate())
			{
				if ($post->action == 'd')	//Delete Action
				{
					foreach($post->checkin_id as $item)
					{
						$update = ORM::factory('checkin')
							->where('user_id', $this->user->id)
							->find($item);
						if ($update->loaded)
						{
							$checkin_id = $update->id;
							$update->delete();

							// Delete Media
							ORM::factory('media')->where('checkin_id',$checkin_id)->delete_all();
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
			'total_items'	 => ORM::factory('checkin')
				->where('user_id', $this->user->id)
				->count_all()
			));

		$checkins = ORM::factory('checkin')
			->where('user_id', $this->user->id)
			->orderby('checkin_date', 'desc')
			->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
			
		$this->template->content->checkins = $checkins;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
			
		// Total Reports
		$this->template->content->total_items = $pagination->total_items;
		
		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->js = new View('members/checkins_js');
	}	
}
