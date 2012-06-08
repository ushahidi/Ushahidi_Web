/**
 * Alerts js file.
 * 
 * Handles javascript stuff related to alerts function.
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

<?php require SYSPATH.'../application/views/admin/utils_js.php' ?>

		// Ajax Submission
		function alertAction ( action, confirmAction, alert_id )
		{
			var statusMessage;
			if( !isChecked( "alert" ) && alert_id=='' )
			{ 
				alert('Please select at least one alert.');
			} else {
				var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
				if (answer){
					
					// Set Submit Type
					$("#alertMain #action").attr("value", action);
					
					if (alert_id != '') 
					{
						// Submit Form For Single Item
						$("#alert_single").attr("value", alert_id);
						$("#alertMain").submit();
					}
					else
					{
						// Set Hidden form item to 000 so that it doesn't return server side error for blank value
						$("#alert_single").attr("value", "000");
						
						// Submit Form For Multiple Items
						$("#alertMain").submit();
					}
				
				} else {
					return false;
				}
			}
		}