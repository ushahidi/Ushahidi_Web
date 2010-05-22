<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Addon Manager
 * Install new Plugins & Themes
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Addon Manager Controller
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
		$this->template->content = new View('admin/plugins');
		$this->template->content->title = 'Addons';
		
		$db = new Database();
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$status = "0";

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
			if ( ! count($db->from('plugin')->where('plugin_name', $dir)->limit(1)->get()))
			{
				$plugin = ORM::factory('plugin');
				$plugin->plugin_name = $dir;
				$plugin->save();
			}
		}

		// Now remove the ones that weren't found from the database
		foreach (ORM::factory('plugin')->find_all() as $plugin)
			if ( ! array_key_exists($plugin->plugin_name, $directories))
				$plugin->delete();
			else
				$directories[$plugin->plugin_name] = $plugin;

		if ( ! $_POST)
		{
			$this->template->content->plugins = $directories;
		}
		else
		{
			try
			{
				unset($_POST['go']);
				// First unset everything
				$db->query('UPDATE `'.$this->table_prefix.'plugin` SET `plugin_active` = 0');

				// Then set all the applicable plugins
				foreach ($this->input->post() as $field => $active)
				{
					$plugin = ORM::factory('plugin', $field);
					// Make sure we run the installer if it hasnt been installed yet.
					// Then mark it as installed
					if (count($db->getwhere('plugin', array('plugin_installed' => FALSE, 'plugin_name' => $field))))
					{
						Kohana::config_set('core.modules', array_merge(Kohana::config('core.modules'), array(PLUGINPATH.$field)));
						$class = ucfirst($field).'_Install';
						if ($path = Kohana::find_file('libraries', $field.'_install'))
						{
							include $path;

							// Run the installer
							$install = new $class;
							$install->run_install();
						}
					}

					$plugin->plugin_active = TRUE;
					$plugin->plugin_installed = TRUE;
					$plugin->save();
				}

				Database::instance()->clear_cache();
				foreach (ORM::factory('plugin')->find_all() as $plugin)
					$directories[$plugin->plugin_name] = $plugin;

				$this->template->content->plugins = $directories;
			}
			catch (Kohana_Database_Exception $e)
			{
				$this->template->content->plugins = $directories;
			}
		}
		
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		
		// Total Reports
		$this->template->content->total_items = count($directories);
		
		// Status Tab
		$this->template->content->status = $status;
	}	
}