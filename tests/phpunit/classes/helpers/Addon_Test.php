<?php
class Addon_Helper_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Executed when this test case is initialized
	 */
	public function setUp()
	{

	}
	
	/**
	 * Tear down operation - Executed when the test is complete
	 */
	public function tearDown()
	{
	}
	
	/**
	 * Tests addon::get_addons()
	 *
	 * @test
	 */
	public function test_get_addons()
	{
		$themes = addon::get_addons('theme');
		$plugins = addon::get_addons('plugin');
		
		// Check for bundled plugins
		$this->assertArrayHasKey('smssync', $plugins);
		$this->assertArrayHasKey('sharing', $plugins);
		
		// Check for bundled themes
		$this->assertArrayHasKey('default', $themes);
		$this->assertArrayHasKey('unicorn', $themes);
		
		// Check for themes not in plugins and vice versa
		$this->assertArrayNotHasKey('default', $plugins, 'Theme "default" returned in get_addons(\'plugin\') array');
		$this->assertArrayNotHasKey('smssync', $themes, 'Plugin "smssync" returned in get_addons(\'theme\') array');
		
		// @todo test includeMeta
		$themes_nometa = addon::get_addons('theme', FALSE);
		$themes_meta = addon::get_addons('theme', TRUE);
		$this->assertNotEmpty($themes_meta['default'], 'get_addons(\'theme\', TRUE) not returning meta array');
		$this->assertEmpty($themes_nometa['default'], 'get_addons(\'theme\', FALSE) not returning empty meta array');
		
	}
	
	
	/**
	 * Tests addon::meta_data()
	 *
	 * @test
	 */
	public function test_meta_data()
	{
		$default = addon::meta_data('default','theme');
		$smssync = addon::meta_data('smssync','plugin');
		
		$this->assertNotEmpty($default['Theme Name']);
		$this->assertNotEmpty($smssync['name']);
	}

}
?>
