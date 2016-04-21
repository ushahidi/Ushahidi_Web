<?php
/**
 * Layers js file.
 *
 * Handles javascript stuff related to layers controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Layers JS View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
// Layers JS
function fillFields(id, layer_name, layer_url, layer_color, layer_file_old)
{
	$("#layer_id").val(decodeURIComponent(id));
	$("#layer_name").val(decodeURIComponent(layer_name));
	$("#layer_url").val(decodeURIComponent(layer_url));
	$("#layer_color").val(decodeURIComponent(layer_color));
	$("#layer_file_old").val(decodeURIComponent(layer_file_old));
}

function layerAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#layer_id_action").val(id);
		// Set Submit Type
		$("#action").val(action);
		// Submit Form
		$("#layerListing").submit();
	}
}
