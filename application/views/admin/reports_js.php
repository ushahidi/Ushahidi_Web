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
		function reportAction ( action, confirmAction, incident_id )
		{
			var statusMessage;
			if( !isChecked( "incident" ) && incident_id=='' )
			{ 
				alert('Please select at least one report.');
			} else {
				var answer = confirm('Are You Sure You Want To ' + confirmAction + ' items?')
				if (answer){
					
					// Set Submit Type
					$("#action").attr("value", action);
					
					if (incident_id != '') 
					{
						// Submit Form For Single Item
						$("#incident_single").attr("value", incident_id);
						$("#reportMain").submit();
					}
					else
					{
						// Set Hidden form item to 000 so that it doesn't return server side error for blank value
						$("#incident_single").attr("value", "000");
						
						// Submit Form For Multiple Items
						$("#reportMain").submit();
					}
				
				} else {
					return false;
				}
			}
		}
		

