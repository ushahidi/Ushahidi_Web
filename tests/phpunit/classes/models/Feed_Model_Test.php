<?php
/**
 * Unit tests for the feed model
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

class Feed_Model_Test extends PHPUnit_Framework_TestCase {
	
	public function setUp()
	{
		// Create an instance of Feed_Model
		$this->feed_model = new Feed_Model();
	}
	
	public function tearDown()
	{
		// Garbage collection
		unset ($this->feed_model);
	}
	
	/**
	 * Tests Feed_Model::is_valid_feed
	 * @test
	 */
	public function testIsValidFeed()
	{
		if (ORM::factory('feed')->count_all() == 0)
		{
			$this->markTestSkipped('There are no records in the feeds table');
		}
		
		// Test with a valid feed id
		$random_feed_id = testutils::get_random_id('feed');
		
		$this->assertEquals(TRUE, Feed_Model::is_valid_feed($random_feed_id));
		
		// Test with an invalid feed id
		$this->assertEquals(FALSE, Feed_Model::is_valid_feed('90.9999'));
	}
	
	
	/**
	 * Dataprovider for testValidate
	 * @dataProvider
	 */
	public function providerTestValidate()
	{
		return array(
			array(
				// Valid feed data
				array(
					'feed_name' => 'Valid Test Feed Name',
					'feed_url' => 'http://http://xkcd.com/rss.xml'
				),
				// Invalid feed data
				array(
					'feed_name' => 'Invalid Test Feed Name',
					'feed_url' => 'xkcd.com/rss.xml'
				)
			)
		);
	}
	
	/**
	 * Tests the validate method in Feed_Model
	 * @test
	 * @dataProvider providerTestValidate
	 */
	public function testValidate($valid_feed_data, $invalid_feed_data)
	{
		$this->assertEquals(TRUE, $this->feed_model->validate($valid_feed_data));
		$this->assertEquals(FALSE, $this->feed_model->validate($invalid_feed_data));
	}
}
?>