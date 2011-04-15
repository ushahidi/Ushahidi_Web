<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * The Encrypt library provides two-way encryption of text and binary strings
 * using the MCrypt extension.
 * @see http://php.net/mcrypt
 *
 * $Id: Encrypt.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Encrypt_Core {

	// OS-dependant RAND type to use
	protected static $rand;

	// Configuration
	protected $config;

	/**
	 * Returns a singleton instance of Encrypt.
	 *
	 * @param   array  configuration options
	 * @return  Encrypt_Core
	 */
	public static function instance($config = NULL)
	{
		static $instance;

		// Create the singleton
		empty($instance) and $instance = new Encrypt((array) $config);

		return $instance;
	}

	/**
	 * Loads encryption configuration and validates the data.
	 *
	 * @param   array|string      custom configuration or config group name
	 * @throws  Kohana_Exception
	 */
	public function __construct($config = FALSE)
	{
		if ( ! defined('MCRYPT_ENCRYPT'))
			throw new Kohana_Exception('encrypt.requires_mcrypt');

		if (is_string($config))
		{
			$name = $config;

			// Test the config group name
			if (($config = Kohana::config('encryption.'.$config)) === NULL)
				throw new Kohana_Exception('encrypt.undefined_group', $name);
		}

		if (is_array($config))
		{
			// Append the default configuration options
			$config += Kohana::config('encryption.default');
		}
		else
		{
			// Load the default group
			$config = Kohana::config('encryption.default');
		}

		if (empty($config['key']))
			throw new Kohana_Exception('encrypt.no_encryption_key');

		// Find the max length of the key, based on cipher and mode
		$size = mcrypt_get_key_size($config['cipher'], $config['mode']);

		if (strlen($config['key']) > $size)
		{
			// Shorten the key to the maximum size
			$config['key'] = substr($config['key'], 0, $size);
		}

		// Find the initialization vector size
		$config['iv_size'] = mcrypt_get_iv_size($config['cipher'], $config['mode']);

		// Cache the config in the object
		$this->config = $config;

		Kohana::log('debug', 'Encrypt Library initialized');
	}

	/**
	 * Encrypts a string and returns an encrypted string that can be decoded.
	 *
	 * @param   string  data to be encrypted
	 * @return  string  encrypted data
	 */
	public function encode($data)
	{
		// Set the rand type if it has not already been set
		if (self::$rand === NULL)
		{
			if (KOHANA_IS_WIN)
			{
				// Windows only supports the system random number generator
				self::$rand = MCRYPT_RAND;
			}
			else
			{
				if (defined('MCRYPT_DEV_URANDOM'))
				{
					// Use /dev/urandom
					self::$rand = MCRYPT_DEV_URANDOM;
				}
				elseif (defined('MCRYPT_DEV_RANDOM'))
				{
					// Use /dev/random
					self::$rand = MCRYPT_DEV_RANDOM;
				}
				else
				{
					// Use the system random number generator
					self::$rand = MCRYPT_RAND;
				}
			}
		}

		if (self::$rand === MCRYPT_RAND)
		{
			// The system random number generator must always be seeded each
			// time it is used, or it will not produce true random results
			mt_srand();
		}

		// Create a random initialization vector of the proper size for the current cipher
		$iv = mcrypt_create_iv($this->config['iv_size'], self::$rand);

		// Encrypt the data using the configured options and generated iv
		$data = mcrypt_encrypt($this->config['cipher'], $this->config['key'], $data, $this->config['mode'], $iv);

		// Use base64 encoding to convert to a string
		return base64_encode($iv.$data);
	}

	/**
	 * Decrypts an encoded string back to its original value.
	 *
	 * @param   string  encoded string to be decrypted
	 * @return  string  decrypted data
	 */
	public function decode($data)
	{
		// Convert the data back to binary
		$data = base64_decode($data);

		// Extract the initialization vector from the data
		$iv = substr($data, 0, $this->config['iv_size']);

		// Remove the iv from the data
		$data = substr($data, $this->config['iv_size']);

		// Return the decrypted data, trimming the \0 padding bytes from the end of the data
		return rtrim(mcrypt_decrypt($this->config['cipher'], $this->config['key'], $data, $this->config['mode'], $iv), "\0");
	}

} // End Encrypt
