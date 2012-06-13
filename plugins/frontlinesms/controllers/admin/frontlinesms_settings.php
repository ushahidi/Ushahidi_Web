<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FrontlineSMS Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Clickatell Settings Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Frontlinesms_Settings_Controller extends Admin_Controller {
	public function index()
	{
		$this->template->this_page = 'addons';
		
		// Standard Settings View
		$this->template->content = new View("admin/addons/plugin_settings");
		$this->template->content->title = "FrontlineSMS Settings";
		
		// Settings Form View
		$this->template->content->settings_form = new View("frontlinesms/admin/frontlinesms_settings");
		
		// Do we have a frontlineSMS Key? If not create and save one on the fly
        $frontlinesms = ORM::factory('frontlinesms', 1);
		
		if ($frontlinesms->loaded AND $frontlinesms->frontlinesms_key)
		{
			$frontlinesms_key = $frontlinesms->frontlinesms_key;
		}
		else
		{
			$frontlinesms_key = strtoupper(text::random('alnum',8));
            $frontlinesms->frontlinesms_key = $frontlinesms_key;
            $frontlinesms->save();
		}

		$this->template->content->settings_form->frontlinesms_key = $frontlinesms_key;
		$this->template->content->settings_form->frontlinesms_link = url::site()."frontlinesms/?key=".$frontlinesms_key."&s=\${sender_number}&m=\${message_content}";
	}
}