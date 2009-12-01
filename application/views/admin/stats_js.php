<?php
/**
 * Stats js file.
 *
 * Handles javascript stuff related to stats function.
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
// Date Picker JS
jQuery(document).ready(function() {
	jQuery("#dp1").datepicker({ 
	    showOn: "both",
		dateFormat: "yy-mm-dd",
	    buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
	    buttonImageOnly: true 
	});
	
	jQuery("#dp2").datepicker({ 
	    showOn: "both",
		dateFormat: "yy-mm-dd",
	    buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
	    buttonImageOnly: true 
	});
});