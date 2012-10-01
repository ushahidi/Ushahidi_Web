<?php
/**
 * Installatation Wizard
 *
 * This class manages the installation process. It tracks the
 * status of the installation and allows a user to resume from
 * where they left off. All install stages are submitted for processing
 * via HTTP POST. 
 *
 * The config files are created and/or modified at the end of the installation
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @subpackage Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Installer_Wizard {
	
	/**
	 * Configuration directory for the application
	 */
	private static $_app_config_dir = '';
	
	/**
	 * Installer data
	 * @var array
	 */
	private static $_data = array();
	
	/**
	 * @var array
	 */
	private static $_errors = array();
		
	/**
	 * Database connection
	 * @var mixed
	 */
	private static $_connection = FALSE;
	
	/**
	 * Required extensions
	 * @var array
	 */
	private static $_extensions = array(
		// Extensions required for UTF-8 functions
		'pcre',
		'iconv',
		'mbstring',
		
		// Database access
		'mysql',
		
		// cURL for remote site access
		'curl',
		
		// IMAP extension
		//'imap',
		
		// GD library for imaging
		'gd',
		
		// Encryption
		'mcrypt',
		
		// Misc
		'spl'
	);
	
	/**
	 * Directories and files that should be writeable by the installer
	 * and application
	 * @var array
	 */
	private static $_filesystem = array(
		// Cache directory
		'cache' => 'application/cache',

		// Logs directory
		'logs' => 'application/logs',

		// Config directory
		'config' => 'application/config',

		// Uploads directory
		'uploads' => 'media/uploads',

		// .htaccess file
		'htaccess' => '.htaccess'
	);

	/**
	 * Installation stages for each mode (basic & advanced)
	 * Precedence of the stages matters
	 * @var array
	 */
	private static $_install_stages = array(
		// Basic install stages
		'basic' => array(
			'requirements',
			'database',
			'general',
			'adminpassword',
			'finish'
		),

		// Advanced install stages
		'advanced' => array(
			'requirements',
			'database',
			'general',
			'email',
			'map',
			'adminpassword',
			'finish'
		)
	);
	
	/**
	 * Form data for the install phases
	 * @var array
	 */
	private static $_forms = array(
		// Database form
		'database' => array(
			'base_path' => '',
			'database' => '',
			'username' => '',
			'password' => '',
			'host' => '',
			'table_prefix' => ''
		),
		
		// General site settings
		'general' => array(
			'site_name' => '',
			'site_tagline' => '',
			'site_language' => '',
			'site_email' => '',
			'enable_clean_urls' => ''
		),
		
		// Email
		'email' => array(
			'alerts_email' => '',
			'email_host' => '',
			'email_username' => '',
			'email_password' => '',
			'email_port' => ''
		),
		
		// Admin password
		'adminpassword' => array(
			'email' => '',
			'password' => '',
			'confirm_password' => ''
		)
	);


	/**
	 * Bootstraps the installer
	 */
	public static function init()
	{
		self::$_app_config_dir = ROOT_DIR.'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;
		
		// Initialize the session
		session_start();

		self::$_data = & $_SESSION;
		
		// Check if the application has already been installed
		if (self::is_installed())
		{
			session_destroy();

			session_unset();
			
			header("Location:../");
			
			// For security: Make sure we don't return the rest of the page
			exit();
		}

		// 
		// TODO: Expire the session after 30 minutes
		// and implement mechanisms to prevent attacks on sessions
		// 

		// Check if installation has started or if a current stage exists
		if ( ! isset(self::$_data['started']) OR ! isset(self::$_data['current_stage']))
		{
			self::$_data['started'] = TRUE;
			
			// Get the site protocol
			$protocol = (isset($_SERVER['HTTPS']) OR (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] === 'on'))
			    ? 'https'
			    : 'http';
						
			// Site protocol
			self::$_data['site_protocol'] = $protocol;
			
			// Build the full URI
			$request_uri = $_SERVER['REQUEST_URI'];
			
			// Get the installation directory
			$site_domain = substr(substr($request_uri, 0, stripos($request_uri, '/installer/')) ,1);
			
			if (strpos($site_domain, "/") !== 0)
			{
				$site_domain = "/" . $site_domain;
			}
			
			// Server port
			$port = ! in_array($_SERVER['SERVER_PORT'], array("80", "443"))
			    ? ':'.$_SERVER['SERVER_PORT'] 
			    : '';

			// Build out the base URL
			$base_url = $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$site_domain;

			// Add a trailing slash to the base URL
			if (substr($base_url, -1) !== "/")
			{
				$base_url .= "/";
			}
			
			self::$_data['site_domain'] = $site_domain;
			self::$_data['base_url'] = $base_url;

			if (isset(self::$_data['current_stage']))
			{
				unset(self::$_data['current_stage']);
			}
		
			// Show the welcome page
			self::render_install_page('main');
		}
		else
		{
			self::render_install_page(self::$_data['current_stage']);
		}
		
	}
	
		
	/**
	 * Executes the callback function of the current step of the installtion
	 * process and loads the page for the next stage. The callback function
	 * for the current step should halt script execution if the validation
	 * checks fail
	 */
	public static function next()
	{		
		// Check for the install mode
		if (isset($_POST['install_mode_basic']))
		{
			self::$_data['install_mode'] = 'basic';
		}
		elseif (isset($_POST['install_mode_advanced']))
		{
			self::$_data['install_mode'] = 'advanced';
		}
		
		// Get the installer mode
		$install_mode = self::$_data['install_mode'];
		
		// Get the current stage name
		$stage_name = array_key_exists('current_stage', self::$_data)
		    ? self::$_data['current_stage']
		    : NULL;

		if (empty($stage_name))
		{
			// We're in the first stage
			$stage_name = self::$_install_stages[$install_mode][0];
			self::$_data['current_stage'] = $stage_name;
		}
		else
		{
			// Run the current stage before advancing to the next one
			$callback_func = sprintf("install_%s", $stage_name);
			
			if ( ! self::$callback_func())
			{
				self::render_install_page($stage_name);
				exit;
			}

			// Set the next stage to be executed
			$stage_position = self::_get_install_position($install_mode, $stage_name) + 1;
			self::$_data['current_stage'] = self::$_install_stages[$install_mode][$stage_position];
		}

		// If we're in the last stage, create/modify all the configuration files
		if (self::_is_last_stage())
		{
			// Create database.php
			self::_create_database_config();

			// Modify the .htaccess and config.php
			self::_set_base_path();

			// Modify encrypt.php and auth.php
			self::_set_security_params();

			// Check for errors
			if (count(self::$_errors) > 0)
			{
				// Go the the previous 
				self::previous(FALSE);
			}
		}
		
		// Show the page for the next stage
		self::render_install_page(self::$_data['current_stage']);
	}
	
	/**
	 * Loads the page for the previous installation step.
	 */
	public static function previous($render = TRUE)
	{
		if ( ! array_key_exists('current_stage', self::$_data))
		{
			self::render_install_page('main');
			return;
		}
		$install_mode = self::$_data['install_mode'];
		$current_position = self::_get_install_position($install_mode, self::$_data['current_stage']);

		$page = 'main';
		if ($current_position > 0)
		{
			$current_position -= 1;
			
			$page = self::$_install_stages[$install_mode][$current_position];
			self::$_data['current_stage'] = $page;
		}
		else
		{
			unset(self::$_data['current_stage']);
		}
		
		// Render?
		if ($render)
		{
			self::render_install_page($page);
		}
	}
	
	/**
	 * Given the installation mode and stage name, gets the index
	 * of the install stage
	 *
	 * @param string $mode
	 * @param string $stage_name
	 * @return int
	 */
	private static function _get_install_position($mode, $stage_name)
	{
		$stages = self::$_install_stages[$mode];
		$stage_pos = 0;

		for ($i = 0; $i < count($stages); $i++)
		{
			if ($stages[$i] === $stage_name)
			{
				$stage_pos = $i;
				break;
			}
		}
		
		return $stage_pos;
	}
	
	/**
	 * Checks if the installer is in the last installation stage
	 * @return bool
	 */
	private static function _is_last_stage()
	{
		if ( ! array_key_exists('current_stage', self::$_data))
			return FALSE;

		$current = self::$_data['current_stage'];
		$mode = self::$_data['install_mode'];
		
		$position = self::_get_install_position($mode, $current);
		
		return count(self::$_install_stages[$mode]) === ($position + 1);
	}

	/**
	 * Verifies whether all the extensions in $_extensions
	 * have been loaded.
	 *
	 * @return bool 
	 */
	private static function _verify_extensions()
	{
		foreach (self::$_extensions as $extension)
		{
			if ( ! extension_loaded($extension))
			{
				self::$_errors[] = sprintf("The <code>%s</code> extension is disabled", $extension);
			}
		}
		
		return count(self::$_errors) == 0;
	}
	
	
	/**
	 * Verifies that the entries in $_filesystem are writeable 
	 * by the installer and the application
	 *
	 * @return bool 
	 */
	private static function _verify_permissions()
	{
		// Check if the filesytem object are writeable
		foreach (self::$_filesystem as $key => $item)
		{
			$item = preg_replace("/\//", DIRECTORY_SEPARATOR, $item);
			$full_path = ROOT_DIR.$item;
			if ( ! is_writable($full_path))
			{
				$type = is_dir($full_path) ? 'directory' : 'file';
				self::$_errors[] = sprintf("<code>%s</code> %s is not writable", $item, $type);
			}
		}
		
		return count(self::$_errors) == 0;
	}
	
	/**
	 * Checks whether the application has already been installed
	 * The presence of database.php in application/config halts the
	 * installation process and redirects to the landing page
	 *
	 * @return bool
	 */
	public static function is_installed()
	{
		// Absolute file name of the DB config file
		$db_config_file = self::$_app_config_dir.'database.php';
		return file_exists($db_config_file);
	}
	
	/**
	 * Helper function for display the page for the various
	 * installation phases. The name of the page must be of a file
	 * that exists in the {installer}/pages directory
	 *
	 * @param $page_name string Name of the page to be displayed
	 */
	public static function render_install_page($page_name)
	{
		// Clear buffer
		if (ob_get_length() > 0)
		{
			ob_end_clean();
		}
		
		ob_start();
		
		// Full path of the page to be rendered
		$page_path = INSTALLER_PAGES . $page_name . '.php';
		
		include INSTALLER_PAGES . 'header.php';
		
		// If the page doesn't exist, show the main page
		if (file_exists($page_path))
		{
			$install_data = self::$_data;
			
			// Remove database info
			if (array_key_exists('database', $install_data))
			{
				unset($install_data['database']);
			}
			
			// Check for errors
			if (count(self::$_errors) > 0)
			{
				$install_data['errors'] = self::$_errors;
			}
		
			// Check if there are any forms for the current page
			if (array_key_exists($page_name, self::$_forms))
			{
				$form = self::$_forms[$page_name];
				if ($_POST)
				{
					foreach ($_POST as $field => $value)
					{
						if (array_key_exists($field, $form))
						{
							$form[$field] = $value;
						}
					}
				}

				$install_data['form'] = $form;
			}

			extract($install_data);
		}
		else
		{
			// Restart the installation
			unset (self::$_data['current_stage']);
			$page_path =  INSTALLER_PAGES . 'main.php';
		}
		
		include $page_path;
		
		if (self::_is_last_stage())
		{
			// Destroy session data
			session_destroy();

			// Unset $_SESSION variable for the runtime
			session_unset();
		}
		
		$content = ob_get_contents();
		ob_clean();
		print $content;
	}
	
	/**
	 * Checks whether a set of parameters have values in the payload. The optional
	 * items are skipped
	 *
	 * @param array $params    Parameter keys to be checked for in the payload
	 * @param array $payload   Input data 
	 * @param array $optional  Parameters to be exempted from the validation check
	 *
	 * @return bool
	 */
	private static function _validate_params($params, & $payload, $optional = NULL)
	{
		foreach ($params as $param)
		{
			if ( ! empty($optional) AND in_array($param, $optional))
				continue;
			
			if (empty($payload[$param]))
			{
				self::$_errors[] = sprintf("The <code>%s</code> parameter has not been specified", $param);
			}
			else
			{
				// Input sanitization
				$payload[$param] = strip_tags($payload[$param]);
			}
		}

		return count(self::$_errors) === 0;
	}


	/**
	 * Verifies whether the installation requirements
	 * have been met
	 *
	 * @return bool
	 */
	public static function install_requirements()
	{
		// Verify the extensions
		self::_verify_extensions();

		// Verify the permissions
		self::_verify_permissions();
		
		// Check for errors
		if (count(self::$_errors) == 0)
		{
			// Check if clean urls are enabled
			self::$_data['enable_clean_urls'] = self::_clean_urls_enabled();
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Sets up the database and creates the database.php
	 * config file
	 *
	 * @return bool
	 */
	public static function install_database()
	{
		// Verify the host, username and password have been specified
		$params = array('host', 'username', 'password', 'database');
		
		if ( ! self::_validate_params($params, $_POST))
			return FALSE;
			
		// Extract the POST data
		extract($_POST);

		// Store the database info
		self::$_data['database'] = array(
			'host' => $host,
			'username' => $username,
			'password' => $password,
			'database_name' => $database,
			'table_prefix' => $table_prefix
		);
		
		// Set up the database schema + objects
		self::_database_connect();
		
		if ( ! self::$_connection)
		{
			self::$_errors[] = sprintf("Database connection error: %s", mysql_error());
			
			return FALSE;
		}

		// Get the schema DDL script
		$schema_ddl = file_get_contents(ROOT_DIR.'sql'.DIRECTORY_SEPARATOR.'ushahidi.sql');
	
		// Add table prefix
		if ( ! empty($table_prefix))
		{
			$find = array(
				'CREATE TABLE IF NOT EXISTS `',
				'INSERT INTO `',
				'INSERT IGNORE INTO `',
				'ALTER TABLE `',
				'UPDATE `',
				'FROM `',
				'LOCK TABLES `',
				'DROP TABLE IF EXISTS `',
				'RENAME TABLE `',
				' TO `',
			);
			
			$replace = array(
				'CREATE TABLE IF NOT EXISTS `'.$table_prefix,
				'INSERT INTO `'.$table_prefix,
				'INSERT IGNORE INTO `'.$table_prefix,
				'ALTER TABLE `'.$table_prefix,
				'UPDATE `'.$table_prefix,
				'FROM `'.$table_prefix,
				'LOCK TABLES `'.$table_prefix,
				'DROP TABLE IF EXISTS `'.$table_prefix,
				'RENAME TABLE `'.$table_prefix,
				' TO `'.$table_prefix,
			);
		
			$schema_ddl = str_replace($find, $replace, $schema_ddl);
		}
	
		// Run the script
		foreach (explode(";", $schema_ddl) as $query)
		{
			if ( ! self::_execute_query($query))
			{
				break;
			}
		}
				
		return count(self::$_errors) == 0;
	}
	
	/**
	 * Connects to the database
	 */
	private static function _database_connect()
	{
		$params = self::$_data['database'];
		
		self::$_connection = mysql_connect($params['host'], $params['username'], 
		    $params['password'], TRUE);
		
		if ( ! self::$_connection)
		{
			self::$_errors[] = sprintf("Connection error: <strong>%s</strong>", mysql_error());

			return FALSE;
		}
		
		$database_name = $params['database_name'];
		
		if ( ! mysql_select_db($database_name))
		{
			if (self::_execute_query(sprintf("CREATE DATABASE %s", self::_escape_str($database_name))))
			{
				mysql_select_db($database_name, self::$_connection);
			}
			else
			{
				self::$_errors[] = sprintf("Error creating database: %s", mysql_error());
			}
		}
				
	}
	
	/**
	 * Executes a query against the database
	 * @param string $query
	 */
	private static function _execute_query($query)
	{
		if (self::$_connection === FALSE)
		{
			self::_database_connect();
		}
		
		if (strlen(trim($query)) > 0)
		{
			if ( ! mysql_query($query))
			{
				self::$_errors[] = sprintf("Encountered error <strong>%s</strong> when executing query <code>%s</code>", 
				    mysql_error(), $query);
			
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Escape string for the database
	 * @param string $query
	 */
	private static function _escape_str($string)
	{
		if (self::$_connection === FALSE)
		{
			self::_database_connect();
		}
		
		return mysql_real_escape_string($string);
	}
	
	/**
	 * Creates the database configuration file - database.php
	 */
	private static function _create_database_config()
	{
		$template_file_name = self::$_app_config_dir.'database.template.php';
		
		$output_file_name = self::$_app_config_dir.'database.php';
		
		// Create the new config file
		if (($output_file = @fopen($output_file_name, 'w')) !== FALSE)
		{
			if (($template_file = file($template_file_name)) !== FALSE)
			{
				$params = self::$_data['database'];
				$config_params = array(
					'user' => $params['username'],
					'pass' => $params['password'],
					'host' => $params['host'],
					'database' => $params['database_name'],
					'table_prefix' => $params['table_prefix']
				);
				
				foreach ($template_file as $line_no => $line)
				{
					foreach ($config_params as $config => $value)
					{
						$search = sprintf("/'%s' =>.*/i", $config);
						if (preg_match($search, $line, $matches))
						{
							$replace = sprintf("'%s' => '%s',", $config, $value);
							$line = preg_replace("/".$matches[0]."/i", $replace, $line);
							break;
						}
					}
					fwrite($output_file, $line);
				}
			}
			fclose($output_file);
		}
		else
		{
			self::$_errors[] = "Error creating the database config file. Check permissions";
		}
		
		return count(self::$_errors) == 0;
	}
	
	/**
	 * Updates the .htaccess and config.php files with the base path
	 */
	private static function _set_base_path()
	{
		// TODO Set the site_domain, site_protocol and install_check parameters
		$params = array(
			'site_domain' => self::$_data['site_domain'],
			'site_protocol' => self::$_data['site_protocol'],
			'installer_check' => FALSE
		);
		
		// Clean URLs enabled, set the 'index_page' config to ''
		// else it remains as 'index.php'
		if (self::$_data['enable_clean_urls'])
		{
			$params = array_merge($params, array(
				'index_page' => ''
			));
		}

		// .htaccess
		$htaccess_file = ROOT_DIR.'.htaccess';
		$htaccess = file($htaccess_file);
		if (($fp = @fopen($htaccess_file, 'w')) !== FALSE)
		{
			foreach ($htaccess as $line_no => $line)
			{
				if (preg_match("/RewriteBase.*/i", $line, $matches))
				{
					$replace = sprintf("RewriteBase %s", self::$_data['site_domain']);
					$line = str_replace($matches[0], $replace, $line);
				}
				fwrite($fp, $line);
			}
			fclose($fp);
		}
		else
		{
			self::$_errors[] = "Permission error. Could not open <code>.htaccess</code> for writing";
		}
		
		// config.php
		$config_file = self::$_app_config_dir.'config.php';
		
		// Load the template file
		$config_template = file(self::$_app_config_dir.'config.template.php');
		
		if (($fp = @fopen($config_file, 'w')) !== FALSE)
		{
			foreach ($config_template as $line_no => $line)
			{
				foreach ($params as $param => $value)
				{
					if (preg_match("/config\['".$param."'\].*/i", $line, $matches))
					{
						// Type check for the param values
						if (is_bool($value))
						{
							// Get the string represenation of the boolean value
							// without quotes
							$value = ($value) ? 'TRUE' : 'FALSE';
						}
						else
						{
							// Quote the value
							$value = "'".$value."'";
						}

						$replace = sprintf("config['%s'] = %s;", $param, $value);

						$line = str_replace($matches[0], $replace, $line);
						break;
					}
				}
				fwrite($fp, $line);
			}
			fclose($fp);
		}
		else
		{
			self::$_errors[] = "Permission error. Could not open <code>config.php</code> for writing";
		}
	}

	/**
	 * Updates the auth.php and encrypt.php files with
	 * the salt pattern and encryption keys
	 */
	private static function _set_security_params()
	{
		// Auth.php
		$auth_file_name = self::$_app_config_dir.'auth.php';
		
		// Load the template
		$auth_template = file(self::$_app_config_dir.'auth.template.php');

		if (($fp = fopen($auth_file_name, 'w')) !== FALSE)
		{
			// Get the salt pattern
			$salt_pattern = self::$_data['salt_pattern'];
			foreach ($auth_template as $line_no => $line)
			{
				if (preg_match("/config\['salt_pattern'\].*/i", $line, $matches))
				{
					$replace = sprintf("config['salt_pattern'] = '%s';", implode(", ", $salt_pattern));
					$line = str_replace($matches[0], $replace, $line);
				}
				fwrite($fp, $line);
			}
			fclose($fp);
		}
		else
		{
			self::$_errors[] = "Permission error. Unable to write to <code>auth.php</code>";
		}
		
		// encryption.php
		$crypto_key = Installer_Utils::get_random_str(32);
		
		$encrypt_file_name = self::$_app_config_dir.'encryption.php';
		
		// Load the template file
		$encryption_template = file(self::$_app_config_dir.'encryption.template.php');
		
		if (($fp = @fopen($encrypt_file_name, 'w')) !== FALSE)
		{
			foreach ($encryption_template as $line_no => $line)
			{
				if (preg_match("/config\['default'\]\['key'\].*/i", $line, $matches))
				{
					$replace = sprintf("config['default']['key'] = '%s';", $crypto_key);
					$line = str_replace($matches[0], $replace, $line);
				}
				fwrite($fp, $line);
			}
			fclose($fp);
		}
		else
		{
			self::$_errors[] = "Permission error. Unable to write to <code>encryption.php</code>";
		}
	}
	

	/**
	 * Setup the general site settings
	 * @return bool
	 */
	public static function install_general()
	{
		$params = array_keys(self::$_forms['general']);
		$optional = array('site_email', 'enable_clean_urls');
		
		if ( ! self::_validate_params($params, $_POST, $optional))
			return FALSE;
		
		// Clean URLs
		if (isset($_POST['enable_clean_urls']))
		{
			self::$_data['enable_clean_urls'] = (bool) $_POST['enable_clean_urls'];
		}
		
		// Validate email
		if ( ! empty($_POST['site_email']))
		{
			if ((filter_var($_POST['site_email'], FILTER_VALIDATE_EMAIL)) === FALSE)
			{
				self::$_errors[] = sprintf("Invalid email address: <strong>%s</strong>", $_POST['site_email']);
				return FALSE;
			}
		}
		
		return self::_update_settings($params);
	}
	
	/**
	 * Set up the email settings
	 * @return bool
	 */
	public static function install_email()
	{
		$params=  array_keys(self::$_forms['email']);
		if ( ! self::_validate_params($params, $_POST))
			return FALSE;
		
		return self::_update_settings($params);
	}

	/**
	 * Internal utility method to update the settings table
	 * @return bool
	 */
	private static function _update_settings($params)
	{
		$query = "UPDATE `".self::$_data['database']['table_prefix']."settings` SET `value` = CASE `key` ";
		
		// Update the site settings
		$settings_keys = array();
		foreach ($params as $param)
		{
			if ( ! isset($_POST[$param]))
				continue;
			$settings_keys[] = self::_escape_str($param);
			$query .= sprintf("WHEN '%s' THEN '%s' ", self::_escape_str($param), self::_escape_str($_POST[$param]));
		}
		
		$settings_keys = "'".implode("','", $settings_keys)."'";
		$query .= sprintf('END WHERE `key` IN (%s)', $settings_keys);
		
		return self::_execute_query($query);
		
	}
	
	/**
	 * Setup the map configuration
	 */
	public static function install_map()
	{
		extract($_POST);
		$api_key_mapping = array('bing_road' => 'api_live');
		
		$exempt_providers = array(
			'google_normal',
			'osm_mapnik'
		);
		
		if (empty($default_map))
		{
			self::$_errors[] = "You have not selected a provider";
			return FALSE;
		}
		
		// Build the update query
		$table_prefix = self::$_data['database']['table_prefix'];
		
		// Running as 2 seperate updates because the CASE method is messy and pointless with just 2 keys anyway
		$query = sprintf("UPDATE `%ssettings` SET `value` = '%s' WHERE `key` = 'default_map'", $table_prefix, self::_escape_str($default_map) );
		
		$result = TRUE;
		// Check for BingMaps API Key
		if ( ! in_array($default_map, $exempt_providers))
		{
			// Check for the API key
			if ( ! empty($api_key))
			{
				$setting_id = $api_key_mapping[$default_map];
				
				$query = sprintf("UPDATE `%ssettings` SET `value` = '%s' WHERE `key` = '%s' ", $table_prefix, self::_escape_str($api_key), self::_escape_str($setting_id) );
				$result = self::_execute_query($query) AND $result;
			}
			else
			{
				self::$_errors[] = "The selected map provider requires an API key";
				return FALSE;
			}
		}
		$result = self::_execute_query($query) AND $result;
		
		return $result;
		
	}
	
	
	/**
	 * Save the admin password
	 */
	public static function install_adminpassword()
	{
		$params = array_keys(self::$_forms['adminpassword']);
		if ( ! self::_validate_params($params, $_POST))
			return FALSE;
		
		extract($_POST);
		
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)
		{
			self::$_errors[] = sprintf("Invalid email address: <strong>%s</strong>", $email);
			return FALSE;
		}
		
		// Do the passwords match?
		if (hash("sha1", $password) !== hash("sha1", $confirm_password))
		{
			self::$_errors[] = "The passwords do not match";
			return FALSE;
		}

		// Get the table prefix
		$table_prefix = self::$_data['database']['table_prefix'];
		
		// Get the salt pattern from the auth.php
		$salt_pattern = Installer_Utils::get_salt_pattern();
		self::$_data['salt_pattern'] = $salt_pattern;
		
		// Generate the password hash
		$password_hash = Installer_Utils::hash_password($password, $salt_pattern);
				
		// Update the admin user password
		$query = sprintf("UPDATE `%susers` SET `email` = '%s', `password` = '%s' WHERE `username` = 'admin'", 
			$table_prefix, self::_escape_str($email), self::_escape_str($password_hash));

		self::$_data['admin_email'] = $email;
		 
		return self::_execute_query($query);
	}

	/**
	 * Checks if clean URLS are enabled
	 *
	 * @return bool
	 */
	private static function _clean_urls_enabled()
	{
		$url = self::$_data['base_url']."installer/mod_rewrite/";
		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($curl_handle);

		$return_code = curl_getinfo($curl_handle,CURLINFO_HTTP_CODE);
		curl_close($curl_handle);

		if ($return_code ==  404 OR $return_code ==  403)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
		
}

?>
