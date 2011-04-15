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
<?php include Kohana::find_file('views', 'kohana_errors', FALSE, 'css') ?>
</style>
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
<p class="stats"><?php echo Kohana::lang('core.stats_footer') ?></p>
</div>
</body>
</html>