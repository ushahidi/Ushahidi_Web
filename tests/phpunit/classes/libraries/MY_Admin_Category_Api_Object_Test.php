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
     * Database object for raw SQL queries
     * @var Database
     */
    private $db;

    /**
     * Database table prefix
     * @var string
     */
    private $table_prefix;

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
        unset ($this->api_controller, $this->db, $this->table_prefix);
    }

    /**
     * Tests category submission
     * @test
     */
    public function submitCategory()
    {

        $_POST = array(
            'action' => urlencode('add'),
            'parent_id' => urlencode('0'),
            'category_title' => urlencode('Test Category Title'),
            'category_description' => urlencode('Testing admin category'),
            'category_color' => urlencode('00FF00'),
            'task' => urlencode('category'),
        );
        
        ob_start();
        $this->api_controller->index();
        $contents = json_decode(ob_get_clean());
        $this->assertEquals(0, $contents->error->code,
            $contents->error->message);
        
        //return the id of the test category for use in other test
        
        $test_cat_id = 0;
        $test_cat_id = ORM::factory('category')->orderby('id', 
            'desc')->limit(1)->find();
        
        return $test_cat_id;
    }

    
    /**
     * Tests edit category
     * @test
     * @depends submitCategory
     */
    public function editCategory($test_cat_id)
    {
        $_POST = array(
            'action' => urlencode('edit'),
            'parent_id' => urlencode('0'),
            'category_id' => urlencode($test_cat_id),
            'category_title' => urlencode('Test Category Title Edited 2'),
            'category_description' => urlencode('Testing admin category Edited'),
            'category_color' => urlencode('00FF00'),
            'task' => urlencode('category'),
        );
        
        ob_start();
        $this->api_controller->index();
        $contents = json_decode(ob_get_clean());
        $this->assertEquals(0, $contents->error->code,
            $contents->error->message);


        return $test_cat_id;
    }

    /**
     * Tests Category deletion
     * @test
     * @depends editCategory
     */
    public function deleteCategory($cat_test_id)
    {
        
        $_POST = array(
            'action' => urlencode('delete'),
            'category_id' => urlencode($cat_test_id),
            'task' => urlencode('category'),
        );
        
        ob_start();
        $this->api_controller->index();
        $contents = json_decode(ob_get_clean());
        $this->assertEquals(0, $contents->error->code,
            $contents->error->message);
        

    }

}
