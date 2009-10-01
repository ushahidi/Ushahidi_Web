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
 * @module     Incident Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Stats_Model extends ORM
{
	
	static function get_hit_stats($range=31)
	{		
		$settings = ORM::factory('settings', 1);
		$stat_id = $settings->stat_id;
	    
	    $stat_url = 'http://tracker.ushahidi.com/px.php?task=stats&siteid='.urlencode($stat_id).'&period=day&range='.urlencode($range);
		$response = simplexml_load_string(self::_curl_req($stat_url));
		
		$visits = '{label:"Visits",data:[';
		$uniques = '{label:"Uniques",data:[';
		$pageviews = '{label:"Pageviews",data:[';
		$i = 0;
		foreach($response->visits->result as $res) {
			$timestamp = strtotime($res['date'])*1000;
			
			if($i != 0) {
				$visits .= ',';
				$uniques .= ',';
				$pageviews .= ',';
			}
			
			$visits .= '['.$timestamp.',';
			$uniques .= '['.$timestamp.',';
			$pageviews .= '['.$timestamp.',';
			
			if(isset($res->nb_visits)){ 
				$visits .= $res->nb_visits;
			}else{
				$visits .= '0';
			}
			
			if(isset($res->nb_uniq_visitors)){ 
				$uniques .= $res->nb_uniq_visitors;
			}else{
				$uniques .= '0';
			}
			
			if(isset($res->nb_actions)){ 
				$pageviews .= $res->nb_actions;
			}else{
				$pageviews .= '0';
			}
			
			$visits .= ']';
			$uniques .= ']';
			$pageviews .= ']';
			
			$i++;
		}
		$visits .= ']}';
		$uniques .= ']}';
		$pageviews .= ']}';
		
		$data = "[$visits,$uniques,$pageviews]";
		
		return $data;
	}
	
	static function get_hit_countries($range=31)
	{
		$settings = ORM::factory('settings', 1);
		$stat_id = $settings->stat_id;
	    
	    $stat_url = 'http://tracker.ushahidi.com/px.php?task=stats&siteid='.urlencode($stat_id).'&period=day&range='.urlencode($range);
		$response = simplexml_load_string(self::_curl_req($stat_url));
		
		$data = array();
		foreach($response->countries->result as $res) {
			$date = (string)$res['date'];
			foreach($res->row as $row){
				$code = (string)$row->code;
				$data[$date][$code]['label'] = (string)$row->label;
				$data[$date][$code]['uniques'] = (string)$row->nb_uniq_visitors;
				$data[$date][$code]['logo'] = 'http://tracker.ushahidi.com/piwik/'.(string)$row->logo;
			}
		}
		
		return $data;
		
	}
	
	
	
	
	/**
	 * Helper function to send a cURL request
	 * @param url - URL for cURL to hit
	 */
	public function _curl_req( $url )
	{
		// Make sure cURL is installed
		if (!function_exists('curl_exec')) {
			throw new Kohana_Exception('stats.cURL_not_installed');
			return false;
		}
		
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); //Set curl to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		return $buffer;
	}

}
