<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Unit tests for the category model
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
class Category_Model_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * Data provider for test validate
	 * @dataProvider
	 */
	public function providerValidate()
	{
		return array(
			array(
				array(
					'category_title' => 'Test title',
					'category_description' => 'Test title category',
					'category_color' => 'f3f3f3',
					'parent_id' => 0
				), // Valid category data
				
				array(
					'category_title' => 'Test title',
					'category_description' => 'Test title category',
					'category_color' => NULL,
					'parent_id' => 0
				), // Invalid category data
				
				FALSE // Do not save record when validation succeeds
			)
		);
	}
	
	/**
	 * Tests the validate() method in Category_Model
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $valid Valid data for the test
	 * @param array $invalid Invalid data for the test
	 * @param array $save Saves the record when validation succeeds
	 */
	public function testValidate($valid, $invalid, $save)
	{
		// Model instance for the test
		$category = new Category_Model();
		
		// Valid data test
		$this->assertEquals(TRUE, $category->validate($valid, $save));
		
		// Invalid data test
		$this->assertEquals(FALSE, $category->validate($invalid, $save));
	}
	
	/**
	 * Data provider for testIsValidCategory
	 *
	 * @dataProvider
	 */
	public function providerIsValidCategory()
	{
		return array(
			array(
				// Valid category id
				testutils::get_random_id('category'),
				
				// Invalid category id
				'-1'
			));
	}
	
	/**
	 * Test the is_valid_category method in Category_Model using a 
	 * valid category id
	 *
	 * @test
	 * @dataProvider providerIsValidCategory
	 * @param int $valid_category_id  ID of a category exisitng in the database
	 * @param int $invalid_category_id ID of a non-existent/non-numeric category
	 */
	public function testIsValidCategory($valid_category_id, $invalid_category_id)
	{
		// Test existence of valid category
		$this->assertEquals(TRUE, Category_Model::is_valid_category($valid_category_id));
		
		// Assert invalidity and non-existence of invalid category
		$this->assertEquals(FALSE, Category_Model::is_valid_category($invalid_category_id));
	}

}
?>