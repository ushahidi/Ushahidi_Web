<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Blocks helper class.
 *
 * @package    Admin
 * @author     Ushahidi Team
 * @copyright  (c) 2011 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class blocks_Core {
	
	/**
	 * Open A Block
	 *
	 * @return string
	 */
	public static function open()
	{
		echo "<li><div class=\"content-block\">";
	}
	
	/**
	 * Close A Block
	 *
	 * @return string
	 */
	public static function close()
	{
		echo "</div></li>";
	}
	
	/**
	 * Block Title
	 *
	 * @return string
	 */
	public static function title($title = NULL)
	{
		if ($title)
		{
			echo "<h5>$title</h5>";
		}
	}
	
	/**
	 * Register A Block
	 *
	 * @param array $block an array with classname, name and description
	 */
	public static function register($block = array())
	{
		// global variable that contains all the blocks
		$blocks = Kohana::config("settings.blocks");
		if ( ! is_array($blocks) )
		{
			$blocks = array();
		}
		
		if ( is_array($block) AND 
			array_key_exists("classname", $block) AND 
			array_key_exists("name", $block) AND 
			array_key_exists("description", $block) )
		{
			if ( ! array_key_exists($block["classname"], $blocks))
			{
				$blocks[$block["classname"]] = array(
					"name" => $block["name"],
					"description" => $block["description"]
				);
			}
		}
		asort($blocks);
		Kohana::config_set("settings.blocks", $blocks);
	}
	
	/**
	 * Render all the active blocks
	 *
	 * @return string block html
	 */	
	public static function render()
	{
		// Get Active Blocks
		$settings = ORM::factory('settings', 1);
		$active_blocks = $settings->blocks;
		$active_blocks = array_filter(explode("|", $active_blocks));
		foreach ($active_blocks as $block)
		{
			$block = new $block();
			$block->block();
		}
	}
	
	/**
	 * Sort Active and Non-Active Blocks
	 * 
	 * @param array $active array of active blocks
	 * @param array $registered array of all blocks
	 * @return array merged and sorted array of active and inactive blocks
	 */
	public static function sort($active = array(), $registered = array())
	{
		// Remove Empty Keys
		$active = array_filter($active);
		$registered = array_filter($registered);
		
		$sorted_array = array();
		$sorted_array = array_intersect($active, $registered);
		return array_merge($sorted_array, array_diff($registered, $sorted_array));
	}
}