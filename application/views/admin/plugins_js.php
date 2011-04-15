<?php
/**
 * Plugins js file.
 *
 * Handles javascript stuff related  to comments function
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Plugins_JS
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>

function pluginAction ( action, pluginAction, plugin_id )
{
	var statusMessage;
	if( !isChecked( "plugin" ) && plugin_id=='' )
	{ 
		alert('Please select at least one plugin.');
	} else {
		var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + pluginAction + '?')
		if (answer){
			// Set Submit Type
			$("#action").attr("value", action);
		
			if (plugin_id != '') 
			{
				// Submit Form For Single Item
				$("#plugin_single").attr("value", plugin_id);
				$("#pluginMain").submit();
			}
			else
			{
				// Set Hidden form item to 000 so that it doesn't return server side error for blank value
				$("#plugin_single").attr("value", "000");
				// Submit Form For Multiple Items
				$("#pluginMain").submit();
			}
	
		} else {
			return false;
		}
	}
}