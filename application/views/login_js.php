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
	
	// If we aren't showing OpenID, we should just go ahead and display the userpass login form
	
	if(kohana::config('config.allow_openid') == false) {
		echo '$("#signin_userpass").show(0);';
	}
	
	?>
});
function toggle(thisDiv) {
	$("#"+thisDiv).toggle(400);
}
function facebook_click() {
	top.location.href = "<?php echo url::site()."login/facebook" ;?>"
}