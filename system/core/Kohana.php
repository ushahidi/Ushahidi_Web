<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Provides Kohana-specific helper functions. This is where the magic happens!
 *
 * $Id: Kohana.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
final class Kohana {

	// The singleton instance of the controller
	public static $instance;

	// Output buffering level
	private static $buffer_level;

	// Will be set to TRUE when an exception is caught
	public static $has_error = FALSE;

	// The final output that will displayed by Kohana
	public static $output = '';

	// The current user agent
	public static $user_agent;

	// The current locale
	public static $locale;

	// Configuration
	private static $configuration;

	// Include paths
	private static $include_paths;

	// Logged messages
	private static $log;

	// Cache lifetime
	private static $cache_lifetime;

	// Log levels
	private static $log_levels = array
	(
		'error' => 1,
		'alert' => 2,
		'info'  => 3,
		'debug' => 4,
	);

	// Internal caches and write status
	private static $internal_cache = array();
	private static $write_cache;

	/**
	 * Sets up the PHP environment. Adds error/exception handling, output
	 * buffering, and adds an auto-loading method for loading classes.
	 *
	 * This method is run immediately when this file is loaded, and is
	 * benchmarked as environment_setup.
	 *
	 * For security, this function also destroys the $_REQUEST global variable.
	 * Using the proper global (GET, POST, COOKIE, etc) is inherently more secure.
	 * The recommended way to fetch a global variable is using the Input library.
	 * @see http://www.php.net/globals
	 *
	 * @return  void
	 */
	public static function setup()
	{
		static $run;

		// This function can only be run once
		if ($run === TRUE)
			return;

		// Start the environment setup benchmark
		Benchmark::start(SYSTEM_BENCHMARK.'_environment_setup');

		// Define Kohana error constant
		define('E_KOHANA', 42);

		// Define 404 error constant
		define('E_PAGE_NOT_FOUND', 43);

		// Define database error constant
		define('E_DATABASE_ERROR', 44);

		if (self::$cache_lifetime = self::config('core.internal_cache'))
		{
			// Load cached configuration and language files
			self::$internal_cache['configuration'] = self::cache('configuration', self::$cache_lifetime);
			self::$internal_cache['language']      = self::cache('language', self::$cache_lifetime);

			// Load cached file paths
			self::$internal_cache['find_file_paths'] = self::cache('find_file_paths', self::$cache_lifetime);

			// Enable cache saving
			Event::add('system.shutdown', array(__CLASS__, 'internal_cache_save'));
		}

		// Disable notices and "strict" errors
		$ER = error_reporting(~E_NOTICE & ~E_STRICT);

		// Set the user agent
		self::$user_agent = trim($_SERVER['HTTP_USER_AGENT']);

		if (function_exists('date_default_timezone_set'))
		{
			$timezone = self::config('locale.timezone');

			// Set default timezone, due to increased validation of date settings
			// which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
			date_default_timezone_set(empty($timezone) ? 'UTC' : $timezone);
		}

		// Restore error reporting
		error_reporting($ER);

		// Start output buffering
		// Hack to support PHP 5.4 - we're now calling Kohana::output_buffer() manually in Kohana::close_buffers() - rjmackay 20120914
		ob_start(/*array(__CLASS__, 'output_buffer')*/);

		// Save buffering level
		self::$buffer_level = ob_get_level();

		// Set autoloader
		spl_autoload_register(array('Kohana', 'auto_load'));

		// Set error handler
		set_error_handler(array('Kohana', 'exception_handler'));

		// Set exception handler
		set_exception_handler(array('Kohana', 'exception_handler'));

		// Send default text/html UTF-8 header
		header('Content-Type: text/html; charset=UTF-8');

		// Load locales
		$locales = self::config('locale.language');

		// Make first locale UTF-8
		$locales[0] .= '.UTF-8';

		// Set locale information
		self::$locale = setlocale(LC_ALL, $locales);

		if (self::$configuration['core']['log_threshold'] > 0)
		{
			// Set the log directory
			self::log_directory(self::$configuration['core']['log_directory']);

			// Enable log writing at shutdown
			register_shutdown_function(array(__CLASS__, 'log_save'));
		}

		// Enable Kohana routing
		Event::add('system.routing', array('Router', 'find_uri'));
		Event::add('system.routing', array('Router', 'setup'));

		// Enable Kohana controller initialization
		Event::add('system.execute', array('Kohana', 'instance'));

		// Enable Kohana 404 pages
		Event::add('system.404', array('Kohana', 'show_404'));

		// Enable Kohana output handling
		Event::add('system.shutdown', array('Kohana', 'shutdown'));

		if (self::config('core.enable_hooks') === TRUE)
		{
			// Find all the hook files
			$hooks = self::list_files('hooks', TRUE);

			foreach ($hooks as $file)
			{
				// Load the hook
				include $file;
			}
		}

		// Setup is complete, prevent it from being run again
		$run = TRUE;

		// Stop the environment setup routine
		Benchmark::stop(SYSTEM_BENCHMARK.'_environment_setup');
	}

