/**
 * Users js file.
 *
 * Handles javascript stuff related to users function
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
		// Users JS
		function fillFields(id, username, name, role, email)
		{
			$("#user_id").attr("value", decodeURIComponent(id));
			$("#username").attr("value", decodeURIComponent(username));
			$("#name").attr("value", decodeURIComponent(name));
			$('#role').attr("value", decodeURIComponent( role ) );
			$('#email').attr("value", decodeURIComponent( email ) );
			
		}
		
		// Form Submission
		function userAction ( action, confirmAction, id )
		{
			var statusMessage;
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' 
				+ confirmAction + ' user with ID: ' + id + '?')
			if (answer){
				// Set User ID
				$("#user_id_action").attr("value", id);
				// Set Submit Type
				$("#action").attr("value", action);		
				// Submit Form
				$("#userMain").submit();			

			}
		}