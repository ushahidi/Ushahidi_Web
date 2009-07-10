/**
 * Form Utilities js file
 *
 * Common form functions for admin dashboard pages.
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
 				
$(document).ready(function()
{		
	$(".hide").click(function () {
		$("#submitStatus").hide();
		return false;
	});
});

// Check All / Check None
function CheckAll( id, name )
{
	// TODO use the given name in the jQuery selector
	//$("INPUT[name='" + name + "'][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
	$("td > input:checkbox").attr('checked', $('#' + id).is(':checked'));
}

//check if a checkbox has been ticked.
function isChecked( id )
{
	//var checked = $("input[id="+id+"]:checked").length
	var checked = $("td > input:checked").length
	
	if( checked == 0 )
	return false
	
	else 
	return true
}
