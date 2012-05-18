<?php defined('SYSPATH') or die('No direct script access');

/**
 * Unit tests for the Admin Reports API
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
 * @backupGlobals disabled
 */
class Admin_Reports_Api_Object_Test extends PHPUnit_Framework_TestCase {

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
	 * Test report submission
	 * @test
	 */
	public function submitReport()
	{
		$report_id = 0;

		$_POST = array(
			'task' => 'report',
			'incident_title' => 'Test Sample Report Title',
			'incident_description' => 'Test Sampe Report Description',
			'incident_date' => '03/18/2011',
			'incident_hour' => '10', 
			'incident_minute' => '10',
			'incident_ampm' => 'pm',
			'incident_category' => '73,41	',
			'latitude' => -1.28730007,
			'longitude' => 36.82145118200820,
			'location_name' => 'Accra',
			'person_first' => 'Henry Addo',
			'person_last' => 'Addo',
			'person_email' => 'henry@ushahidi.com',
			'incident_active' => 1,
			'incident_verified' => 1,
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);

		// Return the id of the test report for use in other test
		$report_id = ORM::factory('incident')->orderby('id', 'desc')->limit(1)->find();

		return $report_id;
	}

	/**
	 * Tests retrieval of unapproved reports
	 * @test
	 */
	public function retrieveUnapprovedReports()
	{
		$_POST = array(
			'by' => 'unapproved',
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);
	}


	/**
	 * Test report approval.
	 * @test
	 * @depends submitReport
	 */
	public function approveReport($report_id)
	{
		$_POST = array(
			'action' => 'approve',
			'incident_id' => $report_id,
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);

		return $report_id;
	}

	/**
	 * Tests retrieval of approved reports
	 * @test
	 */
	public function retrieveApproveReports()
	{
		$_POST = array(
			'by' => 'approved',
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);
	}

	/**
	 * Tests retrieval of unverified reports
	 * @test
	 */
	public function retrieveUnverifiedReports()
	{
		$_POST = array(
			'by' => 'unverified',
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);
	}

	/**
	 * Test report verification.
	 * @test 
	 * @depends approveReport
	 */
	public function verifyReport($report_id)
	{
		$_POST = array(
			'action' => 'verify',
			'incident_id' => $report_id,
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);

		return $report_id;
	}

	/**
	 * Tests retrieval of verified reports
	 * @test
	 */
	public function retrieveVerifiedReports()
	{
		$_POST = array(
			'by' => 'verified',
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);
	}     
            
	/**
	 * Test editing of report.
	 * @test 
	 * @depends verifyReport
	 */
	public function editReport($report_id)
	{
		$_POST = array(
			'task' => 'reports',
			'action' => 'edit',
			'incident_id' => $report_id,
			'incident_title' => 'Hello Ushahidi Edited',
			'incident_description' => 'Description Edited',
			'incident_date' => '03/18/2009',
			'incident_hour' => '10', 
			'incident_minute' => '10',
			'incident_ampm' => 'pm',
			'incident_category' => '73,41',
			'latitude' => -1.28730007,
			'longitude' => 36.82145118200820,
			'location_id'=> 2,
			'location_name' => 'accra',
			'person_first' => 'Henry Addo',
			'person_last' => 'Addo',
			'incident_active' => 1,
			'incident_verified' => 1,
			'person_email' => 'henry@ushahidi.com',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);

		return $report_id;
	}

	/**
	 * Test report deletion.
	 * @test 
	 * @depends editReport
	 */
	public function deleteReport($report_id)
	{
		$_POST = array(
			'form_auth_token' => csrf::token(),
			'action' => 'delete',
			'incident_id' => $report_id,
			'task' => 'reports',
		);

		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		$this->assertEquals(0, $contents->error->code, $contents->error->message);
	}

}
