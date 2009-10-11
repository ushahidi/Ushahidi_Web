/**
 * Feedback Forms js file.
 *
 * Handles javascript stuff related to feedback function.
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

// Form Submission
function formSubmit ()
{
	// Submit Form
	$("#footerfeedbackMain").submit();
}

// Show Function
function showForm(id)
{
	if (id) {
		$('#' + id).toggle(400);
	}
}

function clearField()
{
	$('#person_email').val("");
} 



