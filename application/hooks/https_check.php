<?php defined('SYSPATH') or die('No direct script access.');
/**
 * HTTPS Check Hook
 *
 * This hook checks if HTTPS has been enabled and whether the Webserver is HTTPS capable
 * If the sanity check fails, $config['site_protocol'] is set back to 'http'
 * and a redirect is performed so as to re-load the URL using the newly set protocol
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     HTTPS Check Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class https_check {

	private $https_enabled;   // Flag to denote whether HTTPS is enabled/disabled

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->https_enabled = Kohana::config('core.site_protocol');

		// Hook into routing
		Event::add_after('system.routing', array('Router', 'setup'), array($this, 'rewrite_url'));
	}

	/**
	 * Rewrites the URL depending on whether HTTPS is enabled/disabled
	 *
	 * NOTES: - Emmanuel Kala, 18th Feb 2011
	 * This may bring issues with accessing the API (querying or posting) via mobile and/or external applications
	 * as they may not support querying information via HTTPS
	 *
	 */
	public function rewrite_url()
	{
		if ($this->https_enabled == 'HTTPS')
		{
			$is_https_request = (array_key_exists('HTTPS', $_SERVER) AND $_SERVER['HTTPS'] == 'on')
				? TRUE
				: FALSE;

			if (($this->https_enabled AND ! $is_https_request) OR ( ! $this->https_enabled AND $is_https_request))
			{
				url::redirect(url::base().url::current().Router::$query_string);
			}
		}
	}
}

new https_check();