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
		$total = 0;
		$upvoted_reports = 10;
		$upvoted_comments = 5;
		$downvoted_reports = 2;
		$downvoted_comments = 1;
		
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
		
		return $total;
	}
}