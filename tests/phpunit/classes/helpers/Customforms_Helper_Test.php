<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Unit tests for the custom forms helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Unit Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Customforms_Helper_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests the get_custom_forms method
	 *
	 * @test
	 */
	public function testGetCustomForms()
	{
		// Database instance for the test
		$db = new Database();
		
		// The record count should be the same since get_custom_forms() has no predicates
		$this->assertEquals($db->count_records('form'), customforms::get_custom_forms()->count());
	}
}
?>