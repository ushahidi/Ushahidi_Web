<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Translate Reports Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module     Translate Reports Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class translatereports {
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		$this->cache = new Cache;
		
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports')
		{
			switch (Router::$method)
			{
				// Hook into the Report Add/Edit Form in Admin
				case 'edit':
					// Hook into the form itself
					Event::add('ushahidi_action.report_form_admin', array($this, '_report_form'));
					// Hook into the report_edit (post_SAVE) event
					Event::add('ushahidi_action.report_edit', array($this, '_report_form_submit'));
					break;
				
				// Hook into the Report view (front end)
				case 'view':
					Event::add('ushahidi_action.report_extra', array($this, '_report_view'));
					break;
			}
		}
		elseif (Router::$controller == 'feed')
		{
			// Add Actionable Tag to RSS Feed
			//Event::add('ushahidi_action.feed_rss_item', array($this, '_feed_rss'));
		}
	}
	
	/**
	 * Add Actionable Form input to the Report Submit Form
	 */
	public function _report_form()
	{	
		// Load the View
		$view = View::factory('translatereports_form');
		
		// Get the ID of the Incident (Report)
		$id = Event::$data;
		
		// Initialize Array
		$view->translations = array();
		
		if ($id)
		{
			// Grab Existing Translations
			$translation_items = ORM::factory('translatereports')
				->where('incident_id', $id)
				->find_all();

			$translations = array();
			foreach ($translation_items as $item)
			{
				$translations[$item->id] = array(
						'lang'=>$item->lang,
						'incident_description'=>$item->incident_description
					);
			}
			$view->translations = $translations;
		}
		
		if (Kohana::config('translatereports.languages') == NULL)
		{
			$view->locales = $this->cache->get('locales');
		}else{
			$view->locales = Kohana::config('translatereports.languages');
		}
		
		$view->max_new_translations = Kohana::config('translatereports.max_new_translations');
		
		$view->render(TRUE);
	}
	
	/**
	 * Handle Form Submission and Save Data
	 */
	public function _report_form_submit()
	{
		// Get ID of the incident we either added or are modifying
		$event_data = Event::$data;
		$incident_id = $event_data->id;
		
		$max_new_translations = Kohana::config('translatereports.max_new_translations');
		
		$incident_translations = $_POST['incident_translation'];
		$translation_langs = isset($_POST['translation_lang']) ? $_POST['translation_lang'] : array();
		
		$completed_translations = array();
		
		foreach($incident_translations as $key => $translation)
		{	
			if(is_int($key))
			{
				// If there is no translation, then skip it.
				if($translation == NULL OR $translation == '') continue;
			
				$lang = $translation_langs[$key];
			}else{
				$lang = $key;
			}
			
			// If there was previously a translation and now it's empty, delete it.
			if($translation == NULL OR $translation == '')
			{
				$translation_item = ORM::factory('translatereports')
					->where('incident_id', $incident_id)
					->where('lang', $lang)
					->find();
				$translation_item->delete();
				echo 'Deleting';
				continue;
			}
			
			
			$translation_item = ORM::factory('translatereports')
				->where('incident_id', $incident_id)
				->where('lang', $lang)
				->find();
			$translation_item->incident_id = $incident_id;
			$translation_item->lang = $lang;
			$translation_item->incident_description = $translation;
			$translation_item->save();
		}
	}
	
	/**
	 * Render the Action Taken Information to the Report
	 * on the front end
	 */
	public function _report_view()
	{
		// Load the View
		$view = View::factory('translatereports_frontend');
		
		// Get ID of the incident we either added or are modifying
		$incident_id = Event::$data;
		
		// Grab Existing Translations
		$translation_items = ORM::factory('translatereports')
			->where('incident_id', $incident_id)
			->find_all();

		$translations = array();
		foreach ($translation_items as $item)
		{
			$translations[$item->lang] = $item->incident_description;
		}
		$view->translations = $translations;
		
		if (Kohana::config('translatereports.languages') == NULL)
		{
			$view->locales = $this->cache->get('locales');
		}else{
			$view->locales = Kohana::config('translatereports.languages');
		}
		
		$view->render(TRUE);
	}
}

new translatereports;