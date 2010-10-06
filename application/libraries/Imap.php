<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Imap library. Wrapper to read email using IMAP/POP3.
 * @package    Imap
 * @author	   Ushahidi Team
 * @copyright  (c) 2009 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Imap_Core {

	private $imap_stream;

	/**
	 * Opens an IMAP stream
	 */
	public function __construct()
	{
		// Set Imap Timeouts
		imap_timeout(IMAP_OPENTIMEOUT,90);
		imap_timeout(IMAP_READTIMEOUT,90);


		// If SSL Enabled
		$ssl = Kohana::config('settings.email_ssl') == true ? "/ssl" : "";

		// Do not validate certificates (TLS/SSL server)
		//$novalidate = strtolower(Kohana::config('settings.email_servertype')) == "imap" ? "/novalidate-cert" : "";
		$novalidate = "/novalidate-cert";

		// If POP3 Disable TLS
		$notls = strtolower(Kohana::config('settings.email_servertype')) == "pop3" ? "/notls" : "";

		/*
		More Info about above options at:
		http://php.net/manual/en/function.imap-open.php
		*/


		$service = "{".Kohana::config('settings.email_host').":"
			.Kohana::config('settings.email_port')."/"
			.Kohana::config('settings.email_servertype')
			.$notls.$ssl.$novalidate."}";

		$imap_stream =	imap_open($service, Kohana::config('settings.email_username')
			,Kohana::config('settings.email_password'));

		if (!$imap_stream)
		{
			throw new Kohana_Exception('imap.imap_stream_not_opened', imap_last_error());
		}

		$this->imap_stream = $imap_stream;
	}

	/**
	 * Get messages according to a search criteria
	 *
	 * @param	string	search criteria (RFC2060, sec. 6.4.4). Set to "UNSEEN" by default
	 *					NB: Search criteria only affects IMAP mailboxes.
	 * @param	string	date format. Set to "Y-m-d H:i:s" by default
	 * @return	mixed	array containing messages
	 */
	public function get_messages($search_criteria="UNSEEN",
								 $date_format="Y-m-d H:i:s")
	{
		$no_of_msgs = imap_num_msg($this->imap_stream);
		$max_imap_messages = Kohana::config('email.max_imap_messages');

		// Check to see if the number of messages we want to sort through is greater than
		//   the number of messages we want to allow. If there are too many messages, it
		//   can fail and that's no good.
		$msg_to_pull = $no_of_msgs;
		if($msg_to_pull > $max_imap_messages){
			$msg_to_pull = $max_imap_messages;
		}

		$messages = array();

		for ($i = 1; $i <= $msg_to_pull; $i++)
		{
			$header = imap_headerinfo($this->imap_stream, $i);

			if( ! isset($header->message_id) OR ! isset($header->udate))
			{
				continue;
			}

			$message_id = $header->message_id;
			$date = date($date_format, $header->udate);

			if (isset($header->from))
			{
				$from = $header->from;
			}else{
				$from = FALSE;
			}

			$fromname = "";
			$fromaddress = "";
			$subject = "";

			if ($from != FALSE)
			{
				foreach ($from as $id => $object)
				{
					if (isset($object->personal))
					{
						$fromname = $object->personal;
					}

					if (isset($object->mailbox) AND isset($object->host))
					{
						$fromaddress = $object->mailbox."@".$object->host;
					}

					if ($fromname == "")
					{
						// In case from object doesn't have Name
						$fromname = $fromaddress;
					}
				}
			}

			if (isset($header->subject))
			{
				$subject = $this->_mime_decode($header->subject);
			}

			// Read the message structure
			$structure = imap_fetchstructure($this->imap_stream, $i);
			if (!empty($structure->parts))
			{
				for ($j = 0, $k= count($structure->parts); $j < $k; $j++)
				{
					$part = $structure->parts[$j];

					if ($part->subtype == 'PLAIN')
					{
						$body = imap_fetchbody($this->imap_stream, $i, $j+1);
					}
				}
			}
			else
			{
				$body = imap_body($this->imap_stream, $i);
			}

			// Convert quoted-printable strings (RFC2045)
			$body = imap_qprint($body);

			// Convert to valid UTF8
			$body = htmlentities($body);
			$subject = htmlentities($subject);

			array_push($messages, array('message_id' => $message_id,
										'date' => $date,
										'from' => $fromname,
										'email' => $fromaddress,
										'subject' => $subject,
										'body' => $body));

			// Mark Message As Read
			imap_setflag_full($this->imap_stream, $i, "\\Seen");
		}

		return $messages;
	}

	/**
	 * Delete a message
	 * @param	int	message number
	 */
	public function delete_message($msg_no)
	{
		imap_delete($this->imap_stream, $msg_no);
	}

	/**
	 * Closes an IMAP stream
	 */
	public function close()
	{
		imap_close($this->imap_stream);
	}

	private function _mime_decode($str)
	{
		$elements = imap_mime_header_decode($str);
		$text = "";

		foreach ($elements as $element)
		{
			$text.= $element->text;
		}

		return $text;
	}
} // End Imap
