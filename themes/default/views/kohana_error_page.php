<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php
if (PHP_SAPI === 'cli')
{
	echo $error . ': ' . $message ."\n";
	if ( ! empty($file))
	{
		echo "FILE: ".$file."\n";
	}
	if ( ! empty($line))
	{
		echo "LINE: ".$line."\n";
	}
	echo "ERROR: ".$message."\n";
	exit();
}
?>
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
$url = ( isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )
	? $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]
	: '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
$ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

$environ = "";
$environ .= "*URL*: ".$url."\n";
$environ .= "*REFERER*: ".$referer."\n";
$environ .= "*USER_AGENT*: ".$user_agent."\n";
$environ .= "*IP*: ".$ip_address."\n";
$environ .= "*USHAHIDI VERSION*: ".Kohana::config('settings.ushahidi_version')."\n";
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
	<p class="bug_form_desc">Found a bug? Submit a bug report to the Ushahidi <a href="https://github.com/ushahidi/Ushahidi_Web/issues">Github issues page</a>- help us make Ushahidi better software -- Thanks!</p>
</div>
<?php if ( ! empty($trace)): ?>
<h3><?php echo Kohana::lang('core.stack_trace') ?></h3>
<?php echo $trace ?>
<?php endif ?>
<p class="stats"><?php echo Kohana::lang('core.stats_footer', Kohana::config('version.ushahidi_version')) ?></p>
</div>
</body>
</html>