	/**
	 * Loads the controller and initializes it. Runs the pre_controller,
	 * post_controller_constructor, and post_controller events. Triggers
	 * a system.404 event when the route cannot be mapped to a controller.
	 *
	 * This method is benchmarked as controller_setup and controller_execution.
	 *
	 * @return  object  instance of controller
	 */
	public static function & instance()
	{
		if (self::$instance === NULL)
		{
			Benchmark::start(SYSTEM_BENCHMARK.'_controller_setup');

			if (Router::$method[0] === '_')
			{
				// Do not allow access to hidden methods
				Event::run('system.404');
			}

			// Include the Controller file
			require Router::$controller_path;

			try
			{
				// Start validation of the controller
				$class = new ReflectionClass(ucfirst(Router::$controller).'_Controller');
			}
			catch (ReflectionException $e)
			{
				// Controller does not exist
				Event::run('system.404');
			}

			if ($class->isAbstract() OR (IN_PRODUCTION AND $class->getConstant('ALLOW_PRODUCTION') == FALSE))
			{
				// Controller is not allowed to run in production
				Event::run('system.404');
			}

			// Run system.pre_controller
			Event::run('system.pre_controller');

			// Begin benchmark for the controller
			Benchmark::f_start(ucfirst(Router::$controller).'_Controller');

			// Create a new controller instance
			$controller = $class->newInstance();

			// End benchmark for the controller
			Benchmark::f_stop(ucfirst(Router::$controller).'_Controller');

			// Controller constructor has been executed
			Event::run('system.post_controller_constructor');

			try
			{
				// Load the controller method
				$method = $class->getMethod(Router::$method);

				if ($method->isProtected() or $method->isPrivate())
				{
					// Do not attempt to invoke protected methods
					throw new ReflectionException('protected controller method');
				}

				// Default arguments
				$arguments = Router::$arguments;
			}
			catch (ReflectionException $e)
			{
				// Use __call instead
				$method = $class->getMethod('__call');

				// Use arguments in __call format
				$arguments = array(Router::$method, Router::$arguments);
			}

			// Stop the controller setup benchmark
			Benchmark::stop(SYSTEM_BENCHMARK.'_controller_setup');

			// Start the controller execution benchmark
			Benchmark::start(SYSTEM_BENCHMARK.'_controller_execution');

			// Execute the controller method
			$method->invokeArgs($controller, $arguments);

			// Controller method has been executed
			Event::run('system.post_controller');

			// Stop the controller execution benchmark
			Benchmark::stop(SYSTEM_BENCHMARK.'_controller_execution');
		}

		return self::$instance;
	}

	/**
	 * Get all include paths. APPPATH is the first path, followed by module
	 * paths in the order they are configured, follow by the SYSPATH.
	 *
	 * @param   boolean  re-process the include paths
	 * @return  array
	 */
	public static function include_paths($process = FALSE)
	{
		if ($process === TRUE)
		{
			// Add APPPATH as the first path
			self::$include_paths = array(APPPATH);

			foreach (self::$configuration['core']['modules'] as $path)
			{
				if ($path = str_replace('\\', '/', realpath($path)))
				{
					// Add a valid path
					self::$include_paths[] = $path.'/';
				}
			}

			// Add SYSPATH as the last path
			self::$include_paths[] = SYSPATH;
		}

		return self::$include_paths;
	}

	/**
	 * Get a config item or group.
	 *
	 * @param   string   item name
	 * @param   boolean  force a forward slash (/) at the end of the item
	 * @param   boolean  is the item required?
	 * @return  mixed
	 */
	public static function config($key, $slash = FALSE, $required = TRUE)
	{
		if (self::$configuration === NULL)
		{
			// Load core configuration
			self::$configuration['core'] = self::config_load('core');

			// Re-parse the include paths
			self::include_paths(TRUE);
		}

		// Get the group name from the key
		$group = explode('.', $key, 2);
		$group = $group[0];

		if ( ! isset(self::$configuration[$group]))
		{
			// Load the configuration group
			self::$configuration[$group] = self::config_load($group, $required);
		}

		// Get the value of the key string
		$value = self::key_string(self::$configuration, $key);

		if ($slash === TRUE AND is_string($value) AND $value !== '')
		{
			// Force the value to end with "/"
			$value = rtrim($value, '/').'/';
		}

		return $value;
	}

	/**
	 * Sets a configuration item, if allowed.
	 *
	 * @param   string   config key string
	 * @param   string   config value
	 * @return  boolean
	 */
	public static function config_set($key, $value)
	{
		// Do this to make sure that the config array is already loaded
		self::config($key);

		if (substr($key, 0, 7) === 'routes.')
		{
			// Routes cannot contain sub keys due to possible dots in regex
			$keys = explode('.', $key, 2);
		}
		else
		{
			// Convert dot-noted key string to an array
			$keys = explode('.', $key);
		}

		// Used for recursion
		$conf =& self::$configuration;
		$last = count($keys) - 1;

		foreach ($keys as $i => $k)
		{
			if ($i === $last)
			{
				$conf[$k] = $value;
			}
			else
			{
				$conf =& $conf[$k];
			}
		}

		if ($key === 'core.modules')
		{
			// Reprocess the include paths
			self::include_paths(TRUE);
		}

		return TRUE;
	}

