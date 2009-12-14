<?php
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
?>
jQuery(function() {
// Ajax Validation
	jQuery("#footerfeedbackMain").validate({
		rules: {
			feedback_message: {
				required: true,
				minlength: 10
		},
			person_email: {
				required: true,
				email: true
			},
			feedback_captcha: {
				required: true
			}
		},
		messages: {
			feedback_message: {
				required: "Please enter some information for the reply.",
				minlength: "Your message must consist of at least 10 characters."
			},
			person_email: {
				required: "Please enter an Email Address",
				email: "Please enter a valid Email Address"
			},
			feedback_captcha: {
				required: "Please enter the Security Code"
			}
		}
	});
});

// Form Submission
function formSubmit ()
{
	// Submit Form
	jQuery("#footerfeedbackMain").submit();
}

// Show Function
function showForm(id)
{
	if (id) {
		jQuery('#' + id).toggle(400);
	}
}

function clearField()
{
	jQuery('#person_email').val("");
} 




