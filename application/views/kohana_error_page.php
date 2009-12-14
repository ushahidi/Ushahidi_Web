<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $error ?></title>
<base href="http://php.net/" />
</head>
<body>
<style type="text/css">
* { margin: 0; padding: 0; }
body {background-image:url(<?php echo url::base(); ?>media/img/bkg_login.gif);}
div#ushahidi_login_logo {text-align:center; margin:50px 0 20px 0;}
div#ushahidi_login_logo img { width:400px; height:80px;  }
div#framework_error { background:#fff; font-family:sans-serif; color:#111; font-size:14px; line-height:130%;border:3px solid #ccc;text-align:left;padding: 20px; }
div#framework_error h3 { color:#fff; font-size:16px; padding:8px 6px; margin:0 0 8px; background:#666; text-align:center; }
div#framework_error a { color:#228; text-decoration:none; }
div#framework_error a:hover { text-decoration:underline; }
div#framework_error strong { color:#900; }
div#framework_error p { margin:0; padding:4px 6px 10px; }
div#framework_error tt,
div#framework_error pre,
div#framework_error code { font-family:monospace; padding:2px 4px; font-size:12px; color:#333;
	white-space:pre-wrap; /* CSS 2.1 */
	white-space:-moz-pre-wrap; /* For Mozilla */
	word-wrap:break-word; /* For IE5.5+ */
}
div#framework_error tt { font-style:italic; }
div#framework_error tt:before { content:">"; color:#aaa; }
div#framework_error code tt:before { content:""; }
div#framework_error pre,
div#framework_error code { background:#eaeee5; border:solid 0 #D6D8D1; border-width:0 1px 1px 0; }
div#framework_error .block { display:block; text-align:left; }
div#framework_error .ushahidi_bugs { text-align:center; font-size:12px; }
div#framework_error .stats { padding:4px; background: #eee; border-top:solid 1px #ccc; text-align:center; font-size:10px; color:#888; }
div#framework_error .backtrace { margin:0; padding:0 6px; list-style:none; line-height:12px; }
</style>
<div id="ushahidi_login_logo"><img src="<?php echo url::base(); ?>media/img/admin/logo_login.gif" /></div>
<div id="framework_error" style="width:42em;margin:20px auto;">
<h3><?php echo html::specialchars($error) ?></h3>
<p><?php echo html::specialchars($description) ?></p>
<?php if ( ! empty($line) AND ! empty($file)): ?>
<p><?php echo Kohana::lang('core.error_file_line', $file, $line) ?></p>
<?php endif ?>
<p><code class="block"><?php echo $message ?></code></p>
<?php if ( ! empty($trace)): ?>
<h3><?php echo Kohana::lang('core.stack_trace') ?></h3>
<?php echo $trace ?>
<?php endif ?>
<p class="ushahidi_bugs"><?php echo Kohana::lang('core.report_bug',
	"http://bugs.ushahidi.com?e=".urlencode($message)."&f=".urlencode($file)."&l=".urlencode($line)) ?></p>
<p class="stats"><?php echo Kohana::lang('core.stats_footer', Kohana::config('version.ushahidi_version')) ?></p>
</div>
</body>
</html>