<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Reputation Score helper class
 * 
 *
 * @package	   Reputation
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */

class reputation_Core {
	
	/**
	 * Calculate Total Reputation Score for User
	 * @param int User ID
	 * @return int reputation score
	 */
	public static function calculate($user_id)	
	{
		$subdomain = '';
		if (substr_count($_SERVER["HTTP_HOST"],'.') > 1 AND Kohana::config('config.enable_mhi') == TRUE)
		{
			$subdomain = substr($_SERVER["HTTP_HOST"],0,strpos($_SERVER["HTTP_HOST"],'.'));
		}
		
		$cache = Cache::instance();
		// This is kind of a heavy query, so we'll use a 10 minute cache for $totals
		$total = $cache->get($subdomain.'_reputation');

		if ($total == NULL)
		{ // Cache is Empty so Re-Cache
			$total = 0;
			$upvoted_reports = 10;
			$approved_reports = 15;
			$verified_reports = 20;
			$upvoted_comments = 5;
			$downvoted_reports = 2;
			$downvoted_comments = 1;

			// Get Reports Approved Verified
			$reports = ORM::factory("incident")
						->where("user_id", $user_id)
						->find_all();
						
			foreach ($reports as $report)
			{
				if ($report->incident_active)
				{
					$total += $approved_reports;
				}

				if ($report->incident_verified)
				{
					$total += $verified_reports;
				}
			}

			// Get Totals on [My] Reports that have been voted on
			$ratings = ORM::factory("rating")
						->join("incident", "incident.id", "rating.incident_id")
						->join("users", "users.id", "incident.user_id")
						->where("users.id", $user_id)
						->find_all();
			foreach ($ratings as $rating)
			{
				if ($rating->rating > 0)
				{ // Upvote
					$total += ( $rating->rating * $upvoted_reports );
				}
				elseif ($rating->rating < 0)
				{ // Downvote
					$total += ( $rating->rating * $downvoted_reports );
				}
			}

			// Get Totals on [My] Comments that have been voted on
			$ratings = ORM::factory("rating")
				->join("comment", "comment.id", "rating.comment_id")
				->join("users", "users.id", "comment.user_id")
				->where("users.id", $user_id)
				->find_all();
				
			foreach ($ratings as $rating)
			{
				if ($rating->rating > 0)
				{ // Upvote
					$total += ( $rating->rating * $upvoted_comments );
				}
				elseif ($rating->rating < 0)
				{ // Downvote
					$total += ( $rating->rating * $downvoted_comments );
				}
			}

			$cache->set($subdomain.'_reputation', $total, array('reputation'), 600); // 10 Minutes
		}

		return $total;
	}
}