<?php
/**
 * Unit tests for the incident model
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
class Incident_Model_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * Data provider for the testGetNeighbouring incidents unit test
	 * @dataProvider
	 */
	public function providerTestGetNeighbouringIncidents()
	{
		return array(array(
			testutils::get_random_id('incident', 'WHERE incident_active = 1'),
			10
		));
	}
	
	/**
	 * Tests Incident_Model::get_neigbouring_incidents
	 * @test
	 * @dataProvider providerTestGetNeighbouringIncidents
	 */
	public function testGetNeighbouringIncidents($incident_id, $num_neighbours)
	{
		// Get the neighbouring incidents
		$neighbours = Incident_Model::get_neighbouring_incidents($incident_id, FALSE, 0, $num_neighbours);
	
		if (empty($neighbours))
		{
			$this->markTestSkipped('The incident table is empty.');
		}
		else
		{
			// Check if the no. of returned incidents matches the no. of neighbours specified in @param $neighbours
			$this->assertEquals($num_neighbours, $neighbours->count());
		}
	}

	/**
	 * Tests Incident_Model::is_valid_incident
	 * @test
	 */
	public function testIsValidIncident()
	{
		// Get any incident
		$random_incident = testutils::get_random_id('incident');
		$inactive_incident = testutils::get_random_id('incident', 'WHERE incident_active = 0');
		$active_incident = testutils::get_random_id('incident', 'WHERE incident_active = 1');
		
		//Test to see if there are data in the incident table to test with.
		if (empty($random_incident))
		{
			$this->markTestSkipped('The incident table is empty.');
		}
		elseif (empty($inactive_incident))
		{
			$this->markTestSkipped('No inactive incidents in incident table.');
		}
		elseif (empty($active_incident))
		{
			$this->markTestSkipped('No active incidents in incident table.');
		}
		else
		{
			$this->assertEquals(TRUE, Incident_Model::is_valid_incident($random_incident, FALSE));

			// Get inactive incident
			$inactive_incident = testutils::get_random_id('incident', 'WHERE incident_active = 0');
			// Check fails with default args and explicitly limit to active only
			$this->assertEquals(FALSE, Incident_Model::is_valid_incident($inactive_incident));
			$this->assertEquals(FALSE, Incident_Model::is_valid_incident($inactive_incident, TRUE));
			// Check success when including inactive incidents
			$this->assertEquals(TRUE, Incident_Model::is_valid_incident($inactive_incident, FALSE));
			
			// Get active incident
			$active_incident = testutils::get_random_id('incident', 'WHERE incident_active = 1');
			// Check success with default args and explicitly limit to active only
			$this->assertEquals(TRUE, Incident_Model::is_valid_incident($active_incident));
			$this->assertEquals(TRUE, Incident_Model::is_valid_incident($active_incident, TRUE));
			
			// Null incident value
			$this->assertEquals(FALSE, Incident_Model::is_valid_incident(NULL));
			
			// Non numeric incident value
			$this->assertEquals(FALSE, Incident_Model::is_valid_incident('0.999'));
		}
	}
}
?>
