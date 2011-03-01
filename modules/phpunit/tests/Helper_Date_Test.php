<?php
/**
 * Date Helper Unit Tests
 *
 * @package Core
 * @author  Chris Bandy
 * @group core
 * @group core.helpers
 * @group core.helpers.date
 */
class Helper_Date_Test extends PHPUnit_Framework_TestCase
{
	public function offset_provider()
	{
		return array(
			array('Europe/Berlin', 'America/Chicago', '2009-01-15', 25200),
			array('Europe/Berlin', 'America/Chicago', '2009-06-15', 25200),
			array('Europe/Berlin', 'Asia/Riyadh', '2009-01-15', -7200),
			array('Europe/Berlin', 'Asia/Riyadh', '2009-06-15', -3600),
			array('Europe/London', 'GMT', '2009-01-15', 0),
			array('Europe/London', 'GMT', '2009-06-15', 3600),
		);
	}

	/**
	 * @dataProvider offset_provider
	 * @test
	 */
	public function offset($local, $remote, $when, $expected)
	{
		$result = date::offset($local, $remote, $when, $expected);
		$this->assertEquals($expected, $result);
	}
}
