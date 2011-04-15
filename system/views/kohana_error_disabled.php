<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $error ?></title>
</head>
<body>
<style type="text/css">
<?php include Kohana::find_file('views', 'kohana_errors', FALSE, 'css') ?>
</style>
<div id="framework_error" style="width:24em;margin:50px auto;">
<h3><?php echo html::specialchars($error) ?></h3>
<p style="text-align:center"><?php echo $message ?></p>
</div>
</body>
</html>