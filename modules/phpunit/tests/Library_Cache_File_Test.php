<?php
 /**
 * Cache Library Unit Tests - File Driver
 *
 * @package		Core
 * @subpackage	Libraries
 * @author		Kiall Mac Innes
 * @group		core
 * @group		core.libraries
 * @group		core.libraries.cache
 * @group		core.libraries.cache.file
 */
class Library_Cache_File_Test extends Library_Cache_Base_Test
{
	public function setUp()
	{
		if (($config = Kohana::config('cache.testing.file')) === NULL)
			$this->markTestSkipped('No cache.testing.file config found.');

		Kohana_Config::instance()->set('cache.testing.file.requests',1);

		$this->cache = Cache::instance('testing.file');

		parent::setUp();
	}
}