	/**
	 * Load a config file.
	 *
	 * @param   string   config filename, without extension
	 * @param   boolean  is the file required?
	 * @return  array
	 */
	public static function config_load($name, $required = TRUE)
	{
		if ($name === 'core')
		{
			// Load the application configuration file
			require APPPATH.'config/config'.EXT;

			if ( ! isset($config['site_domain']))
			{
				// Invalid config file
				die('Your Kohana application configuration file is not valid.');
			}

			return $config;
		}

		if (isset(self::$internal_cache['configuration'][$name]))
			return self::$internal_cache['configuration'][$name];

		// Load matching configs
		$configuration = array();

		if ($files = self::find_file('config', $name, $required))
		{
			foreach ($files as $file)
			{
				require $file;

				if (isset($config) AND is_array($config))
				{
					// Merge in configuration
					$configuration = array_merge($configuration, $config);
				}
			}
		}

		if ( ! isset(self::$write_cache['configuration']))
		{
			// Cache has changed
			self::$write_cache['configuration'] = TRUE;
		}

		return self::$internal_cache['configuration'][$name] = $configuration;
	}

	/**
	 * Clears a config group from the cached configuration.
	 *
	 * @param   string  config group
	 * @return  void
	 */
	public static function config_clear($group)
	{
		// Remove the group from config
		unset(self::$configuration[$group], self::$internal_cache['configuration'][$group]);

		if ( ! isset(self::$write_cache['configuration']))
		{
			// Cache has changed
			self::$write_cache['configuration'] = TRUE;
		}
	}

	/**
	 * Add a new message to the log.
	 *
	 * @param   string  type of message
	 * @param   string  message text
	 * @return  void
	 */
	public static function log($type, $message)
	{
		if (self::$log_levels[$type] <= self::$configuration['core']['log_threshold'])
		{
			$message = array(date('Y-m-d H:i:s P'), $type, $message);

			// Run the system.log event
			Event::run('system.log', $message);

			self::$log[] = $message;
		}
	}

	/**
	 * Save all currently logged messages.
	 *
	 * @return  void
	 */
	public static function log_save()
	{
		if (empty(self::$log) OR self::$configuration['core']['log_threshold'] < 1)
			return;

		// Filename of the log
		$filename = self::log_directory().date('Y-m-d').'.log'.EXT;

		if ( ! is_file($filename))
		{
			// Write the SYSPATH checking header
			file_put_contents($filename,
				'<?php defined(\'SYSPATH\') or die(\'No direct script access.\'); ?>'.PHP_EOL.PHP_EOL);

			// Prevent external writes
			chmod($filename, 0644);
		}

		// Messages to write
		$messages = array();

		do
		{
			// Load the next mess
			list ($date, $type, $text) = array_shift(self::$log);

			// Add a new message line
			$messages[] = $date.' --- '.$type.': '.$text;
		}
		while ( ! empty(self::$log));

		// Write messages to log file
		file_put_contents($filename, implode(PHP_EOL, $messages).PHP_EOL, FILE_APPEND);
	}

	/**
	 * Get or set the logging directory.
	 *
	 * @param   string  new log directory
	 * @return  string
	 */
	public static function log_directory($dir = NULL)
	{
		static $directory;

		if ( ! empty($dir))
		{
			// Get the directory path
			$dir = realpath($dir);

			if (is_dir($dir) AND is_writable($dir))
			{
				// Change the log directory
				$directory = str_replace('\\', '/', $dir).'/';
			}
			else
			{
				// Log directory is invalid
				throw new Kohana_Exception('core.log_dir_unwritable', $dir);
			}
		}

		return $directory;
	}

	/**
	 * Load data from a simple cache file. This should only be used internally,
	 * and is NOT a replacement for the Cache library.
	 *
	 * @param   string   unique name of cache
	 * @param   integer  expiration in seconds
	 * @return  mixed
	 */
	public static function cache($name, $lifetime)
	{
		if ($lifetime > 0)
		{
			$path = APPPATH.'cache/kohana_'.$name;

			if (is_file($path))
			{
				// Check the file modification time
				if ((time() - filemtime($path)) < $lifetime)
				{
					// Cache is valid
					return unserialize(file_get_contents($path));
				}
				else
				{
					// Cache is invalid, delete it
					unlink($path);
				}
			}
		}

		// No cache found
		return NULL;
	}

	/**
	 * Save data to a simple cache file. This should only be used internally, and
	 * is NOT a replacement for the Cache library.
	 *
	 * @param   string   cache name
	 * @param   mixed    data to cache
	 * @param   integer  expiration in seconds
	 * @return  boolean
	 */
	public static function cache_save($name, $data, $lifetime)
	{
		if ($lifetime < 1)
			return FALSE;

		$path = APPPATH.'cache/kohana_'.$name;

		if ($data === NULL)
		{
			// Delete cache
			return (is_file($path) and unlink($path));
		}
		else
		{
			// Write data to cache file
			return (bool) file_put_contents($path, serialize($data));
		}
	}

	/**
	 * Kohana output handler.
	 *
	 * @param   string  current output buffer
	 * @return  string
	 */
	public static function output_buffer($output)
	{
		if ( ! Event::has_run('system.send_headers'))
		{
			// Run the send_headers event, specifically for cookies being set
			Event::run('system.send_headers');
		}

		// Set final output
		self::$output = $output;

		// Set and return the final output
		return $output;
	}

