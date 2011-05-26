<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Process control file to bootstrap the Kohana application, Ushahidi_Web in this case
 *
 * This file is a copy of the system/core/Bootstrap.php with the following events
 * disabled to prevent sending HTML output to STDIO:
 *      - Event::run('system.routing')
 *      - Benchmark::stop(SYSTEM_BENCHMARK.'_system_initialization')
 *      - Event::run('system.execute')
 * 
 * $Id: testbootstrap.php
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

// Clean up and exit
Event::run('system.shutdown');
