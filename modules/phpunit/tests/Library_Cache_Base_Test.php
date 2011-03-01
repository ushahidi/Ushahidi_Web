<?php
/**
 * Cache Library Unit Tests
 *
 * @package		Core
 * @subpackage	Libraries
 * @author		Kiall Mac Innes
 */
abstract class Library_Cache_Base_Test extends PHPUnit_Framework_TestCase
{
	protected $cache;
	
	protected $config;
	
	public function setUp()
	{
		$this->cache->delete_all();
	}
		
	/**
	 * DataProvider for the Cache::set() and Cache::get() tests
	 */
	public function set_and_get_provider()
	{
		$object = new stdClass();
		$object->name = 'Bla';
		
		return array(
		  array('id1', 'value1', array('tag1','tag2'), 5),
		  array('id2', 'value2', array('tag1','tag2'), 5),
		  array('id3', 3434, array('tag1','tag2'), 5),
		  array('id4', 45.34, array('tag1','tag2'), 5),
		  array('id5', $object, array('tag1','tag2'), 5),
		  array('id6', array('one','two'), array('tag1','tag2'), 5),
		);
	}
	
	/**
	 * Tests the Cache::set() and Cache::get() functions.
	 * @dataProvider set_and_get_provider
	 * @group core.libraries.cache.set
	 * @group core.libraries.cache.get
	 * @test
	 */
	public function set_and_get($input_id, $input_value, $input_tags, $input_lifetime)
	{
		// Set the cache item..
		$result = $this->cache->set($input_id, $input_value, $input_tags, $input_lifetime);
		$this->assertTrue($result);
		
		// Get the item via ->get()
		$result = $this->cache->get($input_id);
		$this->assertEquals($input_value, $result);
		
		// Check if we have any tags..
		if (is_array($input_tags) AND count($input_tags) > 0)
		{
			foreach ($input_tags as $tag)
			{
				// Get the item via ->find()
				$result = $this->cache->find($tag);
				$this->assertEquals($input_value, $result[$input_id]);
			}
		}
	}
	
	/**
	 * Tests Cache::set() a little more
	 * @dataProvider set_and_get_provider
	 * @group core.libraries.cache.set
	 * @test
	 */
	public function set($input_id, $input_value, $input_tags, $input_lifetime)
	{
		// Set the cache item with a NULL expiry..
		$result = $this->cache->set($input_id, $input_value, $input_tags, NULL);
		$this->assertTrue($result);
		$result = $this->cache->set($input_id, $input_value, $input_tags);
		$this->assertTrue($result);
		
		// Set the cache item with a NULL expiry and NULL tags..
		$result = $this->cache->set($input_id, $input_value, NULL, NULL);
		$this->assertTrue($result);
		$result = $this->cache->set($input_id, $input_value);
		$this->assertTrue($result);
		
		// Set the cache item with a NULL Value..
		$result = $this->cache->set($input_id, NULL);
		$this->assertTrue($result);
	}
	
	/**
	 * Tests Cache expiry is working correctly.
	 * @dataProvider set_and_get_provider
	 * @group core.libraries.cache.set
	 * @group core.libraries.cache.get
	 * @test
	 */
	public function expiry($input_id, $input_value, $input_tags, $input_lifetime)
	{
		// Set the cache item.. Force lifetime to be 1 second.
		$result = $this->cache->set($input_id, $input_value, $input_tags, 1);
		$this->assertTrue($result);
		
		// Wait for the item to expire.
		sleep(2);
		
		$result = $this->cache->get($input_id);
		
		$this->assertNull($result);
		
		// "Test" the delete_expired() method - since we cant call 
		// it directly, we jerryrig our way into it ;)
		
		$result = $this->cache->set($input_id, $input_value, $input_tags, 1);
		$this->assertTrue($result);
		
		$result = $this->cache->set($input_id.'_two', $input_value, $input_tags, 10);
		$this->assertTrue($result);
		
		// Wait for the item to expire.
		sleep(2);
		
		$result = $this->cache->get($input_id.'_two');
	}
	
	/**
	 * Tests the Cache::delete() function.
	 * @dataProvider set_and_get_provider
	 * @group core.libraries.cache.delete
	 * @test
	 */
	public function delete($input_id, $input_value, $input_tags, $input_lifetime)
	{
		// Set the cache item.. Force lifetime to be 1 minute.
		$result = $this->cache->set($input_id, $input_value, $input_tags, 60);
		$this->assertTrue($result);
		
		$result = $this->cache->delete($input_id);
		$this->assertTrue($result);
		
		$result = $this->cache->delete($input_id);
		$this->assertFalse($result);
		
		$result = $this->cache->get($input_id);
		$this->assertNull($result);
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
			$this->markTestSkipped('No Tags Specified');
		
		// Set the cache item.. Force lifetime to be 1 minute.
		$result = $this->cache->set($input_id.'_one', $input_value, $input_tags, 60);
		$this->assertTrue($result);
		
		$result = $this->cache->set($input_id.'_two', $input_value, $input_tags, 60);
		$this->assertTrue($result);
		
		$result = $this->cache->set($input_id.'_three', $input_value, $input_tags, 60);
		$this->assertTrue($result);
		
		$result = $this->cache->delete_tag($input_tags[0]);
		$this->assertTrue($result);
		
		$result = $this->cache->delete_tag($input_tags[0]);
		$this->assertFalse($result);
		
		$result = $this->cache->get($input_id.'_one');
		$this->assertNull($result);
		
		$result = $this->cache->get($input_id.'_two');
		$this->assertNull($result);
		
		$result = $this->cache->get($input_id.'_three');
		$this->assertNull($result);
	}
}