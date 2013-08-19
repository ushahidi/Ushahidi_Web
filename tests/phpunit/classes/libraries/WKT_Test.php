<?php defined('SYSPATH') or die('No direct script access.');
/**
 * WKT library Unit Test
 *
 * @author 		Ushahidi Team
 * @package 	Ushahidi
 * @category 	Unit Tests
 * @copyright 	(c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license 	For license information, see License.txt
 */
class WKT_Test extends PHPUnit_Framework_TestCase {
	

	public function testFlatten()
	{
		$flatten = array(array(1,"cheese"));
		$this->assertEquals(array(1,"cheese"), WKT::flatten($flatten));
		

		$flatten = array(array(1,"cheese"), array(3,4,"chocolate",6), "bermuda");
		$this->assertEquals(array(1,"cheese",3,4,"chocolate",6,"bermuda"), WKT::flatten($flatten));
	}
	
	public function testCollapsePoints()
	{
		//simple array
		$test = array(1,2);
		WKT::collapse_points($test, 0);
		$this->assertEquals("2,1", $test);

		//multidimensional array
		$test = array(array(1,3),array(2,4));
		WKT::collapse_points($test, 0);
		$this->assertEquals(array("3,1", "4,2"), $test);	

	}
}
	