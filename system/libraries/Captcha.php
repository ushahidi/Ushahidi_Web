<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Captcha library.
 *
 * $Id: Captcha.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Captcha
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Captcha_Core {

	// Captcha singleton
	protected static $instance;

	// Style-dependent Captcha driver
	protected $driver;

	// Config values
	public static $config = array
	(
		'style'      => 'basic',
		'width'      => 150,
		'height'     => 50,
		'complexity' => 4,
		'background' => '',
		'fontpath'   => '',
		'fonts'      => array(),
		'promote'    => FALSE,
	);

	/**
	 * Singleton instance of Captcha.
	 *
	 * @return  object
	 */
	public static function instance()
	{
		// Create the instance if it does not exist
		empty(self::$instance) and new Captcha;

		return self::$instance;
	}

	/**
	 * Constructs and returns a new Captcha object.
	 *
	 * @param   string  config group name
	 * @return  object
	 */
	public static function factory($group = NULL)
	{
		return new Captcha($group);
	}

	/**
	 * Constructs a new Captcha object.
	 *
	 * @throws  Kohana_Exception
	 * @param   string  config group name
	 * @return  void
	 */
	public function __construct($group = NULL)
	{
		// Create a singleton instance once
		empty(self::$instance) and self::$instance = $this;

		// No config group name given
		if ( ! is_string($group))
		{
			$group = 'default';
		}

		// Load and validate config group
		if ( ! is_array($config = Kohana::config('captcha.'.$group)))
			throw new Kohana_Exception('captcha.undefined_group', $group);

		// All captcha config groups inherit default config group
		if ($group !== 'default')
		{
			// Load and validate default config group
			if ( ! is_array($default = Kohana::config('captcha.default')))
				throw new Kohana_Exception('captcha.undefined_group', 'default');

			// Merge config group with default config group
			$config += $default;
		}

		// Assign config values to the object
		foreach ($config as $key => $value)
		{
			if (array_key_exists($key, self::$config))
			{
				self::$config[$key] = $value;
			}
		}

		// Store the config group name as well, so the drivers can access it
		self::$config['group'] = $group;

		// If using a background image, check if it exists
		if ( ! empty($config['background']))
		{
			self::$config['background'] = str_replace('\\', '/', realpath($config['background']));

			if ( ! is_file(self::$config['background']))
				throw new Kohana_Exception('captcha.file_not_found', self::$config['background']);
		}

		// If using any fonts, check if they exist
		if ( ! empty($config['fonts']))
		{
			self::$config['fontpath'] = str_replace('\\', '/', realpath($config['fontpath'])).'/';

			foreach ($config['fonts'] as $font)
			{
				if ( ! is_file(self::$config['fontpath'].$font))
					throw new Kohana_Exception('captcha.file_not_found', self::$config['fontpath'].$font);
			}
		}

		// Set driver name
		$driver = 'Captcha_'.ucfirst($config['style']).'_Driver';

		// Load the driver
		if ( ! Kohana::auto_load($driver))
			throw new Kohana_Exception('core.driver_not_found', $config['style'], get_class($this));

		// Initialize the driver
		$this->driver = new $driver;

		// Validate the driver
		if ( ! ($this->driver instanceof Captcha_Driver))
			throw new Kohana_Exception('core.driver_implements', $config['style'], get_class($this), 'Captcha_Driver');

		Kohana::log('debug', 'Captcha Library initialized');
	}

	/**
	 * Validates a Captcha response and updates response counter.
	 *
	 * @param   string   captcha response
	 * @return  boolean
	 */
	public static function valid($response)
	{
		// Maximum one count per page load
		static $counted;

		// User has been promoted, always TRUE and don't count anymore
		if (self::instance()->promoted())
			return TRUE;

		// Challenge result
		$result = (bool) self::instance()->driver->valid($response);

		// Increment response counter
		if ($counted !== TRUE)
		{
			$counted = TRUE;

			// Valid response
			if ($result === TRUE)
			{
				self::instance()->valid_count(Session::instance()->get('captcha_valid_count') + 1);
			}
			// Invalid response
			else
			{
				self::instance()->invalid_count(Session::instance()->get('captcha_invalid_count') + 1);
			}
		}

		return $result;
	}

	/**
	 * Gets or sets the number of valid Captcha responses for this session.
	 *
	 * @param   integer  new counter value
	 * @param   boolean  trigger invalid counter (for internal use only)
	 * @return  integer  counter value
	 */
	public function valid_count($new_count = NULL, $invalid = FALSE)
	{
		// Pick the right session to use
		$session = ($invalid === TRUE) ? 'captcha_invalid_count' : 'captcha_valid_count';

		// Update counter
		if ($new_count !== NULL)
		{
			$new_count = (int) $new_count;

			// Reset counter = delete session
			if ($new_count < 1)
			{
				Session::instance()->delete($session);
			}
			// Set counter to new value
			else
			{
				Session::instance()->set($session, (int) $new_count);
			}

			// Return new count
			return (int) $new_count;
		}

		// Return current count
		return (int) Session::instance()->get($session);
	}

	/**
	 * Gets or sets the number of invalid Captcha responses for this session.
	 *
	 * @param   integer  new counter value
	 * @return  integer  counter value
	 */
	public function invalid_count($new_count = NULL)
	{
		return $this->valid_count($new_count, TRUE);
	}

	/**
	 * Resets the Captcha response counters and removes the count sessions.
	 *
	 * @return  void
	 */
	public function reset_count()
	{
		$this->valid_count(0);
		$this->valid_count(0, TRUE);
	}

	/**
	 * Checks whether user has been promoted after having given enough valid responses.
	 *
	 * @param   integer  valid response count threshold
	 * @return  boolean
	 */
	public function promoted($threshold = NULL)
	{
		// Promotion has been disabled
		if (self::$config['promote'] === FALSE)
			return FALSE;

		// Use the config threshold
		if ($threshold === NULL)
		{
			$threshold = self::$config['promote'];
		}

		// Compare the valid response count to the threshold
		return ($this->valid_count() >= $threshold);
	}

	/**
	 * Returns or outputs the Captcha challenge.
	 *
	 * @param   boolean  TRUE to output html, e.g. <img src="#" />
	 * @return  mixed    html string or void
	 */
	public function render($html = TRUE)
	{
		return $this->driver->render($html);
	}

	/**
	 * Magically outputs the Captcha challenge.
	 *
	 * @return  mixed
	 */
	public function __toString()
	{
		return $this->render();
	}

} // End Captcha Class