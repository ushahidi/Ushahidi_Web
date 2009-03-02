<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * This file contains four arrays of user agent data.  It is used by the
 * User Agent library to help identify browser, platform, robot, and
 * mobile device data. The array keys are used to identify the device
 * and the array values are used to set the actual name of the item.
 */
$config['platform'] = array
(
	'windows nt 6.0' => 'Windows Vista',
	'windows nt 5.2' => 'Windows 2003',
	'windows nt 5.0' => 'Windows 2000',
	'windows nt 5.1' => 'Windows XP',
	'windows nt 4.0' => 'Windows NT',
	'winnt4.0'       => 'Windows NT',
	'winnt 4.0'      => 'Windows NT',
	'winnt'          => 'Windows NT',
	'windows 98'     => 'Windows 98',
	'win98'          => 'Windows 98',
	'windows 95'     => 'Windows 95',
	'win95'          => 'Windows 95',
	'windows'        => 'Unknown Windows OS',
	'os x'           => 'Mac OS X',
	'intel mac'      => 'Intel Mac',
	'ppc mac'        => 'PowerPC Mac',
	'powerpc'        => 'PowerPC',
	'ppc'            => 'PowerPC',
	'cygwin'         => 'Cygwin',
	'linux'          => 'Linux',
	'debian'         => 'Debian',
	'openvms'        => 'OpenVMS',
	'sunos'          => 'Sun Solaris',
	'amiga'          => 'Amiga',
	'beos'           => 'BeOS',
	'apachebench'    => 'ApacheBench',
	'freebsd'        => 'FreeBSD',
	'netbsd'         => 'NetBSD',
	'bsdi'           => 'BSDi',
	'openbsd'        => 'OpenBSD',
	'os/2'           => 'OS/2',
	'warp'           => 'OS/2',
	'aix'            => 'AIX',
	'irix'           => 'Irix',
	'osf'            => 'DEC OSF',
	'hp-ux'          => 'HP-UX',
	'hurd'           => 'GNU/Hurd',
	'unix'           => 'Unknown Unix OS',
);

// The order of this array should NOT be changed. Many browsers return
// multiple browser types so we want to identify the sub-type first.
$config['browser'] = array
(
	'Opera'             => 'Opera',
	'MSIE'              => 'Internet Explorer',
	'Internet Explorer' => 'Internet Explorer',
	'Shiira'            => 'Shiira',
	'Firefox'           => 'Firefox',
	'Chimera'           => 'Chimera',
	'Phoenix'           => 'Phoenix',
	'Firebird'          => 'Firebird',
	'Camino'            => 'Camino',
	'Netscape'          => 'Netscape',
	'OmniWeb'           => 'OmniWeb',
	'Safari'            => 'Safari',
	'Konqueror'         => 'Konqueror',
	'Epiphany'          => 'Epiphany',
	'Galeon'            => 'Galeon',
	'Mozilla'           => 'Mozilla',
	'icab'              => 'iCab',
	'lynx'              => 'Lynx',
	'links'             => 'Links',
	'hotjava'           => 'HotJava',
	'amaya'             => 'Amaya',
	'IBrowse'           => 'IBrowse',
);

$config['mobile'] = array
(
	'mobileexplorer' => 'Mobile Explorer',
	'openwave'       => 'Open Wave',
	'opera mini'     => 'Opera Mini',
	'operamini'      => 'Opera Mini',
	'elaine'         => 'Palm',
	'palmsource'     => 'Palm',
	'digital paths'  => 'Palm',
	'avantgo'        => 'Avantgo',
	'xiino'          => 'Xiino',
	'palmscape'      => 'Palmscape',
	'nokia'          => 'Nokia',
	'ericsson'       => 'Ericsson',
	'blackBerry'     => 'BlackBerry',
	'motorola'       => 'Motorola',
);

// There are hundreds of bots but these are the most common.
$config['robot'] = array
(
	'googlebot'   => 'Googlebot',
	'msnbot'      => 'MSNBot',
	'slurp'       => 'Inktomi Slurp',
	'yahoo'       => 'Yahoo',
	'askjeeves'   => 'AskJeeves',
	'fastcrawler' => 'FastCrawler',
	'infoseek'    => 'InfoSeek Robot 1.0',
	'lycos'       => 'Lycos',
);