<?php
/**
 * MHI Signup JS file
 * 
 * Non-clustered map rendering (Please refer to main_cluster_js for Server Side Clusters)
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
			signup_first_name: "required",
			signup_last_name: "required",
			signup_email: {
				required: true,
				email: true
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
				rangelength: [4, 32]
			},
			signup_instance_name: "required",
			signup_instance_tagline: "required",
			signup_report_categories: {
				required: true,
				//csv: true
			}
		},
		messages: {
			signup_first_name: "Please enter your first name.",
			signup_last_name: "Please enter your first name.",
			signup_email: {
				required: "Please enter your email address.",
				email: "Please enter a valid email address."
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
				required: "Please enter your instance address.",
				rangelength: "Your password must be between 4 and 32 characters."
			},
			signup_instance_name: "Please enter a name for your instance.",
			signup_instance_tagline: "Please enter a tagline for your instance.",
			signup_report_categories: {
				required: "Please enter at least one category for your instance."
			}

		},
		errorPlacement: function(error, element) {
		 error.appendTo(element.parent());
	    }
	});
});