<?php
/**
 * Num Helper Unit Tests
 *
 * @package	Core
 * @author	 Kiall Mac Innes
 * @group core
 * @group core.helpers
 * @group core.helpers.num
 */
class Helper_Num_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * DataProvider for the num::round() test
	 */
	public function round_provider()
	{
		return array(
		  array(23.4545445, 5, 25),
		  array(-23.4545445, 5, -25),
		  array(23.4545445, 1, 23),
		  array(-23.4545445, 1, -23),
		  array(12, 5, 10),
		  array(-12, 5, -10),
		  array(13, 3, 12),
		  array(-13, 3, -12),
		);
	}
	
	/**
	 * Tests the num::round() function.
	 * @dataProvider round_provider
	 * @group core.helpers.num.round
	 * @test
	 */
	public function round($input_number, $input_nearest, $expected_result)
	{
		$result = num::round($input_number, $input_nearest);
		$this->assertEquals($expected_result, $result);
	}
}