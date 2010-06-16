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

	static function get_user_sites($user_id=FALSE)
	{
		$result = ORM::factory('mhi_site')->where('user_id',$user_id)->find_all();

		$sites = array();
		foreach ($result as $res)
			$sites[] = $res;

		return $sites;
	}
}
