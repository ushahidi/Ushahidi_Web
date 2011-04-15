<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Email helper class.
 *
 * $Id: email.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class email_Core {

	// SwiftMailer instance
	protected static $mail;

	/**
	 * Creates a SwiftMailer instance.
	 *
	 * @param   string  DSN connection string
	 * @return  object  Swift object
	 */
	public static function connect($config = NULL)
	{
		if ( ! class_exists('Swift', FALSE))
		{
			// Load SwiftMailer
			require Kohana::find_file('vendor', 'swift/Swift');

			// Register the Swift ClassLoader as an autoload
			spl_autoload_register(array('Swift_ClassLoader', 'load'));
		}

		// Load default configuration
		($config === NULL) and $config = Kohana::config('email');

		switch ($config['driver'])
		{
			case 'smtp':
				// Set port
				$port = empty($config['options']['port']) ? NULL : (int) $config['options']['port'];

				if (empty($config['options']['encryption']))
				{
					// No encryption
					$encryption = Swift_Connection_SMTP::ENC_OFF;
				}
				else
				{
					// Set encryption
					switch (strtolower($config['options']['encryption']))
					{
						case 'tls': $encryption = Swift_Connection_SMTP::ENC_TLS; break;
						case 'ssl': $encryption = Swift_Connection_SMTP::ENC_SSL; break;
					}
				}

				// Create a SMTP connection
				$connection = new Swift_Connection_SMTP($config['options']['hostname'], $port, $encryption);

				// Do authentication, if part of the DSN
				empty($config['options']['username']) or $connection->setUsername($config['options']['username']);
				empty($config['options']['password']) or $connection->setPassword($config['options']['password']);

				if ( ! empty($config['options']['auth']))
				{
					// Get the class name and params
					list ($class, $params) = arr::callback_string($config['options']['auth']);

					if ($class === 'PopB4Smtp')
					{
						// Load the PopB4Smtp class manually, due to its odd filename
						require Kohana::find_file('vendor', 'swift/Swift/Authenticator/$PopB4Smtp$');
					}

					// Prepare the class name for auto-loading
					$class = 'Swift_Authenticator_'.$class;

					// Attach the authenticator
					$connection->attachAuthenticator(($params === NULL) ? new $class : new $class($params[0]));
				}

				// Set the timeout to 5 seconds
				$connection->setTimeout(empty($config['options']['timeout']) ? 5 : (int) $config['options']['timeout']);
			break;
			case 'sendmail':
				// Create a sendmail connection
				$connection = new Swift_Connection_Sendmail
				(
					empty($config['options']) ? Swift_Connection_Sendmail::AUTO_DETECT : $config['options']
				);

				// Set the timeout to 5 seconds
				$connection->setTimeout(5);
			break;
			default:
				// Use the native connection
				$connection = new Swift_Connection_NativeMail($config['options']);
			break;
		}

		// Create the SwiftMailer instance
		return email::$mail = new Swift($connection);
	}

	/**
	 * Send an email message.
	 *
	 * @param   string|array  recipient email (and name), or an array of To, Cc, Bcc names
	 * @param   string|array  sender email (and name)
	 * @param   string        message subject
	 * @param   string        message body
	 * @param   boolean       send email as HTML
	 * @return  integer       number of emails sent
	 */
	public static function send($to, $from, $subject, $message, $html = FALSE)
	{
		// Connect to SwiftMailer
		(email::$mail === NULL) and email::connect();

		// Determine the message type
		$html = ($html === TRUE) ? 'text/html' : 'text/plain';

		// Create the message
		$message = new Swift_Message($subject, $message, $html, '8bit', 'utf-8');

		if (is_string($to))
		{
			// Single recipient
			$recipients = new Swift_Address($to);
		}
		elseif (is_array($to))
		{
			if (isset($to[0]) AND isset($to[1]))
			{
				// Create To: address set
				$to = array('to' => $to);
			}

			// Create a list of recipients
			$recipients = new Swift_RecipientList;

			foreach ($to as $method => $set)
			{
				if ( ! in_array($method, array('to', 'cc', 'bcc')))
				{
					// Use To: by default
					$method = 'to';
				}

				// Create method name
				$method = 'add'.ucfirst($method);

				if (is_array($set))
				{
					// Add a recipient with name
					$recipients->$method($set[0], $set[1]);
				}
				else
				{
					// Add a recipient without name
					$recipients->$method($set);
				}
			}
		}

		if (is_string($from))
		{
			// From without a name
			$from = new Swift_Address($from);
		}
		elseif (is_array($from))
		{
			// From with a name
			$from = new Swift_Address($from[0], $from[1]);
		}

		return email::$mail->send($message, $recipients, $from);
	}

} // End email