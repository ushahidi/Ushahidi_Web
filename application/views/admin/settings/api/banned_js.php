<?php
/**
 * API banned logs js file.
 *
 * Handles javascript stuff related  to api banned function
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

<?php require SYSPATH.'../application/views/admin/utils_js.php' ?>

	// Ajax Submission
	function apiBannedAction ( action, confirmAction, api_banned_id )
	{
		var statusMessage;
		if( !isChecked( "api_banned" ) && api_banned_id =='' )
		{ 
			alert('Please select at least one banned ip.');
		} else {
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);
			
				if (api_banned_id != '') 
				{
					// Submit Form For Single Item
					$("#api_banned_single").attr("value", api_banned_id);
					$("#apiBannedMain").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#api_banned_single").attr("value", "000");
					
					// Submit Form For Multiple Items
					$("#apiBannedMain").submit();
				}
		
			} else {
				return false;
			}
		}
	}