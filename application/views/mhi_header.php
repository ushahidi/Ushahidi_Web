<?php
/**
 * MHI header view page.
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


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $site_name; ?></title>
<?php
	echo html::stylesheet('media/css/mhi/reset','',true);
	echo "<!--[if lte IE 7]>".html::stylesheet('media/css/mhi/reset.ie','',true)."<![endif]-->";
	echo html::stylesheet('media/css/mhi/base','',true);
?>

<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js"></script>

<?php
	echo html::script('media/js/mhi/initialize', true);
?>

<script type="text/javascript" language="javascript">
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
				alphanumeric: true
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
				required: "Please enter your instance address."
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

	/*Validate the Form*/
	$("#frm-MHI-Account").validate({
		rules: {
			firstname: "required",
			lastname: "required",
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				rangelength: [4, 32]
			},
			confirm_password: {
				required: true,
				equalTo: "#password"
			}
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
</script>

</head>

<body class="mhi-signup content">

	<div id="header">
    	<div id="header-wrapper">
    		<h1>Multiple Hosted Instances @ <?php echo $site_name; ?></h1>
        </div>
    </div>

    <div id="wrapper">
    	<div class="twocol-left"><div class="shadow">