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
            'REQUEST_URI' => url::base().'api/?task=category',
        ));

        // Category fields to be submitted
        $_POST = array
        (

        );

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
    public function testSubmitCategory()
    {
        // * count the number of existing categories in the db
        // * keep a record of the total
        // * submit dummy category
        // * count the number of categories again for new total
        // * compare new total with old total
        // * if is new total is greater than old total by 1, then 
        // * new category was submitted.
        $old_total = ORM::factory('category')->count_all();

        $_POST = array(
            'task' => 'category',
            'action' => 'add',
            'parent_id' => 0,
            'category_description' => 'Testing admin category',
            'category_color' => '0FFFF',
        );
        
        ob_start();
        $this->api_controller->index();
        $contents = json_decode(ob_get_clean());

        $this->assertEquals(0, $contents->error->code);

        $new_total = ORM::factory('category')->count_all();

        $this->assertEquals(TRUE, $new_total > $old_total,
            'Could not add test category ');

        //Clean up
        ORM::factory()->where('category_id',0)->delete_all();
    }

    /**
     * Tests Category deletion
     * @test
     */
    public function testDeleteCategory()
    {
        /*$_POST = array(
            'task' => 'category',
            'action' => 'delete',
            'category_id' => 2,
            'parent_id' => 1,
            'category_description' => 'Testing admin category',
            'category_color' => '0FFFF',
        );
        
        ob_start();
        $this->api_controller->index();
        $contents = json_decode(ob_get_clean());

        $this->assertEquals('0', $content->error->code);*/


    }

    /**
     * Tests edit category
     * @test
     */
    public function testEditCategory()
    {
        // * count the number of existing categories in the db
        // * keep a record of the total
        // * submit dummy category
        // * count the number of categories again for new total
        // * compare new total with old total
        // * if is new total is greater than old total by 1, then 
        // * new category was submitted.
        /* $_POST = array(
            'task' => 'category',
            'action' => 'edit',
            'category_id' => 2,
            'parent_id' => 1,
            'category_description' => 'Testing admin category',
            'category_color' => '0FFFF',
        );
        
        ob_start();
        $this->api_controller->index();
        $contents = json_decode(ob_get_clean());

        $this->assertEquals('0', $content->error->code);

        //Clean up*/

    }

}
