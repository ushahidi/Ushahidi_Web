/**
 * Organization js file.
 *
 * Handles javascript stuff related to organization function.
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
 
// Organizations JS
function fillFields(id, organization_name, organization_website,
 organization_description, organization_email, organization_phone1, organization_phone2 )
{
	$("#organization_id").attr("value", unescape(id));
	$("#organization_name").attr("value", unescape(organization_name));
	$("#organization_website").attr("value", unescape(organization_website));
	$("#organization_description").attr("value", 
		unescape(organization_description));
	$("#organization_email").attr("value", unescape(organization_email));
	$("#organization_phone1").attr("value", unescape(organization_phone1));
	$("#organization_phone2").attr("value", unescape(organization_phone2));
}

// Ajax Submission
function orgAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Category ID
		$("#org_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#orgListing").submit();
	}
}
