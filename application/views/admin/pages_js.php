<?php
/**
 * Pages js file.
 *
 * Handles javascript stuff related to pages function.
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
// Pages JS


function fillFields(id, page_title, page_tab,
 page_description )
{
	$("#page_id").attr("value", decodeURIComponent(id));
	page_title = decodeURIComponent(escape($.base64.decode(page_title)));
	$("#page_title").attr("value", decodeURIComponent(page_title));
	page_tab = decodeURIComponent(escape($.base64.decode(page_tab)));
	$("#page_tab").attr("value", decodeURIComponent(page_tab));
	page_description = decodeURIComponent(escape($.base64.decode(page_description)));
	$("#page_description").attr("value", decodeURIComponent(page_description));
	$("#page_description").wysiwyg("setContent",decodeURIComponent(page_description));
}

// Ajax Submission
function pageAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#page_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#pageListing").submit();
	}
}

//Load jwysiwyg editor
var hb_full ;
$(document).ready(function(){
	hb_full = $("#page_description").wysiwyg();
});

