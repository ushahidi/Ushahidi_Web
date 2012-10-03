<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Blocks Controller
 * Add/Edit Ushahidi Instance Shares
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

class Blocks_Controller extends Admin_Controller
{
	private $_registered_blocks;

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';
		
		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
		
		$this->_registered_blocks = Kohana::config("settings.blocks");
	}
	
	function index()
	{
		$this->template->content = new View('admin/manage/blocks/main');
		$this->template->content->title = Kohana::lang('ui_admin.blocks');
		
		// Get Registered Blocks 
		if ( ! is_array($this->_registered_blocks) )
		{
			$this->_registered_blocks = array();
		}
		
		// Get Active Blocks
		$active_blocks = Settings_Model::get_setting('blocks');
		$active_blocks = array_filter(explode("|", $active_blocks));
		
		// setup and initialize form field names
		$form = array
		(
			'action' => '',
			'block' => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		if ( $_POST )
		{
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('block','required', 'alpha_dash');
			if ( ! array_key_exists($post->block, $this->_registered_blocks))
			{
				$post->add_error('block','exists');
			}
			
			if ($post->validate())
			{
				// Activate a block
				if ($post->action == 'a')
				{
					array_push($active_blocks, $post->block);
					Settings_Model::save_setting('blocks', implode("|", $active_blocks));
				}
				
				// Deactivate a block
				elseif ($post->action =='d')
				{
					$active_blocks = array_diff($active_blocks, array($post->block));
					Settings_Model::save_setting('blocks', implode("|", $active_blocks));
				}
			}
			else
			{
				$errors = arr::overwrite($errors, $post->errors('blocks'));
				$form_error = TRUE;
			}
		}
		
		// Sort the Blocks
		$sorted_blocks = blocks::sort($active_blocks, array_keys($this->_registered_blocks));
		
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->total_items = count($this->_registered_blocks);
		$this->template->content->registered_blocks = $this->_registered_blocks;
		$this->template->content->active_blocks = $active_blocks;
		$this->template->content->sorted_blocks = $sorted_blocks;
		
		// Javascript Header
		$this->template->tablerowsort_enabled = TRUE;
		$this->template->js = new View('admin/manage/blocks/blocks_js');
	}
	
	public function sort()
	{
		$this->auto_render = FALSE;
		$this->template = "";
		
		if ($_POST)
		{
			$post = Validation::factory($_POST);
			$post->add_rules('blocks','required');
			
			if ($post->validate())
			{
				$active_blocks = Settings_Model::get_setting('blocks');
				$active_blocks = array_filter(explode("|", $active_blocks));
				
				$blocks = array_map('trim', explode(',', $_POST['blocks']));
				$block_check = array();
				foreach ($blocks as $block)
				{
					if ( in_array($block, $active_blocks) )
					{
						$block_check[] = $block;
					}
				}
				
				Settings_Model::save_setting('blocks', implode("|", $block_check));
				echo 'success';
				return;
			}
		}
		echo 'failure';
		return;
	}
}
