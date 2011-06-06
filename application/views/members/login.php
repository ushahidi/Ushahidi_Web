<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Kohana::lang('ui_main.members');?></title>
<link href="<?php echo url::base() ?>media/css/login.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo url::base() ?>media/css/openid.css" />
<script type="text/javascript" src="<?php echo url::base() ?>media/js/openid/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="<?php echo url::base() ?>media/js/openid/openid-jquery.js"></script>
<script type="text/javascript" src="<?php echo url::base() ?>media/js/openid/openid-jquery-en.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    openid.init('openid_identifier');
    openid.setDemoMode(false);
});
</script>
</head>

<body>
<div id="openid_login_container">

	<div id="ushahidi_site_name">
    	<div id="logo">
			<h1><?php echo $site_name; ?></h1>
			<span><?php echo $site_tagline; ?></span>
		</div>
    </div>

	<div id="openid_login">
		<form method="post" action="<?php echo url::base();?>members/login" id="openid_form">
			<input type="hidden" name="action" value="verify" />
			
			<?php
			if ($openid_error)
			{
				?><div id="openid_error"><?php echo $openid_error;?></div>
				<?php
			}?>
			
			<h2>Sign-in or Create New Account</h2>

			<div id="openid_choice">
				<p>Please click your account provider:</p>
				<div id="openid_btns"></div>
			</div>

			<div id="openid_input_area">
				<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
				<input id="openid_submit" type="submit" value="Sign-In"/>
			</div>
			<noscript>
				<p>OpenID is service that allows you to log-on to many different websites using a single indentity.
				Find out <a href="http://openid.net/what/">more about OpenID</a> and <a href="http://openid.net/get/">how to get an OpenID enabled account</a>.</p>
			</noscript>
		</form>
		<div id="openid_signup">
			If you don't already have an account on any of the above, <a href="https://www.myopenid.com/signup">Click here to sign up</a>
			<br /><br />
			If you've forgotten or lost your login information <a href="#">Click here to recover your account</a>
		</div>
	</div>
</div>
</body>
</html>