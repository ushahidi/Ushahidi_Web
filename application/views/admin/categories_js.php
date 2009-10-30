/**
 * Categories js file.
 * 
 * Handles javascript stuff related to category function.
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
function fillFields(id, parent_id, category_title, category_description, category_color, locale)
{
	$("#category_id").attr("value", unescape(id));
	$("#parent_id").attr("value", unescape(parent_id));
	$("#category_title").attr("value", unescape(category_title));
	$("#category_description").attr("value", unescape(category_description));
	$("#category_color").attr("value", unescape(category_color));
	$("#locale").attr("value", unescape(locale));
}

// Ajax Submission
function catAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction)
	if (answer){
		// Set Category ID
		$("#category_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#catListing").submit();
	}
}