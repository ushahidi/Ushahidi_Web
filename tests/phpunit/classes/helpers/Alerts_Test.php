<?php
class Alerts_Helper_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Data provider for test_alert_code_exists()
	 *
	 * @return array
	 */
	public function providerValidate()
	{
		return array(
			array(
					'3WUXACJZ'
			)
		);
		
	}
	
	/**
	 * Tests Alert_Model->alert_code_exist() where the alert_code exists
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $data Input data to be validated
	 */
	public function test_alert_code_exists($data)
	{
		// Create instance for the Alert_Model class
		$model = new Alert_Model();
		
		// Check if the alert code exists
		$this->assertEquals(TRUE, $model->alert_code_exists($data), 'Alert Code exists');

	}

	/**
	 * Data provider for test_alert_code_not_exists()
	 *
	 * @return array
	 */
	public function providerValidateAlertCode()
	{
		return array(
			array(
					'3WUXAPRT'
			)
		);
		
	}
	
	/**
	 * Tests Alert_Model->alert_code_exist() where the alert_code is
	 * non-existent
	 *
	 * @test
	 * @dataProvider providerValidateAlertCode
	 * @param array $data Input data to be validated
	 */
	public function test_alert_code_non_exists($data)
	{
		// Create instance for the Alert_Model class
		$model = new Alert_Model();
		
		// Check if the alert code exists
		$this->assertEquals(FALSE, $model->alert_code_exists($data), 'Alert Code does not exist');

	}

}
?>
