<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $error ?></title>
<?php
echo html::stylesheet('media/css/error','',true);
echo html::script('media/js/jquery', true);
echo html::script('media/js/jquery.ui.min', true);
echo html::script('media/js/bugs', true);
?>
</head>
<body>
<div id="ushahidi_login_logo"><img src="<?php echo url::file_loc('img'); ?>media/img/admin/logo_login.gif" /></div>
<div id="framework_error" style="width:42em;margin:20px auto;">
<h3><?php echo html::specialchars($error) ?></h3>
<p><?php echo html::specialchars($description) ?></p>
<?php if ( ! empty($line) AND ! empty($file)): ?>
<p><?php echo Kohana::lang('core.error_file_line', array($file, $line)) ?></p>
<?php endif ?>
<p><code class="block"><?php echo $message ?></code></p>
<p class="ushahidi_bugs"><?php echo Kohana::lang('core.report_bug',"#"); ?>
</p>
<div id="loader"></div>
<?php
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
$url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
$ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

$environ = "";
$environ .= "*URL*: ".$url."\n";
$environ .= "*REFERER*: ".$referer."\n";
$environ .= "*USER_AGENT*: ".$user_agent."\n";
$environ .= "*IP*: ".$ip_address."\n";
$environ .= "*USHAHIDI VERSION*: ".Kohana::config('version.ushahidi_version')."\n";
$environ .= "*DB VERSION*: ".Kohana::config('version.ushahidi_db_version')."\n";

$error_message = "";
if ( ! empty($file))
{
	$error_message .= "FILE: ".$file."\n";
}
if ( ! empty($line))
{
	$error_message .= "LINE: ".$line."\n";
}
$error_message .= "ERROR: ".$message."\n";
?>
<div id="bug_form">
	<p class="bug_form_desc">Found a bug? Please fill out and submit the form below - help us make Ushahidi better software -- Thanks!</p>
	<p class="bug_form_desc">All fields are required!</p>
	<table width="100%" border="0" cellspacing="0" cellpadding="6">
		<form method="post" action="http://bugs.ushahidi.com" id="form" onSubmit="return validatePost();">
			<input name="tracker" type="hidden" value="Bug">
			<input name="remote" type="hidden" value="yes">
			<tr>
				<td width="25%" align="right" valign="top" bgcolor="#eeeeee" class="label">Subject:</td>
				<td width="75%" bgcolor="#eeeeee">
					<input name="subject" id="subject" value="" class="text long" />
					<label class="error" for="name" id="subject_error">This field is required.</label>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="label">Your Name:</td>
				<td>
					<input name="yourname" id="yourname" value="" class="text long" />
					<label class="error" for="name" id="yourname_error">This field is required.</label>
				</td>
			</tr>
			<tr bgcolor="#eeeeee">
				<td align="right" valign="top" class="label">Your Email Address:</td>
				<td>
					<input name="email" id="email" value="" class="text long" />
					<label class="error" for="name" id="email_error">This field is required.</label>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="label">Please describe what you were doing when this error occurred:</td>
				<td>
					<textarea id="description" name="description" class="textarea long" rows="10"></textarea>
					<label class="error" for="description" id="description_error">This field is required.</label>
				</td>
			</tr>
			<tr bgcolor="#eeeeee">
				<td align="right" valign="top" class="label">Error:</td>
				<td><textarea name="error_message" rows="3" class="textarea long environ" id="error_message" readonly="readonly"><?php echo $error_message; ?></textarea></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="label">Your Environment:</td>
				<td><textarea name="environ" rows="3" class="textarea long environ" id="environ" readonly="readonly"><?php echo $environ; ?></textarea></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input name="submit" type="submit" class="action_btn" id="submit" value="Submit" /></td>
			</tr>
		</form>
	</table>
</div>
<?php if ( ! empty($trace)): ?>
<h3><?php echo Kohana::lang('core.stack_trace') ?></h3>
<?php echo $trace ?>
<?php endif ?>
<p class="stats"><?php echo Kohana::lang('core.stats_footer', Kohana::config('version.ushahidi_version')) ?></p>
</div>
</body>
</html>