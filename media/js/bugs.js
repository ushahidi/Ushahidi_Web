$(function(){
	$('#show_bugs').click(function() {
		$('#bug_form').toggle();
		$("input#subject").focus();
	});
	
	$('.error').hide();
});

// Ajax Submission
function validatePost()
{
	// validate and process form
	// first hide any error messages
	$('.error').hide();

	var subject = $("input#subject").val();
	if (subject == "") {
		$("label#subject_error").show();
		$("input#subject").focus();
		return false;
	}
	var yourname = $("input#yourname").val();
	if (yourname == "") {
		$("label#yourname_error").show();
		$("input#yourname").focus();
		return false;
	}
	var email = $("input#email").val();
	if (email == "") {
		$("label#email_error").show();
		$("input#email").focus();
		return false;
	}
	var phone = $("input#phone").val();
	if (phone == "") {
		$("label#phone_error").show();
		$("input#phone").focus();
		return false;
	}
	var description = $("#description").val();
	if (description == "") {
		$("label#description_error").show();
		$("input#description").focus();
		return false;
	}
	
	$("#submit").attr("disabled","disabled");
	$("#submit").val("Sending...");
	return true;
}