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
		
		$config = Kohana::config('email');
		$ssl = $config['ssl'] == true ? "ssl/novalidate-cert" : "";
		$service = "{".$config['server'].":".$config['port']."/".$config['servertype']."/".$ssl."}";
	
		$imap_stream =	imap_open($service, $config['username'],$config['password']);
		if (!$imap_stream)
			throw new Kohana_Exception('imap.imap_stream_not_opened', imap_last_error());

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
		$msgs = imap_search($this->imap_stream, $search_criteria);
		$no_of_msgs = $msgs ? count($msgs) : 0;

		$messages = array();

		for ($i = 0; $i < $no_of_msgs; $i++) 
		{
			// Get Message Unique ID in case mail box changes 
			// in the middle of this operation
			$message_id = imap_uid($this->imap_stream, $msgs[$i]);
			
			$header = imap_header($this->imap_stream, $message_id);
			$date = date($date_format, $header->udate);
			$from = $header->from;
			$fromname = "";
			$fromaddress = "";
			$subject = "";

			foreach ($from as $id => $object) 
			{
				if (isset($object->personal))
					$fromname = $object->personal;
				$fromaddress = $object->mailbox."@".$object->host;
				if ($fromname == "")
				{ // In case from object doesn't have Name
					$fromname = $fromaddress;
				}
			}

			if (isset($header->subject))
				$subject = $this->_mime_decode($header->subject);

			$structure = imap_fetchstructure($this->imap_stream, $message_id);

			if (!empty($structure->parts))
			{
				for ($j = 0, $k= count($structure->parts); $j < $k; $j++)
				{
					$part = $structure->parts[$j];

					if ($part->subtype == 'PLAIN')
					{
						$body = imap_fetchbody($this->imap_stream, $message_id, $j+1);
					}
				}
			}
			else {
				$body = imap_body($this->imap_stream, $message_id);
			}

			// Convert quoted-printable strings (RFC2045)
			$body = imap_qprint($body);
			
			array_push($messages, array('msg_no' => $message_id,
										'date' => $date,
										'from' => $fromname,
										'email' => $fromaddress,
										'subject' => $subject,
										'body' => $body));
										
			// Mark Message As Read
			imap_setflag_full($this->imap_stream, $message_id, "\\Seen");
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
