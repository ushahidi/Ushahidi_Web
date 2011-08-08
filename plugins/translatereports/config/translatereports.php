<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Translate Reports Config - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module     Translate Reports Config
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
 	
 	// Max New Translations - Sets the number of new languages that an admin can create at one time
 	//   Note: This will not prevent someone from going back to edit a report and add more translations
	$config['max_new_translations'] = 5;
	
	/* Languages - This is the list of languages to set in the dropdown. Leave as NULL to use the i18n
	               translations that exist for site localizations.
	   
	   Note: Use the codes found in the locale helper (application/helpers/locale.php). If the language
	         isn't represented there (some lesser known languages are not), use the three letter ISO 639-3
	         code, which can be found here: http://www.sil.org/iso639-3/codes.asp. The convention for the
	         code is like this: [langcode]_[countrycode]. The value is the name that will show up on the
	         site. 'en_US'=>'English' will show up as English in the translation drop down and on the front
	         end of the website on the reports page.
	         
	   Example Array:
             $config['languages'] = array(
							'en_US'=>'English',
							'fr_FR'=>'French',
							'luo_KE'=>'Luo',
							'kik_KE'=>'Kikuyu',
							'sw_KE'=>'Kiswahili'
							);
	*/
	$config['languages'] = NULL;
?>