<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Browser Detection Hook
 * Performs two functions:
 * 1. Detects if the browser can handle gzip compressed files
 *    This way we can pre-gzip files if necessary
 * 2. Detects if this is a mobile browser
 *    If so, we can choose to redirect to a mobile controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Browser Hoook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

// Detect Gzip
$gz = "";
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
{
	$gz  = strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false ? 'gz.' : '';
}
Kohana::config_set('settings.gz', $gz);

// Detect Browser
function detect_mobile_device()
{
	// check if the user agent value claims to be windows but not windows mobile
	if(stristr($_SERVER['HTTP_USER_AGENT'],'windows')&&!stristr($_SERVER['HTTP_USER_AGENT'],'windows ce'))
	{
		return false;
	}
	// check if the user agent gives away any tell tale signs it's a mobile browser
	if(preg_matcH('/up.browser|up.link|windows ce|iemobile|mini|iphone|ipod|android|danger|blackberry|mmp|symbian|midp|wap|phone|pocket|mobile|pda|psp/i',$_SERVER['HTTP_USER_AGENT']))
	{
		return true;
	}
	// check the http accept header to see if wap.wml or wap.xhtml support is claimed
	if (isset($_SERVER['HTTP_ACCEPT']))
	{
		if(stristr($_SERVER['HTTP_ACCEPT'],'text/vnd.wap.wml')||stristr($_SERVER['HTTP_ACCEPT'],'application/vnd.wap.xhtml+xml'))
		{
			return true;
		}
	}
	// check if there are any tell tales signs it's a mobile device from the _server headers
	if(isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])||isset($_SERVER['X-OperaMini-Features'])||isset($_SERVER['UA-pixels']))
	{
		return true;
	}
	// build an array with the first four characters from the most common mobile user agents
	$a = array('acs-','alav','alca','amoi','andr','audi','aste','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','ipho','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','opwv','palm','pana','pant','pdxg','phil','play','pluc','port','prox','qtek','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','w3c','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');
	// check if the first four characters of the current user agent are set as a key in the array
	if(isset($a[substr($_SERVER['HTTP_USER_AGENT'],0,4)]))
	{
		return true;
	}
}

// If Mobile Configure Mobile Settings
if(isset($_SERVER['HTTP_USER_AGENT'])
 	&& detect_mobile_device())
{
	Kohana::config_set('settings.mobile', TRUE);
}
else
{
	Kohana::config_set('settings.mobile', FALSE);
}