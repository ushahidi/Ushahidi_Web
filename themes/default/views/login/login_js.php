$(document).ready(function() {
    openid.init('openid_identifier');
    openid.setDemoMode(false);
	<?php

	// Depending on the action, we need to display forms by default

	if ($action == "signin" OR $action == "forgot")
	{
		echo '$("#signin_userpass").show(400);';
	}
	elseif ($action == "openid")
	{
		echo '$("#signin_openid").show(400);';
	}
	elseif ($action == "new")
	{
		echo '$("#signin_new").show(400);';
	}
	elseif ($action == 'resend_confirmation' OR isset($_GET['new_confirm_email']) OR isset($_GET['confirmation_failure']))
	{
		echo '$("#resend_confirm_email").show(0);';
	}

	// Determine which form to default to open

	if ( isset($_GET['newaccount']))
	{
		echo '$("#signin_new").show(0);';
	}
	elseif (kohana::config('config.allow_openid') == false)
	{
		echo '$("#signin_userpass").show(0);';
	}

	?>

	<?php if(kohana::config('riverid.enable') == true) { ?>
	$(".new_email").focusout(function() {

		$.getJSON('<?php echo kohana::config('config.site_domain'); ?>riverid/registered', {email: $(".new_email").val()}, function(response) {
			if (response.response) {
				$("#signin_userpass").show(0);
				$("#username").val($(".new_email").val());
				$('.new_name').attr('disabled', true);
				$('.new_password').attr('disabled', true);
				$('.new_password_again').attr('disabled', true);
				$('.new_submit').attr('disabled', true);
				$('.riverid_email_already_set_copy').html('<small>You already have an account managed by CrowdmapID! Try using your CrowdmapID email and password to login.</small>');
				$(".riverid_email_already_set").show(0);
			}else{
				$("#username").val('');
				$('.new_name').attr('disabled', false);
				$('.new_password').attr('disabled', false);
				$('.new_password_again').attr('disabled', false);
				$('.new_submit').attr('disabled', false);
				$(".riverid_email_already_set").hide(0);
			}
		});

	});
	<?php } ?>
});
function toggle(thisDiv) {
	$("#"+thisDiv).toggle(400);
}
function facebook_click() {
	top.location.href = "<?php echo url::site()."login/facebook"; ?>"
}
