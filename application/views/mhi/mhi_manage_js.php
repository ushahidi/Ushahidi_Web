<?php
/**
 * MHI Manage JS file
 * 
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
	
    $('#deployment-filter').children().click(function(){
		// clear out all the show/hide/active classes
		$(this).siblings().removeClass("selected");
		
		// add the active class to the element clicked
		$(this).addClass("selected")
		
        // start by hidding all child divs of "sites"
        $("#deployments").children().hide();
                    
		// show the appropriate divs
		switch($(this).text()){
			case "All":
				$(".deployment").show();
			break;
			case "Active":
            	if ($(".d-active").length > 0){
                    $(".d-active").show();
                } else {
                	$(".no-results").show();
                };
                
			break
			case "Inactive":
				if ($(".d-inactive").length > 0){
                    $(".d-inactive").show();
                } else {
                	$(".no-results").show();
                };
			break;
		};
	});
	
});

$(function(){
	/*Validate the All Deployment PW Form*/
	$("#frm-MHI-Admin-PW").validate({
		rules: {
			admin_password: {
				required: true,
				rangelength: [4, 32]
			},
			confirm_admin_password: {
				required: true,
				equalTo: "#admin_password"
			}
		},
		messages: {
			admin_password: {
				required: "Please enter a password.",
				rangelength: "Your password must be between 4 and 32 characters."
			},
			confirm_admin_password: {
				required: "Please confirm your password.",
				equalTo: "Passwords do not match."
			}
		},
		errorPlacement: function(error, element) {
		 error.appendTo(element.parent());
	    }
	});
});

$(function(){
	/*Validate the All Deployment PW Form*/
	$("#frm-MHI-Admin-PW-Single").validate({
		rules: {
			admin_password: {
				required: true,
				rangelength: [4, 32]
			}
		},
		messages: {
			admin_password: {
				required: "Please enter a password.",
				rangelength: "Your password must be between 4 and 32 characters."
			}
		},
		errorPlacement: function(error, element) {
		 error.appendTo(element.parent());
	    }
	});
});