<?php
/**
 * site settings js file.
 *
 * Handles javascript stuff related to stats function.
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     John Etherton <john@ethertontech.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Settings View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

<?php
	// Prevent an HTTP call if auto upgrading isn't enabled
	if (Kohana::config('config.enable_auto_upgrader') == TRUE){
?>

// Check for a new version of the Ushahidi Software
jQuery(document).ready(function() {
	// Check if we need to upgrade this deployment of Ushahidi
	//   if we're on the dashbboard, check for a new version
	jQuery.get("<?php echo url::base().'admin/upgrade/check_current_version' ?>", function(data){
			jQuery('#need_to_upgrade').html(data);
			jQuery('#need_to_upgrade').removeAttr("style");
		});
		
});

<?php
	}
?>