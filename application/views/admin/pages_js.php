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
	$("#page_id").attr("value", unescape(id));
	$("#page_title").attr("value", unescape(page_title));
	$("#page_tab").attr("value", unescape(page_tab));
	$("#page_description").attr("value", 
		unescape(page_description));
	tinyMCE.getInstanceById("page_description").setContent(unescape(page_description));
}

// Ajax Submission
function pageAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Category ID
		$("#page_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#pageListing").submit();
	}
}

// Initialize tinyMCE Wysiwyg Editor
tinyMCE.init({
	mode : "exact",
	elements : "page_description",
	theme : "simple",
	toolbar_location : "top",
	height:"350px",
	width:"600px"
});