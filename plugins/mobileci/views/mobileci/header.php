<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<title><?php echo $site_name; ?></title>
<?php
/*
if ($show_map === TRUE)
{
	echo "\n<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>\n";
}
*/
echo html::script('plugins/mobileci/views/js/jquery', true);
//echo html::script('plugins/mobileci/views/js/jquerymobile', true);
echo html::script('plugins/mobileci/views/js/modernizr', true);

//echo html::stylesheet('plugins/mobileci/views/css/jquerymobile', true);
echo html::stylesheet('plugins/mobileci/views/css/style');
?>
<script type="text/javascript">
<?php /* echo $js; */ ?>
</script>
</head>

<body <?php
/*
if ($show_map === TRUE) {
	echo " onload=\"initialize()\"";
}
*/
?>>
<div id="container">
	<div id="header">
		<h1><?php echo $site_name; ?></h1>
		<span><?php echo $site_tagline; ?></span>
	</div>
	<div id="page">