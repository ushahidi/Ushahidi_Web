<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Imap library. Provides an IMAP interface to mailboxes. 
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

		$this->imap_stream = $imap_stream;
	}

	/**
	 * Get messages according to a search criteria
	 * 
	 * @param	string	search criteria (RFC2060, sec. 6.4.4). Set to "UNSEEN" by default
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
		imap_delete($this->imap_stream, $msgs[$i]);
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
