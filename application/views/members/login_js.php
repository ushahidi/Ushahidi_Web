$(document).ready(function() {
    openid.init('openid_identifier');
    openid.setDemoMode(false);
	<?php
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
	?>
});
function toggle(thisDiv) {
	$("#"+thisDiv).toggle(400);
}
function facebook_click() {
	top.location.href = "<?php echo url::site()."members/login/facebook" ;?>"
}