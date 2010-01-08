<?php
/**
 * Reporter js file.
 *
 * Handles javascript stuff related to reporter function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Pages Javascript
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
// Reporter JS
function fillFields(id,incident_id,location_id,user_id,service_id,level_id,service_userid,service_account,reporter_first,reporter_last,reporter_email,reporter_phone,reporter_ip,reporter_date)
{

	$("#reporter_id").attr("value", unescape(id));
	$("#incident_id").attr("value", unescape(incident_id));
	$("#location_id").attr("value", unescape(location_id));
	$("#user_id").attr("value", unescape(user_id));
	$("#service_id").attr("value", unescape(service_id));
	$("#level_id").attr("value", unescape(level_id));
	$("#service_userid").attr("value", unescape(service_userid));
	$("#service_account").attr("value", unescape(service_account));
	$("#reporter_first").attr("value", unescape(reporter_first));
	$("#reporter_last").attr("value", unescape(reporter_last));
	$("#reporter_email").attr("value", unescape(reporter_email));
	$("#reporter_phone").attr("value", unescape(reporter_phone));
	$("#reporter_ip").attr("value", unescape(reporter_ip));
	$("#reporter_date").attr("value", unescape(reporter_date));

}

// Ajax Submission
function reporterAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Reporter ID
		$("#rptr_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#rptrListing").submit();
	}
}


