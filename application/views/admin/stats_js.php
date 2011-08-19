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
	    buttonImage: "<?php echo url::file_loc('img'); ?>media/img/icon-calendar.gif", 
	    buttonImageOnly: true 
	});
	
	jQuery("#dp2").datepicker({ 
	    showOn: "both",
		dateFormat: "yy-mm-dd",
	    buttonImage: "<?php echo url::file_loc('img'); ?>media/img/icon-calendar.gif", 
	    buttonImageOnly: true 
	});
	
	
<?php
	// Prevent an HTTP call if auto upgrading isn't enabled and
	//   make sure we are looking at the dashboard
	if (Kohana::config('config.enable_auto_upgrader') == TRUE
		AND Router::$controller == 'dashboard'){
?>

// Check for a new version of the Ushahidi Software
jQuery(document).ready(function() {
	// Check if we need to upgrade this deployment of Ushahidi
	//   if we're on the dashboard, check for a new version
	jQuery.get("<?php echo url::base().'admin/upgrade/check_current_version' ?>", function(data){
			jQuery('#need_to_upgrade').html(data);
			jQuery('#need_to_upgrade').removeAttr("style");
		});
		
});

<?php
	}
?>
	
	
});