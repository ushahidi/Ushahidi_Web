<?php
/**
 * Sharing js file.
 *
 * Handles javascript stuff related to sharing controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Sharing JS View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
// Sharing JS
function fillFields(id, sharing_url, sharing_name, sharing_color)
{
	$("#sharing_id").val(decodeURIComponent(id));
	$("#sharing_name").val(decodeURIComponent(sharing_name));
	$("#sharing_url").val(decodeURIComponent(sharing_url));
	$("#sharing_color").val(decodeURIComponent(sharing_color));
}

// Ajax Submission
function sharingAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#sharing_id_action").val(id);
		// Set Submit Type
		$("#action").val(action);
		// Submit Form
		$("#sharingListing").submit();
	}
}
