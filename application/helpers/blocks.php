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
	
	private $_blocks = array();
	
	public function __construct()
	{
		// Filter::register_main_blocks
		Event::run('ushahidi_filter.register_main_blocks', $this->_blocks);
	}
	
	/**
	 * Open A Block
	 */
	public static function open()
	{
		echo "<li><div class=\"content-block\">";
	}
	
	/**
	 * Close A Block
	 */
	public static function close()
	{
		echo "</div></li>";
	}
	
	/**
	 * Block Title
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
	 * @param array $block
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
	 */
	public static function render()
	{
		// Get Active Blocks
		$settings = ORM::factory('settings', 1);
		$active_blocks = $settings->blocks;
		$active_blocks = array_filter(explode(";", $active_blocks));
		foreach ($active_blocks as $block)
		{
			$block::block();
		}
	}
	
	/**
	 * Return a sorted array of blocks
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