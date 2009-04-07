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
		// Categories JS
		function fillFields(id, username, name, role, email)
		{
			$("#user_id").attr("value", unescape(id));
			$("#username").attr("value", unescape(username));
			$("#name").attr("value", unescape(name));
			$('#role').attr("value",unescape( role ) );
			$('#email').attr("value",unescape( email ) );
			
		}
		
		// Form Submission
		function userAction ( action, confirmAction, id )
		{
			var statusMessage;
			var answer = confirm('Are You Sure You Want To ' 
				+ confirmAction + ' users?')
			if (answer){
				// Set Category ID
				$("#user_id").attr("value", id);
				// Set Submit Type
				$("#action").attr("value", action);		
				// Submit Form
				$("#userMain").submit();			

			}
		}