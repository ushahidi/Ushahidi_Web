/**
 * Main reports js file.
 * 
 * Handles javascript stuff related to reports function.
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

<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>
// Ajax Submission
function feedbackAction ( action, confirmAction, feedback_id )
{
	var statusMessage;
	if( !isChecked( "feedback" ) && feedback_id=='' )
	{ 
		alert('Please select at least one feedback item.');
	} else {
		var answer = confirm('Are You Sure You Want To ' + confirmAction + ' items?')
		if (answer){
			
			// Set Submit Type
			$("#action").attr("value", action);
					
			if (feedback_id != '') 
			{
				// Submit Form For Single Item
				$("#feedback_single").attr("value",feedback_id);
				$("#feedbackMain").submit();
			}
			else
			{
				
				// Set Hidden form item to 000 so that it doesn't return server side error for blank value
				$("#feedback_single").attr("value", "000");
				// Submit Form For Multiple Items				
				$("#feedbackMain").submit();
			}
				
		} else {
			return false;
		}
	}
}
		



