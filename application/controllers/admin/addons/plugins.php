<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Addon Manager
 * Install new Plugins & Themes
 * Credits To Jeremy Bush at Zombor.net (http://www.zombor.net/)
 *
 * Portions of this code:
 * Copyright (c) 2008-2009, Argentum Team
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
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

class Plugins_Controller extends Admin_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'addons';

		// If this is not a super-user account, redirect to dashboard
		if(!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
        {
             url::redirect('admin/dashboard');
		}
	}
	
	public function index()
	{
		$this->template->content = new View('admin/addons/plugins');
		$this->template->content->title = 'Addons';
		
		if (isset($_GET['status']) && ! empty($_GET['status']))
		{
			$status = $_GET['status'];
			
			if (strtolower($status) == 'a')
			{
				$filter = 'plugin_active = 1';
			}
			elseif (strtolower($status) == 'i')
			{
				$filter = 'plugin_active = 0';
			}
			else
			{
				$status = "0";
				$filter = '1=1';
			}
		}
		else
		{
			$status = "0";
			$filter = '1=1';
		}
		
		// Add the hidden plugins to the list of plugins to filter out
		if(count(Kohana::config('plugins.hide_from_list')) != 0) {
			$hide_from_list = array_map(array(Database::instance(), 'escape'), Kohana::config('plugins.hide_from_list'));
			$filter .= ' AND plugin_name NOT IN ('.implode(",", $hide_from_list).')';
		}

		$db = new Database();

		// Update the plugin list in the database
		$d = dir(PLUGINPATH);
		$directories = array();
		while (($entry = $d->read()) !== FALSE)
		{
			// Set the plugin to not enabled by default
			// Don't include hidden folders
			if ($entry[0] != '.') $directories[$entry] = FALSE;
		}
		
		// Sync the folder with the database
		foreach ($directories as $dir => $found)
		{
			// Only include the plugin if it contains readme.txt
			$file = PLUGINPATH.$dir."/readme.txt";
			if ( file::file_exists_i($file) AND ! count($db->from('plugin')->where('plugin_name', $dir)->limit(1)->get()))
			{
				$plugin = ORM::factory('plugin');
				$plugin->plugin_name = $dir;
				$plugin->save();
			}
		}

		// Remove Any Plugins not found in the plugins folder from the database
		foreach (ORM::factory('plugin')->find_all() as $plugin)
		{
			$file = PLUGINPATH.$plugin->plugin_name."/readme.txt";
			if ( ! array_key_exists($plugin->plugin_name, $directories) OR ! file::file_exists_i($file) )
			{
				$plugin->delete();
			}
		}		
				
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			//  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('plugin_id.*','required','numeric');

			if ($post->validate())
			{
				if ($post->action == 'a')
				{
					// Activate Action
					foreach(array_unique($post->plugin_id) as $item)
					{
						$plugin = ORM::factory('plugin', $item);
						// Make sure we run the installer if it hasnt been installed yet.
						// Then mark it as installed
						if ($plugin->loaded AND $plugin->plugin_name)
						{
							Kohana::config_set('core.modules', array_merge(Kohana::config('core.modules'), array(PLUGINPATH.$plugin->plugin_name)));
							
							// Name of the class (First letter of class should be capitalized)
							$class = ucfirst($plugin->plugin_name).'_Install';
							
							// Find the Library File
							$path = $this->_find_install($plugin->plugin_name);
							if ($path)
							{
								include $path;

								// Run the installer
								$install = new $class;
								$install->run_install();
							}
							
							// Mark as Active and Mark as Installed
							$plugin->plugin_active = 1;
							$plugin->plugin_installed = 1;
							$plugin->save();
						}
					}
				}
				elseif ($post->action == 'i')	
				{ // Deactivate Action
					foreach($post->plugin_id as $item)
					{
						$plugin = ORM::factory('plugin', $item);
						if ($plugin->loaded)
						{
							$plugin->plugin_active = 0;
							$plugin->save();
						}
					}
				}
				elseif ($post->action == 'd')
				{ // Delete Action
					foreach($post->plugin_id as $item)
					{
						$plugin = ORM::factory('plugin', $item);
						if ($plugin->loaded AND $plugin->plugin_name)
						{
							Kohana::config_set('core.modules', array_merge(Kohana::config('core.modules'), array(PLUGINPATH.$plugin->plugin_name)));
							
							// Name of the class (First letter of class should be capitalized)
							$class = ucfirst($plugin->plugin_name).'_Install';
							
							// Find the Library File
							$path = $this->_find_install($plugin->plugin_name);
							
							if ($path)
							{
								include $path;

								// Run the uninstaller
								$install = new $class;
								$install->uninstall();
							}
							
							// Mark as InActive and Mark as UnInstalled
							$plugin->plugin_active = 0;
							$plugin->plugin_installed = 0;
							$plugin->save();
						}
					}
				}
			}
			else
			{
				$form_error = TRUE;
			}

		}
		
		$plugins = ORM::factory('plugin')
			->where($filter)
			->orderby('plugin_name', 'ASC')
			->find_all();
		$this->template->content->plugins = $plugins;
		$this->template->content->total_items = $plugins->count();
		
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		
		// Status Tab
		$this->template->content->status = $status;
		
		// Javascript Header
		$this->template->js = new View('admin/addons/addons_js');
	}
	
	/**
	 * Using this function because someone somewhere will name this file wrong!!!
	 */
	private function _find_install($plugin_name)
	{
		if ($path = Kohana::find_file('libraries', $plugin_name.'_install'))
		{ // plugin_i
			return $path;
		}
		elseif ($path = Kohana::find_file('libraries', $plugin_name.'_Install'))
		{ // plugin_I
			return $path;
		}
		elseif ($path = Kohana::find_file('libraries', ucfirst($plugin_name).'_install'))
		{ // Plugin_i
			return $path;
		}
		elseif ($path = Kohana::find_file('libraries', ucfirst($plugin_name).'_Install'))
		{ // Plugin_I
			return $path;
		}
		else
		{
			return false;
		}
		
	}	
}
