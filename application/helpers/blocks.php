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
	 */
	public static function register($name =  NULL, $description = NULL)
	{
		if ($name)
		{
			$blocks = Kohana::config("blocks.main");
			if ( ! array_key_exists($name, $blocks))
			{
				$blocks[$name] = $description;
			}
			Kohana::config_set("blocks.main", $blocks);
		}
	}
}