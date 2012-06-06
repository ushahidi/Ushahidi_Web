<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Badges Controller
 * Add and assign badges
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

class Badges_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';

		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}

	function index()
	{
		$this->template->content = new View('admin/manage/badges/main');
		$this->template->content->title = Kohana::lang('ui_main.badges');

		// setup and initialize form field names
		$form = array
		(
			'id' => '',
			'name' => '',
			'description' => ''
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
			$post->add_rules('action', 'required', 'alpha', 'length[1,1]');
			$post->add_rules('name', 'standard_text', 'length[1,250]');
			$post->add_rules('description', 'standard_text');
			
			// Add some additional rules for adding a new badge
			if (isset($_POST['action']) AND $_POST['action'] == 'a')
			{
				// Users can select a badge or upload one. See what they are doing.
				if ($_FILES['image']['error'] == 0)
				{
					// Uploading an image, if uploaded, it overrules a selection
					$uploading_custom_badge = true;
					$post->add_rules('image', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[100K]');
				}
				else
				{
					// Selecting one
					$uploading_custom_badge = false;
					$post->add_rules('selected_badge', 'required');
				}
			}

			if ($post->validate())
			{
				// ADD
				if ($post->action == 'a')
				{
					// Step 1. Save badge name and description

					$badge = new Badge_Model();
					$badge->name = $post->name;
					$badge->description = $post->description;
					$badge->save();

					// Step 2. Save badge image
					
					if ($uploading_custom_badge)
					{
						$filename = upload::save('image');
					}
					else
					{
						// We already have this on the filesystem! Use that one.
						$bp_path = MEDIAPATH.'img/badge_packs/';
						$selected_badge = base64_decode(str_ireplace('badge_','',$post->selected_badge));
						$filename = $bp_path.$selected_badge;
					}
					
					if ($filename)
					{
						$new_filename = "badge_".$badge->id."_".time();
						$file_type = strrev(substr(strrev($filename),0,4));

						// Large size
						$l_name = $new_filename.$file_type;
						Image::factory($filename)->save(Kohana::config('upload.directory', TRUE).$l_name);

						// Medium size
						$m_name = $new_filename.'_m'.$file_type;
						Image::factory($filename)->resize(80,80,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE).$m_name);

						// Thumbnail
						$t_name = $new_filename.'_t'.$file_type;
						Image::factory($filename)->resize(60,60,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE).$t_name);
						
						// Name the files for the DB
						$media_link = $l_name;
						$media_medium = $m_name;
						$media_thumb = $t_name;
						
						// Okay, now we have these three different files on the server, now check to see
						//   if we should be dropping them on the CDN
						
						if(Kohana::config("cdn.cdn_store_dynamic_content"))
						{
							$cdn = new cdn;
							$media_link = $cdn->upload($media_link);
							$media_medium = $cdn->upload($media_medium);
							$media_thumb = $cdn->upload($media_thumb);
							
							// We no longer need the files we created on the server. Remove them.
							$local_directory = rtrim(Kohana::config('upload.directory', TRUE), '/').'/';
							unlink($local_directory.$new_filename.$file_type);
							unlink($local_directory.$new_filename.'_m'.$file_type);
							unlink($local_directory.$new_filename.'_t'.$file_type);
						}
						
						// Only perform this operation if it's not coming from a badge pack,
						//   otherwise we would lose badges every time we selected them!
						if ($uploading_custom_badge)
						{
							// Remove the temporary file
							unlink($filename);
						}

						// Delete old badge image
						ORM::factory('media')->where(array('badge_id' => $badge->id))->delete_all();

						// Save new badge image
						$media = new Media_Model();
						$media->badge_id = $badge->id;
						$media->media_type = 1; // Image
						$media->media_link = $media_link;
						$media->media_medium = $media_medium;
						$media->media_thumb = $media_thumb;
						$media->media_date = date("Y-m-d H:i:s",time());
						$media->save();
					}
				}

				// ASSIGN USER
				if ($post->action == 'b')
				{
					$badge_user = new Badge_User_Model();
					$badge_user->badge_id = $post->badge_id;
					$badge_user->user_id = $post->assign_user;
					$badge_user->save();
				}

				// REVOKE USER
				if ($post->action == 'r')
				{
					ORM::factory('badge_user')->where(array('badge_id' => (int)$post->badge_id, 'user_id' => (int)$post->revoke_user))->delete_all();
				}

				// DELETE
				elseif ($post->action =='d')
				{
					// Remove from badge table
					ORM::factory('badge')->delete((int)$post->badge_id);

					// Remove from media
					ORM::factory('media')->where(array('badge_id' => (int)$post->badge_id))->delete_all();

					// Remove from assignment
					ORM::factory('badge_user')->where(array('badge_id' => (int)$post->badge_id))->delete_all();
				}
			}
			else
			{
				$errors = arr::overwrite($errors, $post->errors('badges'));
				$form_error = TRUE;
			}
		}
		
		// Badge Pack stuff
		$this->template->content->badge_packs = badges::get_packs();

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Get badges
		$this->template->content->badges = Badge_Model::badges();
		$this->template->content->total_items = count($this->template->content->badges);

		// Get all users for dropdowns
		$users_result = ORM::factory('user')->orderby('name', 'asc')->find_all();
		$users = array();
		foreach($users_result as $user)
		{
			$users[$user->id] = $user->username;
		}
		$this->template->content->users = $users;

		// Javascript Header
		$this->template->js = new View('admin/manage/badges/badges_js');
	}
}
