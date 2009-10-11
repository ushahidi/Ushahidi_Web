<?php
/**
 * Help view js file.
 * 
 * Handles javascript stuff related to help view function.
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
			/*
			Send Message JS
			*/			
			// Ajax Validation
			$("#sendMessage").validate({
				rules: {
					name: {
						required: true,
						minlength: 3
					},
					email: {
						email: true
					},
					phone: {
						minlength: 3
					},
					message: {
						required: true,
						minlength: 3
					},
					captcha: {
						required: true
					}
				},
				messages: {
					name: {
						required: "Please enter your Name",
						minlength: "Your Name must consist of at least 3 characters"
					},
					email: {
						required: "Please enter an Email Address",
						email: "Please enter a valid Email Address"
					},
					phone: {
						minlength: "Your Phone number is invalid"
					},
					message: {
						required: "Please enter a Comment",
						minlength: "Your Comment must be at least 3 characters long"
					},
					captcha: {
						required: "Please enter the Security Code"
					}
				}
			});
		});