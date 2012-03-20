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
				// Valid category data
				array(
					'category_title' => 'Test title',
					'category_description' => 'Test title category',
					'category_color' => 'f3f3f3',
					'parent_id' => 0
				), 
				
				// Invalid category data set 1
				array(
					'category_title' => 'Test title',
					'category_description' => 'Test title category',
					'category_color' => NULL,
					'parent_id' => 0
				), 
				
				// Invalid category data data set 2 - special parent category
				array(
					'category_title' => 'Test title',
					'category_description' => 'Test title category',
					'category_color' => 'F3F3F3',
					'parent_id' => testutils::get_random_id('category', 'WHERE category_trusted = 1')
				),
				
				// Invalid category data set 3 - special subcategory
				array(
					'category_title' => 'Edited Cat',
					'category_description' => 'Editing a category',
					'category_color' => 'f3f3f3',
					'parent_id' => testutils::get_random_id('category')
				),

				// Do not save record when validation succeeds
				FALSE 
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
	public function testValidate($valid, $invalid, $invalid2, $specialsub, $save)
	{
		// Model instance for the test on a new category
		$category = new Category_Model();
		
		// Model instance for the test on an existing special category
		$special_sub = new Category_Model(testutils::get_random_id('category', 'WHERE category_trusted = 1'));
		
		// Valid data test
		$this->assertEquals(TRUE, $category->validate($valid, $save));
		
		// Invalid data test 1
		$this->assertEquals(FALSE, $category->validate($invalid, $save));
		
		// Invalid data test 2
		$this->assertEquals(FALSE, $category->validate($invalid2, $save));
		
		// Invalid data test 3
		$this->assertEquals(FALSE , $special_sub->validate($specialsub, $save));
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