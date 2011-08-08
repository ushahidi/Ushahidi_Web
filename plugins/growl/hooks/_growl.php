<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Growl Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module     Growl Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
 
 // This class was heavily inspired by and largely copied from Tyler Hall's proof of concept on http://github.com/tylerhall/php-growl

class growl {
	
	const GROWL_PRIORITY_LOW = -2;
    const GROWL_PRIORITY_MODERATE = -1;
    const GROWL_PRIORITY_NORMAL = 0;
    const GROWL_PRIORITY_HIGH = 1;
    const GROWL_PRIORITY_EMERGENCY = 2;
	
	private $appName;
    private $address;
    private $notifications;
    private $password;
    private $port;
    private $title;
    private $message;
	
	/**
	 * Registers the main event add method
	 */
	public function __construct($message='Default Message')
	{
		$settings = ORM::factory('growl')->find(1);
		$ips = $settings->ips;
		$pws = $settings->passwords;
		
		$this->appName       = Kohana::config('settings.site_name');
		$this->address       = $ips;
		$this->notifications = array();
		$this->password      = $pws;
		$this->port          = 9887;
		$this->message       = $message;
		
		// Events
		//echo Event::add('ushahidi_action.report_add', array($this, 'growlit'));
		$this->growlit();
	}
	
	public function growlit(){
		$this->addNotification('Ushahidi');
		$this->register();
		$this->notify('Ushahidi',$this->appName,$this->message,0,true);
	}
	
	public function addNotification($name, $enabled = true)
    {
        $this->notifications[] = array('name' => utf8_encode($name), 'enabled' => $enabled);
    }

    public function register()
    {
    	$data         = '';
        $defaults     = '';
        $num_defaults = 0;

        for($i = 0; $i < count($this->notifications); $i++)
        {
            $data .= pack('n', strlen($this->notifications[$i]['name'])) . $this->notifications[$i]['name'];
            if($this->notifications[$i]['enabled'])
            {
                $defaults .= pack('c', $i);
                $num_defaults++;
            }
        }

        // pack(Protocol version, type, app name, number of notifications to register)
        $data  = pack('c2nc2', 1, 0, strlen($this->appName), count($this->notifications), $num_defaults) . $this->appName . $data . $defaults;
        $data .= pack('H32', md5($data . $this->password));
        $this->send($data);
		
        return true;
    }

    public function notify($name, $title, $message, $priority = 0, $sticky = false)
    {
        $name     = utf8_encode($name);
        $title    = utf8_encode($title);
        $message  = utf8_encode($message);
        $priority = intval($priority);

        $flags = ($priority & 7) * 2;
        if($priority < 0) $flags |= 8;
        if($sticky) $flags |= 256;

        // pack(protocol version, type, priority/sticky flags, notification name length, title length, message length. app name length)
        $data = pack('c2n5', 1, 1, $flags, strlen($name), strlen($title), strlen($message), strlen($this->appName));
        $data .= $name . $title . $message . $this->appName;
        $data .= pack('H32', md5($data . $this->password));

        return $this->send($data);
    }

    private function send($data)
    {
    	if(function_exists('socket_create') && function_exists('socket_sendto'))
        {
            $sck = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_sendto($sck, $data, strlen($data), 0x100, $this->address, $this->port);
            return true;
        }
        elseif(function_exists('fsockopen'))
        {
            $fp = fsockopen('udp://' . $this->address, $this->port);
            fwrite($fp, $data);
            fclose($fp);
            return true;
        }

        return false;
    }
}

//new growl;