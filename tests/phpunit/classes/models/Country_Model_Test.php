<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Country_Model Test
 *
 * @package Ushahidi
 * @category Unit Tests
 * @author Ushahidi Team
 * @copyright (c) Ushahidi Inc 2008-2011
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Country_ModelTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Tests CountyModel::get_country_by_name
	 * @test
	 */
	public function testGetCountryByName()
	{
		// Use valid country
		$valid = Country_Model::get_country_by_name('Kenya');
		$this->assertEquals(TRUE, $valid instanceof Country_Model, sprintf('Invalid country object type (%s) returned', get_class($valid)));
		$this->assertGreaterThanOrEqual(1, $valid->id);
			
		// Use invalid country
		$invalid = Country_Model::get_country_by_name('Nairobi');
		$this->assertNull($invalid);
	}
}
?>