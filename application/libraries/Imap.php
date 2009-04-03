<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Imap library. Wrapper to read email using IMAP/ POP3. 
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
	public function __construct($config = array())
	{
		// Use default imap configuration when none is provided
		$config = !empty($config) ? $config : Kohana::config('imap');
		
		$imap_stream =	imap_open($config['service'], $config['email_address'], 
						   $config['password']);
		if (!$imap_stream)
			throw new Kohana_Exception('imap.imap_stream_not_opened', imap_last_error());

		$this->imap_stream = $imap_stream;
	}

	/**
	 * Create an instance of Imap
	 *
     * @param   string  name of service. Either 'imap' or 'pop3'
	 * @return	object
	 */
	public static function factory($service)
	{
		if ($service == 'imap')
			return new Imap(Kohana::config('imap'));
		elseif ($service == 'pop3')
			return new Imap(Kohana::config('pop3'));
		else
			throw new Kohana_Exception('imap.unsupported_service', $service);
	}

	/**
	 * Get messages according to a search criteria
	 * 
	 * @param	string	search criteria (RFC2060, sec. 6.4.4). Set to "UNSEEN" by default
	 					NB: Search criteria only affects IMAP mailboxes.
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
			$header = imap_header($this->imap_stream, $msgs[$i]);
			$date = date($date_format, $header->udate);
			$from = $this->_mime_decode($header->fromaddress);
			$subject = $this->_mime_decode($header->subject);

			$structure = imap_fetchstructure($this->imap_stream, $msgs[$i]);

			if (!empty($structure->parts))
			{
				for ($j = 0, $k= count($structure->parts); $j < $k; $j++)
				{
					$part = $structure->parts[$j];

					if ($part->subtype == 'PLAIN')
					{
						$body = imap_fetchbody($this->imap_stream, $msgs[$i], $j+1);
					}
				}
			}
			else {
				$body = imap_body($this->imap_stream, $msgs[$i]);
			}

			// Convert quoted-printable strings (RFC2045)
			$body = imap_qprint($body);
			
			array_push($messages, array('msg_no' => $msgs[$i],
										'date' => $date,
										'from' => $from,
										'subject' => $subject,
										'body' => $body));
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
