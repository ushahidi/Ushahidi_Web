<?php
/**
 * MHI header view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $site_name; ?></title>
<?php
	echo html::stylesheet('media/css/mhi/reset','',true);
	echo "<!--[if lte IE 7]>".html::stylesheet('media/css/mhi/reset.ie','',true)."\n"."<![endif]-->";
	echo html::stylesheet('media/css/mhi/base','',true);
?>

<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js"></script>

<?php 
	foreach($js_files as $js_file)
		echo $js_file."\n";
?>

<?php if($js != '') { ?>
<script type="text/javascript" language="javascript">	
<?php echo $js."\n"; ?>
</script>
<?php } ?>

</head>

<body class="<?php echo $this_body; ?> content">
	<div id="header">
    	<div id="header-wrapper">
    		<h1><a href="<?php echo url::base() ?>mhi/">Ushahidi</a></h1>
            <ul class="primary-nav">
                <li><a href="<?php echo url::base() ?>mhi/about"<?php if($this_body == 'about') { ?> class="active" <?php } ?>>About</a></li>
                <li><a class="contact.html" href="#">Contact Us</a></li>
            </ul>
        </div>
    </div>
	<div id="wrapper">