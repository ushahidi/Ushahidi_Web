<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Unit test for the cateory model
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Test
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
				),
				FALSE
			)
		);
	}
	
	/**
	 * Tests the validate() method in Category_Model
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $data Values to check
	 * @param array $save Saves the record when validation succeeds
	 */
	public function testValidate($data, $save)
	{
		// Model instance for the test
		$category = new Category_Model();
		
		// Return true if the values in $data pass validation tests
		$this->assertEquals(TRUE, $category->validate($data, $save));
	}
	
	/**
	 * Data provider for testIsValidCategory
	 *
	 * @dataProvider
	 */
	public function providerIsValidCategory()
	{
		return array(array(1));
	}
	
	/**
	 * Test the is_valid_category method in Category_Model using a 
	 * valid category id
	 *
	 * @test
	 * @dataProvider providerIsValidCategory
	 * @param int $category_id Database id of the category to be validated
	 */
	public function testIsValidCategory($category_id)
	{
		// Check if the specifed category exists in the database
		$this->assertEquals(TRUE, Category_Model::is_valid_category($category_id));
	}
	
	/**
	 * Data provider for testInvalidCategory 
	 *
	 * @dataProvider
	 */
	public function providerIsInvalidCategory()
	{
		return array(array('-1'));
	}
	
	/**
	 * Tests the is_valid_category method in Category_Model using an invalid
	 * category id
	 *
	 * @test
	 * @dataProvider  providerIsInvalidCategory
	 */
	public function testIsInvalidCategory($category_id)
	{
		$this->assertEquals(FALSE, Category_Model::is_valid_category($category_id));
	}
}
?>