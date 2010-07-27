<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for MHI sites
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class mhi_site_Model extends ORM
{
	protected $table_name = 'mhi_site';

	protected $primary_key = 'id';

	protected $primary_val = 'site_domain';

	public $site_name;

	public $site_tagline;

	static function domain_exists($site_domain)
	{

		// TODO: We could also do a subdomain lookup to see if the subdomain is being used already for something other than MHI

		// Check if the subdomain has been taken

		$count = ORM::factory('mhi_site')->where('site_domain',$site_domain)->count_all();
		if ($count != 0)
			return true;

		return false;
	}

	//site_domains must be an array
	static function domain_owner($site_domains)
	{
		$return_array = array();
		// Return an array of the MHI user id of the owner of the domain

		foreach($site_domains as $domain)
		{
			$result = ORM::factory('mhi_site')->where('site_domain',$domain)->find_all();
			foreach ($result as $res){
				$return_array[$res->id] = $res->user_id;
			}
		}

		return $return_array;
	}

	// This function activates or deactivates a site

	static function activation($domain,$activation)
	{
		$result = ORM::factory('mhi_site')->where('site_domain',$domain)->find_all();

		foreach ($result as $res){
			$site = ORM::factory('mhi_site',$res->id);
			$site->site_active = $activation;
			$site->save();
		}

		return true;
	}

	// $a should be an assoc array including user_id, site_domain, site_privacy, site_active

	static function save_site($a)
	{
		$mhi_site = ORM::factory('mhi_site');
		$mhi_site->user_id = $a['user_id'];
		$mhi_site->site_domain = $a['site_domain'];
		$mhi_site->site_privacy = $a['site_privacy'];
		$mhi_site->site_active = $a['site_active'];
		$mhi_site->site_dateadd = date('Y-m-d H:i:s', time());
		$mhi_site->save();

		$result = ORM::factory('mhi_site')->where('site_domain',$a['site_domain'])->find_all();
		$id = 0;
		foreach ($result as $res)
			$id = $res->id;

		return $id;
	}

	// Get sites, user_id returns all of that users sites

	static function get_user_sites($user_id=FALSE,$detailed_data=FALSE)
	{
		$result = ORM::factory('mhi_site')->where('user_id',$user_id)->find_all();

		$sites = array();
		foreach ($result as $res)
		{
			if ($detailed_data != FALSE)
			{
				// Go to the deployment's database and grab some additional details
				$details = Mhi_Site_Model::get_site_details($res->site_domain);
				$res->site_name = $details['site_name'];
				$res->site_tagline = $details['site_tagline'];
			}
			$sites[] = $res;
		}

		return $sites;
	}

	function get_site_details($domain)
	{
		$mhi_db = Kohana::config('database.default');
		$table_prefix = $mhi_db['table_prefix'];
		$mhi_db_name = $mhi_db['connection']['database'];

		// Switch to new DB for a moment

		$base_db = DBGenesis::current_db();

		mysql_query('USE '.$base_db.'_'.$domain.';');

		// START: Everything that happens in the deployment DB happens below
		$settings = ORM::factory('settings', 1);
		$array = array(
			'site_name' => $settings->site_name,
			'site_tagline' => $settings->site_tagline
			);

		// END: Everything that happens in the deployment DB happens above

		//Switch back to our db, otherwise we would be running off some other deployments DB and that wouldn't work
		mysql_query('USE '.$mhi_db_name);

		return $array;
	}

	function get_db_versions()
	{
		$mhi_db = Kohana::config('database.default');
		$table_prefix = $mhi_db['table_prefix'];
		$mhi_db_name = $mhi_db['connection']['database'];

		$dbs = Mhi_Site_Database_Model::get_all_db_details();

		// Switch to new DB for a moment

		$array = array();

		foreach($dbs as $db)
		{
			mysql_query('USE '.$db.';');

			// START: Everything that happens in the deployment DB happens below
			$settings = ORM::factory('settings', 1);
			$array[$db] = $settings->db_version;
		}

		// END: Everything that happens in the deployment DB happens above

		//Switch back to our db, otherwise we would be running off some other deployments DB and that wouldn't work
		mysql_query('USE '.$mhi_db_name);

		return $array;
	}
}
