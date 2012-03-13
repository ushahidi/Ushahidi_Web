<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Register Plugins Hook
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
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Register Plugins Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class register_plugins {
	/**
	 * Adds the register method to load after the find_uri Router method.
	 */
	public function __construct()
	{
		// Hook into routing
		if (Kohana::config('config.installer_check') == FALSE OR file_exists(DOCROOT."application/config/database.php"))
		{
			Event::add_after('system.routing', array('Router', 'find_uri'), array($this, 'register'));
		}

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');
	}

	/**
	 * Loads all ushahidi plugins
	 */
	public function register()
	{
		$db = Database::instance();
		$plugins = array();
		// Get the list of plugins from the db
		foreach ($db->getwhere('plugin', array(
				'plugin_active' => 1,
				'plugin_installed' => 1)) as $plugin)
		{
			$plugins[$plugin->plugin_name] = PLUGINPATH.$plugin->plugin_name;
		}

		// Now set the plugins
		Kohana::config_set('core.modules', array_merge(Kohana::config('core.modules'), $plugins));

		// We need to manually include the hook file for each plugin,
		// because the additional plugins aren't loaded until after the application hooks are loaded.
		foreach ($plugins as $key => $plugin)
		{
			if (file_exists($plugin.'/hooks'))
			{
				$d = dir($plugin.'/hooks'); // Load all the hooks
				while (($entry = $d->read()) !== FALSE)
					if ($entry[0] != '.')
					{
						// $plugin_base Variable gives plugin hook access to the base location of the plugin
						$plugin_base = url::base()."plugins/".$key."/";
						include $plugin.'/hooks/'.$entry;
					}
			}
		}
	}
}

new register_plugins;