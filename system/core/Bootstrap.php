<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana process control file, loaded by the front controller.
 * 
 * $Id: Bootstrap.php 3918 2009-01-21 03:15:53Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */

define('KOHANA_VERSION',  '2.3.1');
define('KOHANA_CODENAME', 'accipiter');

// Test of Kohana is running in Windows
define('KOHANA_IS_WIN', DIRECTORY_SEPARATOR === '\\');

// Kohana benchmarks are prefixed to prevent collisions
define('SYSTEM_BENCHMARK', 'system_benchmark');

// Load benchmarking support
require SYSPATH.'core/Benchmark'.EXT;

// Start total_execution
Benchmark::start(SYSTEM_BENCHMARK.'_total_execution');

// Start kohana_loading
Benchmark::start(SYSTEM_BENCHMARK.'_kohana_loading');

// Load core files
require SYSPATH.'core/utf8'.EXT;
require SYSPATH.'core/Event'.EXT;
require SYSPATH.'core/Kohana'.EXT;

// Prepare the environment
Kohana::setup();

// End kohana_loading
Benchmark::stop(SYSTEM_BENCHMARK.'_kohana_loading');

// Start system_initialization
Benchmark::start(SYSTEM_BENCHMARK.'_system_initialization');

// Prepare the system
Event::run('system.ready');

// Determine routing
Event::run('system.routing');

// End system_initialization
Benchmark::stop(SYSTEM_BENCHMARK.'_system_initialization');

// Make the magic happen!
Event::run('system.execute');

// Save benchmark results
Benchmark::save_results();

// Clean up and exit
Event::run('system.shutdown');
