<?php defined('SYSPATH') or die('No direct script access.');
/**
 * File helper class.
 * Extends built-in helper class
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     File Helper
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class file extends file_Core {

	/**
	 * Case Insensitive file_exists
	 *
	 * @param   string - Path to file
	 * @return  bool - True/False
	 */
	public static function file_exists_i($file)
	{
		$path=pathinfo($file);
		$dir=$path['dirname']!='' ? $path['dirname'] : '.' ;
		$path_array = glob($path['dirname'].'/*');
		$path_array = (is_array($path_array)) ? $path_array : array();
		return in_array(strtolower($file),array_map('strtolower',$path_array))
		 	? true : false;
	}
}