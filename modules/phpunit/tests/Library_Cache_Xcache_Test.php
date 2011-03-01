<?php
 /**
 * Cache Library Unit Tests - Xcache Driver
 *
 * @package		Core
 * @subpackage	Libraries
 * @author		Kiall Mac Innes
 * @group		core
 * @group		core.libraries
 * @group		core.libraries.cache
 * @group		core.libraries.cache.xcache
 */
class Library_Cache_Xcache_Test extends Library_Cache_Base_Test
{
	public function setUp()
	{
		if (!extension_loaded('xcache'))
			$this->markTestSkipped('The XCache extension is not loaded');
			
		if (($config = Kohana::config('cache.testing.xcache')) === NULL)
			$this->markTestSkipped('No cache.testing.xcache config found.');
		
		if (Kohana::config('cache_xcache') === NULL)
			$this->markTestSkipped('No cache_xcache config found.');
			
		Kohana_Config::instance()->set('cache.testing.xcache.requests',1);
		
		$this->cache = Cache::instance('testing.xcache');
		
		parent::setUp();
	}
	
	/**
	 * DataProvider for the Cache::set() and Cache::get() tests
	 */
	public function set_and_get_provider()
	{
		$object = new stdClass();
		$object->name = 'Bla';
		
		return array(
		  array('id1', 'value1', NULL, 5),
		  array('id2', 'value2', NULL, 5),
		  array('id3', 3434, NULL, 5),
		  array('id4', 45.34, NULL, 5),
		  array('id5', $object, NULL, 5),
		  array('id6', array('one','two'), NULL, 5),
		);
	}
	
	/**
	 * Tests the Cache::delete_tag() function.
	 * @dataProvider set_and_get_provider
	 * @group core.libraries.cache.delete_tag
	 * @test
	 */
	public function delete_tag($input_id, $input_value, $input_tags, $input_lifetime)
	{
		
		if (count($input_tags) < 1)
			$this->markTestSkipped('Tags are not supported by XCache');
	}
}