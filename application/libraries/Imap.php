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

		// Check if the host name is valid, if not, set imap_stream as false and return false
		if(count(dns_get_record("".Kohana::config('settings.email_host')."")) == 0)
		{
			$this->imap_stream = false;
			return false;
		}

		if ( $imap_stream = @imap_open($service, Kohana::config('settings.email_username')
			,Kohana::config('settings.email_password')))
		{

			$this->imap_stream = $imap_stream;

		} else {
			// We don't usually want to break the entire scheduler process if email settings are off
			//   so lets return false instead of halting the entire script with a Kohana Exception.

			$this->imap_stream = false;
			return false;

			//throw new Kohana_Exception('imap.imap_stream_not_opened', $throwing_error);
		}
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
		global $htmlmsg,$plainmsg,$attachments;
		
		// If our imap connection failed earlier, return no messages

		if($this->imap_stream == false)
		{
			return array();
		}

		// Use imap_search() to find the 'UNSEEN' messages.
		// This is more efficient than previous code using imap_num_msg()
		$new_msgs = imap_search($this->imap_stream, 'UNSEEN');
		$max_imap_messages = Kohana::config('email.max_imap_messages');

		if ($new_msgs == null)
		{
			return array();
		}

		// Check to see if the number of messages we want to sort through is greater than
		//   the number of messages we want to allow. If there are too many messages, it
		//   can fail and that's no good.
		$msg_to_pull = sizeof($new_msgs);
		
		// This check has had problems in the past
		if($msg_to_pull > $max_imap_messages)
		{
			$msg_to_pull = $max_imap_messages;
		}

		$messages = array();

		for ($msgidx = 0; $msgidx < $msg_to_pull; $msgidx++)
		{
			$msgno = $new_msgs[$msgidx];
			$header = imap_headerinfo($this->imap_stream, $msgno);

			if( ! isset($header->message_id) OR ! isset($header->udate))
			{
				continue;
			}
			
			// Skip messages that aren't new/unseen
			// not sure we need this check now we use imap_search to pull only UNSEEN
			if ($header->Unseen != 'U' AND $header->Recent != 'N')
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
			$body = "";
			$attachments = "";

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

			// Fetch Body
			$this->_getmsg($this->imap_stream, $msgno);
			
			if ($htmlmsg)
			{
				// Convert HTML to Text
				$html2text = new Html2Text($htmlmsg);
				$htmlmsg = $html2text->get_text();
			}
			$body = ($plainmsg) ? $plainmsg : $htmlmsg;
			
			// Fetch Attachments
			$attachments = $this->_extract_attachments($this->imap_stream, $msgno);

			// This isn't the perfect solution but windows-1256 encoding doesn't work with mb_detect_encoding()
			//   so if it doesn't return an encoding, lets assume it's arabic. (sucks)
			if(mb_detect_encoding($body, 'auto', true) == '')
			{
				$body = iconv("windows-1256", "UTF-8", $body);
			}

			// Convert to valid UTF8
			$detected_encoding = mb_detect_encoding($body, "auto");
			if($detected_encoding == 'ASCII') $detected_encoding = 'iso-8859-1';
			$body = htmlentities($body,NULL,$detected_encoding);
			$subject = htmlentities(strip_tags($subject),NULL,'UTF-8');

			array_push($messages, array('message_id' => $message_id,
										'date' => $date,
										'from' => $fromname,
										'email' => $fromaddress,
										'subject' => $subject,
										'body' => $body,
										'attachments' => $attachments));

			// Mark Message As Read
			imap_setflag_full($this->imap_stream, $msgno, "\\Seen");
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
		// Dump imap errors to avoid 'Mailbox is empty' errors 
		$error = imap_errors();
		@imap_close($this->imap_stream);
	}

	private function _mime_decode($str)
	{
		$elements = imap_mime_header_decode($str);
		$text = "";

		foreach ($elements as $element)
		{
			
			// Make sure Arabic characters can be passed through as UTF-8
			
			if(strtoupper($element->charset) == 'WINDOWS-1256'){
				$element->text = iconv("windows-1256", "UTF-8", $element->text);
			}
			
			$text.= $element->text;
		}

		return $text;
	}
	
	/**
	 * Extract Attachments from Email
	 */
	private function _extract_attachments($connection, $message_number) {

		$attachments = array();
		$structure = imap_fetchstructure($connection, $message_number);

		if(isset($structure->parts) && count($structure->parts)) {

			for($i = 0; $i < count($structure->parts); $i++) {

				$attachments[$i] = array(
					'is_attachment' => false,
					'file_name' => '',
					'name' => '',
					'type' => 0,
					'subtype' => '',
					'attachment' => ''
				);

				// Use the type and subtype to resolve the attachments.
				// Previously used of the file name extension but found that different phone models (when sending MMS to email),
				// different carriers (when sending MMS to email), and different email clients
				// do not reliably add extensions or even provide a sane filename.
				// However, they all set the content type correctly and PHP was able to identify the mime type as image/*.
				
				$attachments[$i]['type'] = $structure->parts[$i]->type;
				$attachments[$i]['subtype'] = $structure->parts[$i]->subtype;

				if($structure->parts[$i]->ifdparameters) {
					foreach($structure->parts[$i]->dparameters as $object) {
						if(strtolower($object->attribute) == 'filename') {
							$attachments[$i]['is_attachment'] = true;
							$attachments[$i]['file_name'] = $object->value;
						}
					}
				}

				if($structure->parts[$i]->ifparameters) {
					foreach($structure->parts[$i]->parameters as $object) {
						if(strtolower($object->attribute) == 'name') {
							$attachments[$i]['is_attachment'] = true;
							$attachments[$i]['name'] = $object->value;
						}
					}
				}

				if($attachments[$i]['is_attachment']) {
					$attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i+1);
					if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
						$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
					}
					elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
						$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
					}
				}

			}

		}
		
		$valid_attachments = array();
		foreach ($attachments as $attachment)
		{
			$file_content = $attachment['attachment'];
			
			// Don't accept images smaller that 12.5k
			// When MMS is sent to an email address, sometimes the source
			// carrier wraps the message and the image into html with some 
			// embedded GIFs. This tries to filter them out
			if (strlen($file_content) < 12500) {
				continue;
			}
			
			$file_type = $attachment['type'];
			$file_extension = $attachment['subtype'];
			
			if ($file_extension == 'JPEG')
			{
				$file_extension = '.JPG';
			}
			else
			{
				$file_extension = '.' . $file_extension;
			}
			
			$new_file_name = time()."_".$this->_random_string(10); // Included rand so that files don't overwrite each other
			$valid_attachments[] = $this->_save_attachments($file_type, $new_file_name, $file_content, $file_extension);
		}

		// Remove Nulls
		return array_filter($valid_attachments);
	}
	
	
	/**
	 * Save Attachments to Upload Folder
	 * Right now we only accept gif, png and jpg files
	 */	
	private function _save_attachments($file_type,$file_name,$file_content,$file_extension)
	{
	  // $file_type == 5 is image, == 6 is video...  see:
	  // http://us.php.net/manual/en/function.imap-fetchstructure.php
	  if ($file_type == 5)
		{
			$attachments = array();
			$file = Kohana::config("upload.directory")."/".$file_name.$file_extension;
			$fp = fopen($file, "w");
			fwrite($fp, $file_content);
			fclose($fp);
			
			// IMAGE SIZES: 800X600, 400X300, 89X59
			
			// Large size
			Image::factory($file)->resize(800,600,Image::AUTO)
				->save(Kohana::config('upload.directory', TRUE).$file_name.$file_extension);

			// Medium size
			Image::factory($file)->resize(400,300,Image::HEIGHT)
				->save(Kohana::config('upload.directory', TRUE).$file_name."_m".$file_extension);
			
			// Thumbnail
			Image::factory($file)->resize(89,59,Image::HEIGHT)
				->save(Kohana::config('upload.directory', TRUE).$file_name."_t".$file_extension);
				
			$attachments[] = array(
					$file_name.$file_extension,
					$file_name."_m".$file_extension,
					$file_name."_t".$file_extension
				);
			return $attachments;
		}
		else
		{
			return null;
		}
	}
	
	// Random Character String
	private function _random_string($length)
	{
		$random = "";
		srand((double)microtime()*1000000);
		$char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$char_list .= "abcdefghijklmnopqrstuvwxyz";
		$char_list .= "1234567890";
		// Add the special characters to $char_list if needed

		for($i = 0; $i < $length; $i++)
		{
			$random .= substr($char_list,(rand()%(strlen($char_list))), 1);
		}
		
		return $random;
	}
	
	private function _getmsg($mbox,$mid)
	{
		// input $mbox = IMAP stream, $mid = message id
		// output all the following:
		global $htmlmsg,$plainmsg,$attachments;
		// the message may in $htmlmsg, $plainmsg, or both
		$htmlmsg = $plainmsg = '';
		$attachments = array();

		// HEADER
		$h = imap_header($mbox,$mid);
		// add code here to get date, from, to, cc, subject...

		// BODY
		$s = imap_fetchstructure($mbox,$mid);
		if (@!$s->parts)	 // not multipart
			$this->_getpart($mbox,$mid,$s,0);  // no part-number, so pass 0
		else {	// multipart: iterate through each part
			foreach ($s->parts as $partno0=>$p)
				$this->_getpart($mbox,$mid,$p,$partno0+1);
		}
	}

	private function _getpart($mbox,$mid,$p,$partno)
	{
		// $partno = '1', '2', '2.1', '2.1.3', etc if multipart, 0 if not multipart
		global $htmlmsg,$plainmsg,$attachments;

		// DECODE DATA
		$data = ($partno)?
			imap_fetchbody($mbox,$mid,$partno):	 // multipart
			imap_body($mbox,$mid);	// not multipart
		// Any part may be encoded, even plain text messages, so check everything.
		if ($p->encoding==4)
			$data = quoted_printable_decode($data);
		elseif ($p->encoding==3)
			$data = base64_decode($data);
		// no need to decode 7-bit, 8-bit, or binary

		// TEXT
		if ($p->type==0 && $data) {
			// Messages may be split in different parts because of inline attachments,
			// so append parts together with blank row.
			if (strtolower($p->subtype)=='plain')
				$plainmsg .= trim($data) ."\n\n";
			else
				$htmlmsg .= $data ."<br><br>";
		}

		// EMBEDDED MESSAGE
		// Many bounce notifications embed the original message as type 2,
		// but AOL uses type 1 (multipart), which is not handled here.
		// There are no PHP functions to parse embedded messages,
		// so this just appends the raw source to the main message.
		elseif ($p->type==2 && $data)
		{
			$plainmsg .= trim($data) ."\n\n";
		}

		// SUBPART RECURSION
		if (isset($p->parts))
		{
			foreach ($p->parts as $partno0=>$p2)
				$this->_getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
		}
	}
} // End Imap
