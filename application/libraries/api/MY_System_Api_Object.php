<?php defined('SYSPATH') or die('No direct script access.');
/**
 * System_Api_Object
 *
 * This class handles private functions that not accessbile by the public
 * via the API.
 *
 * @version 24 - Emmanuel Kala 2010-10-22
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class System_Api_Object extends Api_Object_Core {

	protected $replar;

	public function __construct($api_service)
	{
		$this->replar = array();
		parent::__construct($api_service);
	}

	/**
	 * Implementation of abstract method declared in superclass
	 */
	public function perform_task()
	{
		// System information mainly obtained through use of callback
		// Therefore set the default response to "not found"
		$this->set_error_message(array("error" => $this->api_service->get_error_msg(999)));
	}

	/**
	 * Get an ushahidi deployment version number.
	 *
	 * @param string response_type - JSON or XML
	 *
	 * @return string
	 */
	public function get_version_number()
	{
		$json_version = array();
		$version = Kohana::config('settings.ushahidi_version');
		$database = Kohana::config('version.ushahidi_db_version');

		$ret_json_or_xml = '';
		// Will hold the JSON/XML string to return

		if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
		{
			$json_version[] = array("version" => $version, "database" => $database);
		}
		else
		{
			$json_version['version'] = array("version" => $version, "database" => $database);
			$this->replar[] = 'version';
		}

		// Get Active Plugins
		$plugins = ORM::factory('plugin')->where('plugin_active = 1')->orderby('plugin_name', 'ASC')->find_all();
		$active_plugins = array();
		foreach ($plugins as $plugin)
		{
			$active_plugins[] = $plugin->plugin_name;
		}

		$features = array(
			'admin_reports_v2' => TRUE,
			'api_key' => FALSE,
			'jsonp' => TRUE,
		);

		// Create the json array
		$data = array("payload" =>
			array(
				"domain" => $this->domain,
				"version" => $json_version,
				"checkins" => Kohana::config('settings.checkins'),
				"email" => Kohana::config('settings.site_email'),
				"sms" => Kohana::config('settings.sms_no1'),
				"plugins" => $active_plugins,
				"features" => $features,
			),
			"error" => $this->api_service->get_error_msg(0)
		);

		if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
		{
			$ret_json_or_xml = $this->array_as_json($data);
		}
		else
		{
			$ret_json_or_xml = $this->array_as_xml($data, $this->replar);
		}

		$this->response_data = $ret_json_or_xml;
	}

	/**
	 * Get true or false depending on MHI being enabled or not
	 *
	 * @param string response_type - JSON or XML
	 *
	 * @return string
	 */
	public function get_mhi_enabled()
	{
		$enabled = 'FALSE';
		$ret_json_or_xml = '';
		// Will hold the JSON/XML string to return

		if (Kohana::config('config.enable_mhi') == TRUE)
		{
			$enabled = 'TRUE';
		}

		//create the json array
		$data = array("payload" => array("domain" => $this->domain, "mhienabled" => $enabled), "error" => $this->api_service->get_error_msg(0));

		if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
		{
			$ret_json_or_xml = $this->array_as_json($data);
		}
		else
		{
			$ret_json_or_xml = $this->array_as_xml($data, $this->replar);
		}

		$this->response_data = $ret_json_or_xml;
	}

	/**
	 * Get true or false depending on whether HTTPS has been enabled or not
	 *
	 * @param string response_type - JSON or XML
	 *
	 * @return string
	 */
	public function get_https_enabled()
	{
		$enabled = 'FALSE';
		$ret_json_or_xml = '';
		// Will hold the JSON/XML string to return

		if (Kohana::config('core.site_protocol') == 'https')
		{
			$enabled = 'TRUE';
		}

		//create the json array
		$data = array("payload" => array("domain" => $this->domain, "httpsenabled" => $enabled), "error" => $this->api_service->get_error_msg(0));

		if ($this->response_type == 'json' OR $this->response_type == 'jsonp')
		{
			$ret_json_or_xml = $this->array_as_json($data);
		}
		else
		{
			$ret_json_or_xml = $this->array_as_xml($data, $this->replar);
		}

		$this->response_data = $ret_json_or_xml;

	}

}