	/**
	 * Closes all open output buffers, either by flushing or cleaning all
	 * open buffers, including the Kohana output buffer.
	 *
	 * @param   boolean  disable to clear buffers, rather than flushing
	 * @return  void
	 */
	public static function close_buffers($flush = TRUE)
	{
		if (ob_get_level() >= self::$buffer_level)
		{
			// Set the close function
			$close = ($flush === TRUE) ? 'ob_end_flush' : 'ob_end_clean';

			while (ob_get_level() > self::$buffer_level)
			{
				// Flush or clean the buffer
				$close();
			}

			// This will flush the Kohana buffer, which sets self::$output
			// Hack to support PHP 5.4 - we're now calling Kohana::output_buffer() manually - rjmackay 20120914
			self::output_buffer(ob_get_contents());
			ob_end_clean();
			
			// Reset the buffer level
			self::$buffer_level = ob_get_level();
		}
	}

	/**
	 * Triggers the shutdown of Kohana by closing the output buffer, runs the system.display event.
	 *
	 * @return  void
	 */
	public static function shutdown()
	{
		// Close output buffers
		self::close_buffers(TRUE);

		// Run the output event
		Event::run('system.display', self::$output);

		// Render the final output
		self::render(self::$output);
	}

	/**
	 * Inserts global Kohana variables into the generated output and prints it.
	 *
	 * @param   string  final output that will displayed
	 * @return  void
	 */
	public static function render($output)
	{
		// Fetch memory usage in MB
		$memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

		// Fetch benchmark for page execution time
		$benchmark = Benchmark::get(SYSTEM_BENCHMARK.'_total_execution');

		if (self::config('core.render_stats') === TRUE)
		{
			// Replace the global template variables
			$output = str_replace(
				array
				(
					'{kohana_version}',
					'{kohana_codename}',
					'{execution_time}',
					'{memory_usage}',
					'{included_files}',
				),
				array
				(
					KOHANA_VERSION,
					KOHANA_CODENAME,
					$benchmark['time'],
					number_format($memory, 2).'MB',
					count(get_included_files()),
				),
				$output
			);
		}

		if ($level = self::config('core.output_compression') AND ini_get('output_handler') !== 'ob_gzhandler' AND (int) ini_get('zlib.output_compression') === 0)
		{
			if ($level < 1 OR $level > 9)
			{
				// Normalize the level to be an integer between 1 and 9. This
				// step must be done to prevent gzencode from triggering an error
				$level = max(1, min($level, 9));
			}

			if (stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
			{
				$compress = 'gzip';
			}
			elseif (stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== FALSE)
			{
				$compress = 'deflate';
			}
		}

		if (isset($compress) AND $level > 0)
		{
			switch ($compress)
			{
				case 'gzip':
					// Compress output using gzip
					$output = gzencode($output, $level);
				break;
				case 'deflate':
					// Compress output using zlib (HTTP deflate)
					$output = gzdeflate($output, $level);
				break;
			}

			// This header must be sent with compressed content to prevent
			// browser caches from breaking
			header('Vary: Accept-Encoding');

			// Send the content encoding header
			header('Content-Encoding: '.$compress);

			// Sending Content-Length in CGI can result in unexpected behavior
			if (stripos(PHP_SAPI, 'cgi') === FALSE)
			{
				header('Content-Length: '.strlen($output));
			}
		}

		echo $output;
	}

	/**
	 * Displays a 404 page.
	 *
	 * @throws  Kohana_404_Exception
	 * @param   string  URI of page
	 * @param   string  custom template
	 * @return  void
	 */
	public static function show_404($page = FALSE, $template = FALSE)
	{
		throw new Kohana_404_Exception($page, $template);
	}

	/**
	 * Dual-purpose PHP error and exception handler. Uses the kohana_error_page
	 * view to display the message.
	 *
	 * @param   integer|object  exception object or error code
	 * @param   string          error message
	 * @param   string          filename
	 * @param   integer         line number
	 * @return  void
	 */
	public static function exception_handler($exception, $message = NULL, $file = NULL, $line = NULL)
	{
		// PHP errors have 5 args, always
		$PHP_ERROR = (func_num_args() === 5);

		// Test to see if errors should be displayed
		if ($PHP_ERROR AND (error_reporting() & $exception) === 0)
			return;

		// This is useful for hooks to determine if a page has an error
		self::$has_error = TRUE;

		// Error handling will use exactly 5 args, every time
		if ($PHP_ERROR)
		{
			$code     = $exception;
			$type     = 'PHP Error';
			$template = 'kohana_error_page';
		}
		else
		{
			$code     = $exception->getCode();
			$type     = get_class($exception);
			$message  = $exception->getMessage();
			$file     = $exception->getFile();
			$line     = $exception->getLine();
			$template = ($exception instanceof Kohana_Exception) ? $exception->getTemplate() : 'kohana_error_page';
		}

		if (is_numeric($code))
		{
			$codes = self::lang('errors');

			if ( ! empty($codes[$code]))
			{
				list($level, $error, $description) = $codes[$code];
			}
			else
			{
				$level = 1;
				$error = $PHP_ERROR ? 'Unknown Error' : get_class($exception);
				$description = '';
			}
		}
		else
		{
			// Custom error message, this will never be logged
			$level = 5;
			$error = $code;
			$description = '';
		}

		// Remove the DOCROOT from the path, as a security precaution
		$file = str_replace('\\', '/', realpath($file));
		$file = preg_replace('|^'.preg_quote(DOCROOT).'|', '', $file);

		if ($level <= self::$configuration['core']['log_threshold'])
		{
			// Log the error
			self::log('error', self::lang('core.uncaught_exception', $type, $message, $file, $line));
		}

		if ($PHP_ERROR)
		{
			$description = self::lang('errors.'.E_RECOVERABLE_ERROR);
			$description = is_array($description) ? $description[2] : '';

			if ( ! headers_sent())
			{
				// Send the 500 header
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
			}
		}
		else
		{
			if (method_exists($exception, 'sendHeaders') AND ! headers_sent())
			{
				// Send the headers if they have not already been sent
				$exception->sendHeaders();
			}
		}

		while (ob_get_level() > self::$buffer_level)
		{
			// Close open buffers
			ob_end_clean();
		}

		// Test if display_errors is on
		if (self::$configuration['core']['display_errors'] === TRUE)
		{
			if ( ! IN_PRODUCTION AND $line != FALSE)
			{
				// Remove the first entry of debug_backtrace(), it is the exception_handler call
				$trace = $PHP_ERROR ? array_slice(debug_backtrace(), 1) : $exception->getTrace();

				// Beautify backtrace
				$trace = self::backtrace($trace);
			}

			// Load the error
			require self::find_file('views', empty($template) ? 'kohana_error_page' : $template);
		}
		else
		{
			// Get the i18n messages
			$error   = self::lang('core.generic_error');
			$message = self::lang('core.errors_disabled', url::site(), url::site(Router::$current_uri));

			// Load the errors_disabled view
			require self::find_file('views', 'kohana_error_disabled');
		}

		if ( ! Event::has_run('system.shutdown'))
		{
			// Run the shutdown even to ensure a clean exit
			Event::run('system.shutdown');
		}

		// Turn off error reporting
		error_reporting(0);
		exit;
	}

	/**
	 * Provides class auto-loading.
	 *
	 * @throws  Kohana_Exception
	 * @param   string  name of class
	 * @return  bool
	 */
	public static function auto_load($class)
	{
		if (class_exists($class, FALSE))
			return TRUE;

		if (($suffix = strrpos($class, '_')) > 0)
		{
			// Find the class suffix
			$suffix = substr($class, $suffix + 1);
		}
		else
		{
			// No suffix
			$suffix = FALSE;
		}

		if ($suffix === 'Core')
		{
			$type = 'libraries';
			$file = substr($class, 0, -5);
		}
		elseif ($suffix === 'Controller')
		{
			$type = 'controllers';
			// Lowercase filename
			$file = strtolower(substr($class, 0, -11));
		}
		elseif ($suffix === 'Model')
		{
			$type = 'models';
			// Lowercase filename
			$file = strtolower(substr($class, 0, -6));
		}
		elseif ($suffix === 'Driver')
		{
			$type = 'libraries/drivers';
			$file = str_replace('_', '/', substr($class, 0, -7));
		}
		else
		{
			// This could be either a library or a helper, but libraries must
			// always be capitalized, so we check if the first character is
			// uppercase. If it is, we are loading a library, not a helper.
			$type = ($class[0] < 'a') ? 'libraries' : 'helpers';
			$file = $class;
		}

		if ($filename = self::find_file($type, $file))
		{
			// Load the class
			require $filename;
		}
		else
		{
			// The class could not be found
			return FALSE;
		}

		if ($filename = self::find_file($type, self::$configuration['core']['extension_prefix'].$class))
		{
			// Load the class extension
			require $filename;
		}
		elseif ($suffix !== 'Core' AND class_exists($class.'_Core', FALSE))
		{
			// Class extension to be evaluated
			$extension = 'class '.$class.' extends '.$class.'_Core { }';

			// Start class analysis
			$core = new ReflectionClass($class.'_Core');

			if ($core->isAbstract())
			{
				// Make the extension abstract
				$extension = 'abstract '.$extension;
			}

			// Transparent class extensions are handled using eval. This is
			// a disgusting hack, but it gets the job done.
			eval($extension);
		}

		return TRUE;
	}

	/**
	 * Find a resource file in a given directory. Files will be located according
	 * to the order of the include paths. Configuration and i18n files will be
	 * returned in reverse order.
	 *
	 * @throws  Kohana_Exception  if file is required and not found
	 * @param   string   directory to search in
	 * @param   string   filename to look for (including extension only if 4th parameter is TRUE)
	 * @param   boolean  file required
	 * @param   string   file extension
	 * @return  array    if the type is config, i18n or l10n
	 * @return  string   if the file is found
	 * @return  FALSE    if the file is not found
	 */
	public static function find_file($directory, $filename, $required = FALSE, $ext = FALSE)
	{
		// NOTE: This test MUST be not be a strict comparison (===), or empty
		// extensions will be allowed!
		if ($ext == '')
		{
			// Use the default extension
			$ext = EXT;
		}
		else
		{
			// Add a period before the extension
			$ext = '.'.$ext;
		}

		// Search path
		$search = $directory.'/'.$filename.$ext;

		if (isset(self::$internal_cache['find_file_paths'][$search]))
			return self::$internal_cache['find_file_paths'][$search];

		// Load include paths
		$paths = self::$include_paths;

		// Nothing found, yet
		$found = NULL;

		if ($directory === 'config' OR $directory === 'i18n')
		{
			// Search in reverse, for merging
			$paths = array_reverse($paths);

			foreach ($paths as $path)
			{
				if (is_file($path.$search))
				{
					// A matching file has been found
					$found[] = $path.$search;
				}
			}
		}
		else
		{
			foreach ($paths as $path)
			{
				if (is_file($path.$search))
				{
					// A matching file has been found
					$found = $path.$search;

					// Stop searching
					break;
				}
			}
		}

		if ($found === NULL)
		{
			if ($required === TRUE)
			{
				// Directory i18n key
				$directory = 'core.'.inflector::singular($directory);

				// If the file is required, throw an exception
				throw new Kohana_Exception('core.resource_not_found', self::lang($directory), $filename);
			}
			else
			{
				// Nothing was found, return FALSE
				$found = FALSE;
			}
		}

		if ( ! isset(self::$write_cache['find_file_paths']))
		{
			// Write cache at shutdown
			self::$write_cache['find_file_paths'] = TRUE;
		}

		return self::$internal_cache['find_file_paths'][$search] = $found;
	}

	/**
	 * Lists all files and directories in a resource path.
	 *
	 * @param   string   directory to search
	 * @param   boolean  list all files to the maximum depth?
	 * @param   string   full path to search (used for recursion, *never* set this manually)
	 * @return  array    filenames and directories
	 */
	public static function list_files($directory, $recursive = FALSE, $path = FALSE)
	{
		$files = array();

		if ($path === FALSE)
		{
			$paths = array_reverse(self::include_paths());

			foreach ($paths as $path)
			{
				// Recursively get and merge all files
				$files = array_merge($files, self::list_files($directory, $recursive, $path.$directory));
			}
		}
		else
		{
			$path = rtrim($path, '/').'/';

			if (is_readable($path))
			{
				$items = (array) glob($path.'*');

				foreach ($items as $index => $item)
				{
					$files[] = $item = str_replace('\\', '/', $item);

					// Handle recursion
					if (is_dir($item) AND $recursive == TRUE)
					{
						// Filename should only be the basename
						$item = pathinfo($item, PATHINFO_BASENAME);

						// Append sub-directory search
						$files = array_merge($files, self::list_files($directory, TRUE, $path.$item));
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Fetch an i18n language item.
	 *
	 * @param   string  language key to fetch
	 * @param   array   additional information to insert into the line
	 * @param   string  locale name to override settings
	 * @return  string  i18n language string, or the requested key if the i18n item is not found
	 */
	public static function lang($key, $args = array(), $force_locale = NULL)
	{
		// Extract the main group from the key
		$group = explode('.', $key, 2);
		$group = $group[0];

		// Get locale name
		$locale = self::config('locale.language.0');

		// If we are overriding the language settings then do it here.
		if ($force_locale != NULL)
		{
			$locale = $force_locale;
		}

		if ( ! isset(self::$internal_cache['language'][$locale][$group]))
		{
			// Messages for this group
			$messages = array();

			if ($files = self::find_file('i18n', $locale.'/'.$group))
			{
				foreach ($files as $file)
				{
					include $file;

					// Merge in configuration
					if ( ! empty($lang) AND is_array($lang))
					{
						foreach ($lang as $k => $v)
						{
							$messages[$k] = $v;
						}
					}
				}
			}

			if ( ! isset(self::$write_cache['language']))
			{
				// Write language cache
				self::$write_cache['language'] = TRUE;
			}

			self::$internal_cache['language'][$locale][$group] = $messages;
		}

		// Get the line from cache
		$line = self::key_string(self::$internal_cache['language'][$locale], $key);

		if ($line === NULL)
		{
			// Only save error to log for en_US to the log since it's the default fallback
			if ($locale == 'en_US')
			{
				self::log('info', 'Missing i18n entry '.$key.' for language '.$locale);
			}

			if ($force_locale != NULL)
			{
				// Return the key string as fallback
				return $key;
			}else{
				// Try for English
				return Kohana::lang($key, $args, 'en_US');
			}
		}

		if (is_string($line) AND func_num_args() > 1)
		{
			$args = array_slice(func_get_args(), 1);

			// Add the arguments into the line
			$line = vsprintf($line, is_array($args[0]) ? $args[0] : $args);
		}

		return $line;
	}

	/**
	 * Returns the value of a key, defined by a 'dot-noted' string, from an array.
	 *
	 * @param   array   array to search
	 * @param   string  dot-noted string: foo.bar.baz
	 * @return  string  if the key is found
	 * @return  void    if the key is not found
	 */
	public static function key_string($array, $keys)
	{
		if (empty($array))
			return NULL;

		// Prepare for loop
		$keys = explode('.', $keys);

		do
		{
			// Get the next key
			$key = array_shift($keys);

			if (isset($array[$key]))
			{
				if (is_array($array[$key]) AND ! empty($keys))
				{
					// Dig down to prepare the next loop
					$array = $array[$key];
				}
				else
				{
					// Requested key was found
					return $array[$key];
				}
			}
			else
			{
				// Requested key is not set
				break;
			}
		}
		while ( ! empty($keys));

		return NULL;
	}

	/**
	 * Sets values in an array by using a 'dot-noted' string.
	 *
	 * @param   array   array to set keys in (reference)
	 * @param   string  dot-noted string: foo.bar.baz
	 * @return  mixed   fill value for the key
	 * @return  void
	 */
	public static function key_string_set( & $array, $keys, $fill = NULL)
	{
		if (is_object($array) AND ($array instanceof ArrayObject))
		{
			// Copy the array
			$array_copy = $array->getArrayCopy();

			// Is an object
			$array_object = TRUE;
		}
		else
		{
			if ( ! is_array($array))
			{
				// Must always be an array
				$array = (array) $array;
			}

			// Copy is a reference to the array
			$array_copy =& $array;
		}

		if (empty($keys))
			return $array;

		// Create keys
		$keys = explode('.', $keys);

		// Create reference to the array
		$row =& $array_copy;

		for ($i = 0, $end = count($keys) - 1; $i <= $end; $i++)
		{
			// Get the current key
			$key = $keys[$i];

			if ( ! isset($row[$key]))
			{
				if (isset($keys[$i + 1]))
				{
					// Make the value an array
					$row[$key] = array();
				}
				else
				{
					// Add the fill key
					$row[$key] = $fill;
				}
			}
			elseif (isset($keys[$i + 1]))
			{
				// Make the value an array
				$row[$key] = (array) $row[$key];
			}

			// Go down a level, creating a new row reference
			$row =& $row[$key];
		}

		if (isset($array_object))
		{
			// Swap the array back in
			$array->exchangeArray($array_copy);
		}
	}

	/**
	 * Retrieves current user agent information:
	 * keys:  browser, version, platform, mobile, robot, referrer, languages, charsets
	 * tests: is_browser, is_mobile, is_robot, accept_lang, accept_charset
	 *
	 * @param   string   key or test name
	 * @param   string   used with "accept" tests: user_agent(accept_lang, en)
	 * @return  array    languages and charsets
	 * @return  string   all other keys
	 * @return  boolean  all tests
	 */
	public static function user_agent($key = 'agent', $compare = NULL)
	{
		static $info;

		// Return the raw string
		if ($key === 'agent')
			return self::$user_agent;

		if ($info === NULL)
		{
			// Parse the user agent and extract basic information
			$agents = self::config('user_agents');

			foreach ($agents as $type => $data)
			{
				foreach ($data as $agent => $name)
				{
					if (stripos(self::$user_agent, $agent) !== FALSE)
					{
						if ($type === 'browser' AND preg_match('|'.preg_quote($agent).'[^0-9.]*+([0-9.][0-9.a-z]*)|i', self::$user_agent, $match))
						{
							// Set the browser version
							$info['version'] = $match[1];
						}

						// Set the agent name
						$info[$type] = $name;
						break;
					}
				}
			}
		}

		if (empty($info[$key]))
		{
			switch ($key)
			{
				case 'is_robot':
				case 'is_browser':
				case 'is_mobile':
					// A boolean result
					$return = ! empty($info[substr($key, 3)]);
				break;
				case 'languages':
					$return = array();
					if ( ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
					{
						if (preg_match_all('/[-a-z]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])), $matches))
						{
							// Found a result
							$return = $matches[0];
						}
					}
				break;
				case 'charsets':
					$return = array();
					if ( ! empty($_SERVER['HTTP_ACCEPT_CHARSET']))
					{
						if (preg_match_all('/[-a-z0-9]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET'])), $matches))
						{
							// Found a result
							$return = $matches[0];
						}
					}
				break;
				case 'referrer':
					if ( ! empty($_SERVER['HTTP_REFERER']))
					{
						// Found a result
						$return = trim($_SERVER['HTTP_REFERER']);
					}
				break;
			}

			// Cache the return value
			isset($return) and $info[$key] = $return;
		}

		if ( ! empty($compare))
		{
			// The comparison must always be lowercase
			$compare = strtolower($compare);

			switch ($key)
			{
				case 'accept_lang':
					// Check if the lange is accepted
					return in_array($compare, self::user_agent('languages'));
				break;
				case 'accept_charset':
					// Check if the charset is accepted
					return in_array($compare, self::user_agent('charsets'));
				break;
				default:
					// Invalid comparison
					return FALSE;
				break;
			}
		}

		// Return the key, if set
		return isset($info[$key]) ? $info[$key] : NULL;
	}

	/**
	 * Quick debugging of any variable. Any number of parameters can be set.
	 *
	 * @return  string
	 */
	public static function debug()
	{
		if (func_num_args() === 0)
			return;

		// Get params
		$params = func_get_args();
		$output = array();

		foreach ($params as $var)
		{
			$output[] = '<pre>('.gettype($var).') '.html::specialchars(print_r($var, TRUE)).'</pre>';
		}

		return implode("\n", $output);
	}

	/**
	 * Displays nice backtrace information.
	 * @see http://php.net/debug_backtrace
	 *
	 * @param   array   backtrace generated by an exception or debug_backtrace
	 * @return  string
	 */
	public static function backtrace($trace)
	{
		if ( ! is_array($trace))
			return;

		// Final output
		$output = array();

		foreach ($trace as $entry)
		{
			$temp = '<li>';

			if (isset($entry['file']))
			{
				// Hack this since kohana lang seems to fail here every time
				$temp .= sprintf('<tt>%s <strong>[%s]:</strong></tt>', preg_replace('!^'.preg_quote(DOCROOT).'!', '', $entry['file']), $entry['line']);
				//$temp .= self::lang('core.error_file_line', preg_replace('!^'.preg_quote(DOCROOT).'!', '', $entry['file']), $entry['line']);
			}

			$temp .= '<pre>';

			if (isset($entry['class']))
			{
				// Add class and call type
				$temp .= $entry['class'].$entry['type'];
			}

			// Add function
			$temp .= $entry['function'].'( ';

			// Add function args
			if (isset($entry['args']) AND is_array($entry['args']))
			{
				// Separator starts as nothing
				$sep = '';

				while ($arg = array_shift($entry['args']))
				{
					if (is_string($arg) AND is_file($arg))
					{
						// Remove docroot from filename
						$arg = preg_replace('!^'.preg_quote(DOCROOT).'!', '', $arg);
					}

					$temp .= $sep.html::specialchars(print_r($arg, TRUE));

					// Change separator to a comma
					$sep = ', ';
				}
			}

			$temp .= ' )</pre></li>';

			$output[] = $temp;
		}

		return '<ul class="backtrace">'.implode("\n", $output).'</ul>';
	}

	/**
	 * Saves the internal caches: configuration, include paths, etc.
	 *
	 * @return  boolean
	 */
	public static function internal_cache_save()
	{
		if ( ! is_array(self::$write_cache))
			return FALSE;

		// Get internal cache names
		$caches = array_keys(self::$write_cache);

		// Nothing written
		$written = FALSE;

		foreach ($caches as $cache)
		{
			if (isset(self::$internal_cache[$cache]))
			{
				// Write the cache file
				self::cache_save($cache, self::$internal_cache[$cache], self::$configuration['core']['internal_cache']);

				// A cache has been written
				$written = TRUE;
			}
		}

		return $written;
	}

} // End Kohana

/**
 * Creates a generic i18n exception.
 */
class Kohana_Exception extends Exception {

	// Template file
	protected $template = 'kohana_error_page';

	// Header
	protected $header = FALSE;

	// Error code
	protected $code = E_KOHANA;

	/**
	 * Set exception message.
	 *
	 * @param  string  i18n language key for the message
	 * @param  array   addition line parameters
	 */
	public function __construct($error)
	{
		$args = array_slice(func_get_args(), 1);

		// Fetch the error message
		$message = Kohana::lang($error, $args);

		if ($message === $error OR empty($message))
		{
			// Unable to locate the message for the error
			$message = 'Unknown Exception: '.$error;
		}

		// Sets $this->message the proper way
		parent::__construct($message);
	}

	/**
	 * Magic method for converting an object to a string.
	 *
	 * @return  string  i18n message
	 */
	public function __toString()
	{
		return (string) $this->message;
	}

	/**
	 * Fetch the template name.
	 *
	 * @return  string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Sends an Internal Server Error header.
	 *
	 * @return  void
	 */
	public function sendHeaders()
	{
		// Send the 500 header
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
	}

} // End Kohana Exception

/**
 * Creates a custom exception.
 */
class Kohana_User_Exception extends Kohana_Exception {

	/**
	 * Set exception title and message.
	 *
	 * @param   string  exception title string
	 * @param   string  exception message string
	 * @param   string  custom error template
	 */
	public function __construct($title, $message, $template = FALSE)
	{
		Exception::__construct($message);

		$this->code = $title;

		if ($template !== FALSE)
		{
			$this->template = $template;
		}
	}

} // End Kohana PHP Exception

/**
 * Creates a Page Not Found exception.
 */
class Kohana_404_Exception extends Kohana_Exception {

	protected $code = E_PAGE_NOT_FOUND;

	/**
	 * Set internal properties.
	 *
	 * @param  string  URL of page
	 * @param  string  custom error template
	 */
	public function __construct($page = FALSE, $template = FALSE)
	{
		if ($page === FALSE)
		{
			// Construct the page URI using Router properties
			$page = Router::$current_uri.Router::$url_suffix.Router::$query_string;
		}

		Exception::__construct(Kohana::lang('core.page_not_found', $page));

		$this->template = $template;
	}

	/**
	 * Sends "File Not Found" headers, to emulate server behavior.
	 *
	 * @return void
	 */
	public function sendHeaders()
	{
		// Send the 404 header
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 File Not Found');
	}

} // End Kohana 404 Exception
