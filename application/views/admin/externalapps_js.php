/**
 * External Apps js file.
 *
 * Handles javascript stuff related to badges in the admin panel.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     External Apps Javascript
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

function appAction ( action, confirmAction, externalapp_id )
{
	var answer = confirm('<?php echo htmlspecialchars(Kohana::lang('ui_admin.are_you_sure_you_want_to')); ?> ' + confirmAction + '?')
	if (answer){

		// Set External App ID
		$("#id").attr("value", externalapp_id);

		// Set Submit Type
		$("#action").attr("value", action);

		// Submit Form
		$("#externalappMain").submit();

	}
}
