<?php
/**
 * MHI Account Management JS file
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
	$("#frm-MHI-Account").validate({
		rules: {
			firstname: "required",
			lastname: "required",
			email: {
				required: true,
				email: true
			},
			account_password: {
				required: true,
				rangelength: [4, 32]
			},
			account_confirm_password: {
				required: true,
				equalTo: "#account_password"
			},
		},
		messages: {
			firstname: "Please enter your first name.",
			lastname: "Please enter your first name.",
			email: {
				required: "Please enter your email address.",
				email: "Please enter a valid email address."
			},
			password: {
				required: "Please enter a password.",
				rangelength: "Your password must be between 4 and 32 characters."
			},
			confirm_password: {
				required: "Please confirm your password.",
				equalTo: "Passwords do not match."
			}
		},
		errorPlacement: function(error, element) {
		 error.appendTo(element.parent());
	    }
	});

});