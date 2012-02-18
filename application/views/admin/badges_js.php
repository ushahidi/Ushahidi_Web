/**
 * Badges js file.
 *
 * Handles javascript stuff related to badges in the admin panel.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Badges Javascript
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

function badgeAction ( action, confirmAction, badge_id )
{
	var answer = confirm('<?php echo htmlspecialchars(Kohana::lang('ui_admin.are_you_sure_you_want_to')); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#badge_id").attr("value", badge_id);
		// Set Submit Type
		$(".js_action").attr("value", action);

		// Assign
		if(action == 'b'){
			$("#assign_user").attr("value", $("#assign_user_"+badge_id).val());
		}

		// Revoke
		if(action == 'r'){
			$("#revoke_user").attr("value", $("#revoke_user_"+badge_id).val());
		}

		// Submit Form
		$("#badgeListing").submit();
	}
}

$(document).ready(function() {

	$('.badge_selection').click(function() {

		// Set form field value
		$('#selected_badge').val(this.id);
		
		// Re-add transparency to every element
		$('.badge_selection').addClass('transparent-25');

		// Remove transparency from selected element
		$(this).removeClass('transparent-25');

	});

});
