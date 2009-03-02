<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * utf8::strlen
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _strlen($str)
{
	// Try mb_strlen() first because it's faster than combination of is_ascii() and strlen()
	if (SERVER_UTF8)
		return mb_strlen($str);

	if (utf8::is_ascii($str))
		return strlen($str);

	return strlen(utf8_decode($str));
}