<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Statistics
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Stats_Model extends ORM {

	static $time_out = 1;

	/**
	 * Generates the JavaScript for stats tracking
	 */
	public static function get_javascript()
	{
		// Make sure cURL is installed
		if ( ! function_exists('curl_exec'))
		{
			throw new Kohana_Exception('footer.cURL_not_installed');
			return false;
		}

		// Get the stat id
		$stat_id = Settings_Model::get_setting('stat_id');

		// If stats isn't set, ignore this
		if ($stat_id == 0)
			return '';

		$cache = Cache::instance();
		$tag = $cache->get(Kohana::config('settings.subdomain').'_piwiktag');

		if ( ! $tag)
		{ // Cache is Empty so Re-Cache

			// Grabbing the URL to update stats URL, Name, Reports, etc on the stats server
			$additional_query = '';
			if (isset($_SERVER["HTTP_HOST"]))
			{
				// Grab the site domain from the config and trim any whitespaces
				$site_domain = trim(Kohana::config('config.site_domain'));
				$slashornoslash = '';
				if (empty($site_domain) OR $site_domain{0} != '/')
				{
					$slashornoslash = '/';
				}

				// URL
				$val = 'http://'.$_SERVER["HTTP_HOST"].$slashornoslash.$site_domain;
				$additional_query = '&val='.base64_encode($val);

				// Site Name
				$site_name = utf8tohtml::convert(Kohana::config('settings.site_name'),TRUE);
				$additional_query .= '&sitename='.base64_encode($site_name);

				// Version
				$version = Kohana::config('settings.ushahidi_version');
				$additional_query .= '&version='.base64_encode($version);

				// Report Count
				$number_reports = ORM::factory("incident")->where("incident_active", 1)->count_all();
				$additional_query .= '&reports='.base64_encode($number_reports);

				// Latitude
				$latitude = Kohana::config('settings.default_lat');
				$additional_query .= '&lat='.base64_encode($latitude);

				// Longitude
				$longitude = Kohana::config('settings.default_lon');
				$additional_query .= '&lon='.base64_encode($longitude);
			}

			$url = 'https://tracker.ushahidi.com/dev.px.php?task=tc&siteid='.$stat_id.$additional_query;
			$curl_handle = curl_init();

			// cURL options
			$curl_options = array(
				CURLOPT_URL => $url,

				// Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
				CURLOPT_CONNECTTIMEOUT => self::$time_out,

				// Set cURL to store data in variable instead of print
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => FALSE
			);

			curl_setopt_array($curl_handle, $curl_options);

			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);

			try
			{
				// This works because the tracking code is only wrapped in one tag
				$tag = (string) @simplexml_load_string($buffer);
			}
			catch (Exception $e)
			{
				// In case the xml was malformed for whatever reason, we will just guess what the tag should be here
				$tag = <<< STATSCOLLECTOR
					<!-- Stats Collector -->
					<script type="text/javascript">
					setTimeout(function() {
						var statsCollector = document.createElement('img');
						    statsCollector.src = document.location.protocol + "//tracker.ushahidi.com/piwik/piwik.php?idsite={$stat_id}&rec=1";
						    statsCollector.style.cssText = "width: 1px; height: 1px; opacity: 0.1;";

						document.body.appendChild(statsCollector);
					}, 100);
					</script>
					<!-- End Stats Collector -->
STATSCOLLECTOR;
			}

			// Reset Cache Here
			$cache->set(Kohana::config('settings.subdomain').'_piwiktag', $tag, array('piwiktag'), 86400); // 1 Day
		}

		return $tag;

	}

	/*
	*	range will be ignored if dp1 and dp2 are set
	*	dp1 and dp2 format is YYYY-MM-DD
	*/
	public static function get_hit_stats($range=30, $dp1=NULL, $dp2=NULL)
	{
		// Get ID for stats
		$stat_id = Settings_Model::get_setting('stat_id');
		$stat_key = Settings_Model::get_setting('stat_key');

		$twodates = '';
		if ($dp1 !== NULL AND $dp2 !== NULL)
		{
			$twodates = '&twodates='.urlencode($dp1.','.$dp2);
		}

		$stat_url = 'https://tracker.ushahidi.com/px.php?stat_key='.$stat_key
		    .'&task=stats&siteid='.urlencode($stat_id).'&period=day&range='.urlencode($range).$twodates;

		// Ignore errors since we are error checking later

		$response = @simplexml_load_string(self::_curl_req($stat_url));

		// If we encounter an error, return false
		if
		(
			isset($response->result->error[0]) OR
			isset($response->error[0]) OR
			! isset($response->visits->result)
		)
		{
			Kohana::log('error', "Error on stats request");
			return false;
		}

		foreach ($response->visits->result as $res)
		{
			$dt = $res['date'];
			$y = substr($dt,0,4);
			$m = substr($dt,5,2);
			$d = substr($dt,8,2);
			$timestamp = mktime(0,0,0,$m,$d,$y)*1000;

			if (isset($res->nb_visits))
			{
				$data['visits'][ (string) $timestamp] = (string) $res->nb_visits;
			}
			else
			{
				$data['visits'][ (string) $timestamp] = '0';
			}

			if (isset($res->nb_uniq_visitors))
			{
				$data['uniques'][ (string) $timestamp] = (string) $res->nb_uniq_visitors;
			}
			else
			{
				$data['uniques'][ (string) $timestamp] = '0';
			}

			if (isset($res->nb_actions))
			{
				$data['pageviews'][ (string) $timestamp] = (string) $res->nb_actions;
			}
			else
			{
				$data['pageviews'][ (string) $timestamp] = '0';
			}
		}

		return $data;
	}

	static function get_hit_countries($range=30, $dp1=NULL, $dp2=NULL)
	{
		$stat_id = Settings_Model::get_setting('stat_id');
		$stat_key = Settings_Model::get_setting('stat_key');

		$twodates = '';
		if ($dp1 !== NULL AND $dp2 !== NULL)
		{
			$twodates = '&twodates='.urlencode($dp1.','.$dp2);
		}

		$stat_url = 'https://tracker.ushahidi.com/px.php?stat_key='.$stat_key
		    .'&task=stats&siteid='.urlencode($stat_id).'&period=day&range='.urlencode($range).$twodates;

		// Ignore errors since we are error checking later

		$response = @simplexml_load_string(self::_curl_req($stat_url));

		// If we encounter an error, return false
		if
		(
			isset($response->result->error[0]) OR
			isset($response->error[0]) OR
			! isset($response->countries->result)
		)
		{
			Kohana::log('error', "Error on stats request");
			return false;
		}

		$data = array();
		foreach ($response->countries->result as $res)
		{
			$date = (string) $res['date'];
			foreach ($res->row as $row)
			{
				$code = (string) $row->code;
				$data[$date][$code]['label'] = (string) $row->label;
				$data[$date][$code]['uniques'] = (string) $row->nb_uniq_visitors;
				$logo = (string) $row->logo;
				$data[$date][$code]['logo'] = 'https://tracker.ushahidi.com/piwik/'.$logo;
			}
		}

		return $data;

	}

	/*
	* get an array of report counts
	* @param approved - Only count approved reports if true
	* @param by_time - Format array with timestamp as the key if true
	* @param range - Number of days back from today to pull reports from. Will end up defaulting to 100000 days to get them all.
	* @param dp1 - Arbitrary date range. Low date. YYYY-MM-DD
	* @param dp2 - Arbitrary date range. High date. YYYY-MM-DD
	*/
	static function get_report_stats($approved=FALSE, $by_time=FALSE, $range=NULL, $dp1=NULL, $dp2=NULL, $line_chart_data=FALSE)
	{
		if ($range === NULL)
		{
			$range = 100000;
		}

		if ($dp1 === NULL)
		{
			$dp1 = 0;
		}

		if ($dp2 === NULL)
		{
			$dp2 = '3000-01-01';
		}

		// Set up the range calculation
		$time = time() - ($range*86400);
		$range_date = date('Y-m-d', $time);

		// Only grab approved
		if ($approved)
		{
			$reports = ORM::factory('incident')
			    ->where('incident_active', '1')
			    ->where('incident_date >=', $dp1)
			    ->where('incident_date <=',$dp2)
			    ->where('incident_date >', $range_date)
			    ->find_all();
		}
		else
		{
			$reports = ORM::factory('incident')
			    ->where('incident_date >=', $dp1)
			    ->where('incident_date <=', $dp2)
			    ->where('incident_date >', $range_date)
			    ->find_all();
		}

		$reports_categories = ORM::factory('incident_category')->find_all();

		// Initialize arrays so we don't error out
		$report_data = array();
		$verified_counts = array();
		$approved_counts = array();
		$all = array();
		$earliest_timestamp = 32503680000; // Year 3000 in epoch so we can catch everything less than this.
		$latest_timestamp = 0;

		// Gather some data into an array on incident reports
		$num_reports = 0;
		foreach ($reports as $report)
		{
			$timestamp = (string) strtotime(substr($report->incident_date,0,10));
			$report_data[$report->id] = array(
				'date'=>$timestamp,
				'mode'=>$report->incident_mode,
				'active'=>$report->incident_active,
				'verified'=>$report->incident_verified
			);

			if ($timestamp < $earliest_timestamp)
			{
				$earliest_timestamp = $timestamp;
			}

			if ($timestamp > $latest_timestamp)
			{
				$latest_timestamp = $timestamp;
			}

			if ( ! isset($verified_counts['verified'][$timestamp]))
			{
				$verified_counts['verified'][$timestamp] = 0;
				$verified_counts['unverified'][$timestamp] = 0;
				$approved_counts['approved'][$timestamp] = 0;
				$approved_counts['unapproved'][$timestamp] = 0;
				$all[$timestamp] = 0;
			}

			$all[$timestamp]++;

			if ($report->incident_verified == 1)
			{
				$verified_counts['verified'][$timestamp]++;
			}
			else
			{
				$verified_counts['unverified'][$timestamp]++;
			}

			if ($report->incident_active == 1)
			{
				$approved_counts['approved'][$timestamp]++;
			}
			else
			{
				$approved_counts['unapproved'][$timestamp]++;
			}
			$num_reports++;
		}

		$category_counts = array();
		$lowest_date = 9999999999; // Really far in the future.
		$highest_date = 0;
		foreach ($reports_categories as $report)
		{
			// If this report category doesn't have any reports (in case we are only
			//  looking at approved reports), move on to the next one.
			if ( ! isset($report_data[$report->incident_id]))
				continue;

			$c_id = $report->category_id;
			$timestamp = $report_data[$report->incident_id]['date'];

			if ($timestamp < $lowest_date)
			{
				$lowest_date = $timestamp;
			}

			if ($timestamp > $highest_date)
			{
				$highest_date = $timestamp;
			}

			if ( ! isset($category_counts[$c_id][$timestamp]))
			{
				$category_counts[$c_id][$timestamp] = 0;
			}

			$category_counts[$c_id][$timestamp]++;
		}

		// Populate date range
		$date_range = array();
		$add_date = $lowest_date;
		while ($add_date <= $highest_date)
		{
			$date_range[] = $add_date;
			$add_date += 86400;
		}

		// Zero out days that don't have a count
		foreach ($category_counts as & $arr)
		{
			foreach ($date_range as $timestamp)
			{
				if ( ! isset($arr[$timestamp]))
				{
					$arr[$timestamp] = 0;
				}

				if ( ! isset($verified_counts['verified'][$timestamp]))
				{
					$verified_counts['verified'][$timestamp] = 0;
				}

				if ( ! isset($verified_counts['unverified'][$timestamp]))
				{
					$verified_counts['unverified'][$timestamp] = 0;
				}

				if ( ! isset($approved_counts['approved'][$timestamp]))
				{
					$approved_counts['approved'][$timestamp] = 0;
				}

				if ( ! isset($approved_counts['unapproved'][$timestamp]))
				{
					$approved_counts['unapproved'][$timestamp] = 0;
				}

				if ( ! isset($all[$timestamp]))
				{
					$all[$timestamp] = 0;
				}

			}
			// keep dates in order
			ksort($arr);
			ksort($verified_counts['verified']);
			ksort($verified_counts['unverified']);
			ksort($approved_counts['approved']);
			ksort($approved_counts['unapproved']);
			ksort($all);

		}

		// Add all our data sets to the array we are returning
		$data['category_counts'] = $category_counts;
		$data['verified_counts'] = $verified_counts;
		$data['approved_counts'] = $approved_counts;
		$data['all']['all'] = $all;

		// I'm just tacking this on here. However, we could improve performance
		//   by implementing the code above but I just don't have the time
		//   to mess with it.
		if ($by_time)
		{
			// Reorder the array. Is there a built in PHP function that can do this?
			$new_data = array();
			foreach ($data as $main_key => $data_array)
			{
				foreach ($data_array as $key => $counts)
				{

					if ($line_chart_data == FALSE)
					{
						foreach ($counts as $timestamp => $count)
						{
							$new_data[$main_key][$timestamp][$key] = $count;
						}
					}
					else
					{
						foreach ($counts as $timestamp => $count)
						{
							$timestamp_key = (string) ($timestamp*1000);
							if ( ! isset($new_data[$main_key][$timestamp_key]))
							{
								$new_data[$main_key][$timestamp_key] = 0;
							}
							$new_data[$main_key][$timestamp_key] += $count;
						}
					}
				}
			}

			$data = $new_data;

		}

		if ($line_chart_data == FALSE)
		{
			$data['total_reports'] = $num_reports;
			$data['total_categories'] = count($category_counts);
			$data['earliest_report_time'] = $earliest_timestamp;
			$data['latest_report_time'] = $latest_timestamp;
		}

		return $data;
	}

	/**
	 * Creates a new site in centralized stat tracker
	 * @param sitename - name of the instance
	 * @param url - base url
	 */
	public function create_site( $sitename, $url)
	{
		$stat_url = 'https://tracker.ushahidi.com/px.php?task=cs&sitename='.urlencode($sitename).'&url='.urlencode($url);

		// Ignore errors since we are error checking later

		$xml = simplexml_load_string(Stats_Model::_curl_req($stat_url));
		$stat_id = (string) $xml->id[0];
		$stat_key = (string) $xml->key[0];

		if ($stat_id > 0)
		{
			Settings_Model::save_setting('stat_id', $stat_id);
			Settings_Model::save_setting('stat_key', $stat_key);
			return $stat_id;
		}

		return false;
	}

	/**
	 * Helper function to send a cURL request
	 * @param url - URL for cURL to hit
	 */
	public function _curl_req($url)
	{
		// Make sure cURL is installed
		if ( ! function_exists('curl_exec'))
		{
			throw new Kohana_Exception('stats.cURL_not_installed');
			return false;
		}

		$curl_handle = curl_init();

		// cURL options
		$curl_options = array(
			CURLOPT_URL => $url,

			// Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
			CURLOPT_CONNECTTIMEOUT => 15,

			// Set curl to store data in variable instead of print
			CURLOPT_RETURNTRANSFER => 1,

			CURLOPT_SSL_VERIFYPEER => FALSE
		);

		curl_setopt_array($curl_handle, $curl_options);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);

		return $buffer;
	}

}
