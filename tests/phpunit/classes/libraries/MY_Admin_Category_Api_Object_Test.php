<?php defined('SYSPATH') or die('No direct script access');

/**
 * Unit tests for the Admin Category API
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

/**
 * 
 * @backupGlobals disabled
 */
class Admin_Category_Api_Object_Test extends PHPUnit_Framework_TestCase {

    /**
     * API controller object to run the tests
     * @var Api_Controller
     */
    private $api_controller;

    /**
     * Initialize objects
     */
    protected function setUp()
    {
        $_SERVER = array_merge($_SERVER, array(
            'REQUEST_METHOD' => 'POST',
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
            'REQUEST_URI' => url::base().'api',
        ));

        // Instantiate the API controller
        $this->api_controller = new Api_Controller();
    }

    /**
     * Unset objects and variables aka Garbage collection
     */
    protected function tearDown()
    {
        unset ($this->api_controller);
    }

    /**
     * Tests category submission
     * @test
     */
    public function submitCategory()
    {
		$category_id = 0;

		$_POST = array(
			'action' => 'add',
			'parent_id' => '0',
			'category_title' => 'Test Category Title',
			'category_description' => 'Testing admin category',
			'category_color' => '00FF00',
			'task' => 'category',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);

		// Return the id of the test category for use in other test
		$category_id = ORM::factory('category')->orderby('id',  'desc')->limit(1)->find();

		return $category_id;
    }

    
	/**
	 * Tests edit category
	 * @test
	 * @depends submitCategory
	 */
	public function editCategory($category_id)
	{
		$_POST = array(
			'action' => 'edit',
			'parent_id' => '0',
			'category_id' => $category_id,
			'category_title' => 'Test Category Title Edited 2',
			'category_description' => 'Testing admin category Edited',
			'category_color' => '00FF00',
			'task' => 'category',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals(0, $contents->error->code, $contents->error->message);

		return $category_id;
	}

	/**
	 * Tests Category deletion
	 * @test
	 * @depends editCategory
	 */
	public function deleteCategory($category_id)
	{
		$_POST = array(
			'action' => 'delete',
			'category_id' => $category_id,
			'task' => 'category',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);
	}
}