<?php
/**
 * MHI Signup JS file
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main_JS View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
$(function(){

	/*Add alpha-numeric validation*/
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
	}, "Please use letters or numbers only.");

	/*Validate the Form*/
	$("#frm-MHI-Signup").validate({
		rules: {
			signup_first_name: {
				required: true,
				rangelength: [1, 30]
			},
			signup_last_name: {
				required: true,
				rangelength: [1, 30]
			},
			signup_email: {
				required: true,
				email: true,
				remote: {
			        url: "<?php echo url::base(); ?>mhi/checkemail/",
			        type: "post"
				}
			},
			signup_password: {
				required: true,
				rangelength: [4, 32]
			},
			signup_confirm_password: {
				required: true,
				equalTo: "#signup_password"
			},
			signup_subdomain: {
				required: true,
				alphanumeric: true,
				rangelength: [4, 32],
				remote: {
			        url: "<?php echo url::base(); ?>mhi/checksubdomain/",
			        type: "post"
				}
			},
			signup_instance_name: {
				required: true,
				rangelength: [4, 100]
			},
			signup_instance_tagline: {
				required: true,
				rangelength: [4, 100]
			},
			signup_tos: {
				required: true
			},
			verify_password: {
				required: true,
				rangelength: [4, 32]
			}
		},
		messages: {
			signup_first_name: {
				required: "Please enter your first name.",
				rangelength: "Your first name must be between 1 and 30 characters."
			},
			signup_last_name: {
				required: "Please enter your last name.",
				rangelength: "Your first last must be between 1 and 30 characters"
			},
			signup_email: {
				required: "Please enter your email address.",
				email: "Please enter a valid email address.",
				remote: "This email address has already been taken."
			},
			signup_password: {
				required: "Please enter a password.",
				rangelength: "Your password must be between 4 and 32 characters."
			},
			signup_confirm_password: {
				required: "Please confirm your password.",
				equalTo: "Passwords do not match."
			},
			signup_subdomain: {
				required: "Please enter your deployment address.",
				rangelength: "The name you use for your deployment address must be between 4 and 32 characters.",
				remote: "This subdomain has already been taken."
			},
			signup_instance_name: {
				required: "Please enter a name for your deployment.",
				rangelength: "Name must be between 4 and 100 characters."
			},
			signup_instance_tagline: {
				required: "Please enter a tagline for your deployment.",
				rangelength: "Tagline must be between 4 and 100 characters."
			},
			signup_tos: {
				required: "You must accept the Website Terms of Use."
			},
			signup_password: {
				required: "Please enter your password.",
				rangelength: "Your password is between 4 and 32 characters."
			}

		},
		errorPlacement: function(error, element) {
		 error.appendTo(element.parent());
	    }
	});
});