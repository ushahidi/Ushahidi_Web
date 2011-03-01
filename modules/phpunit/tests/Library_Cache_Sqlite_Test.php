<?php
 /**
 * Cache Library Unit Tests - Sqlite Driver
 *
 * @package		Core
 * @subpackage	Libraries
 * @author		Kiall Mac Innes
 * @group		core
 * @group		core.libraries
 * @group		core.libraries.cache
 * @group		core.libraries.cache.sqlite
 */
class Library_Cache_Sqlite_Test extends Library_Cache_Base_Test
{
    public function setUp()
    {
        if (!extension_loaded('sqlite'))
            $this->markTestSkipped('The Sqlite extension is not loaded');

        if (($config = Kohana::config('cache.testing.sqlite')) === NULL)
            $this->markTestSkipped('No cache.testing.sqlite config found.');

        if (Kohana::config('cache_sqlite') === NULL)
            $this->markTestSkipped('No cache_sqlite config found.');

        Kohana_Config::instance()->set('cache.testing.sqlite.requests',1);

        $this->cache = Cache::instance('testing.sqlite');

        parent::setUp();
    }
}
