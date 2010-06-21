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
<title>Crowdmap</title>
<?php
	echo html::stylesheet('media/css/mhi/reset','',true);
	
	echo "<!--[if lte IE 7]>".html::stylesheet('media/css/mhi/reset.ie','',true)."\n"."<![endif]-->";
	
	echo html::stylesheet('http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz','',false);
	
	echo html::link('http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz','stylesheet','text/css', false);
	
	echo html::stylesheet('media/css/mhi/base','',true);
	
	echo html::script(array(
		    'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js',
		    'http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js',
		    'media/js/mhi/jquery.cycle.min.js',
		    'media/js/mhi/initialize.js'
			), true);
?>

<?php if($js != '') { ?>
<script type="text/javascript" language="javascript">
<?php echo $js."\n"; ?>
</script>
<?php } ?>

<?php if($form_error === true) { ?>
<script type="text/javascript" language="javascript">
$(function(){
    //show the dagum form	
    $("#login-form").show();
    //add the active class to sign-in link
    $(this).addClass("active");
});
</script>
<?php } ?>

</head>

<body class="<?php echo $this_body; ?> content">
	<div id="page-wrap">
        <div id="header">
            <h1><a href="<?php echo url::site() ?>mhi/">Crowdmap</a></h1>
            
            <ul class="primary-nav">
            	<li><a href="<?php echo url::site() ?>mhi/"<?php if($this_body == 'crowdmap-home') { ?> class="active" <?php } ?>>Home</a></li>
            	<li><a href="<?php echo url::site() ?>mhi/features"<?php if($this_body == 'crowdmap-features') { ?> class="active" <?php } ?>>Features</a></li>
                <li><a href="<?php echo url::site() ?>mhi/about"<?php if($this_body == 'crowdmap-about') { ?> class="active" <?php } ?>>About</a></li>
                <li><a href="<?php echo url::site() ?>mhi/contact"<?php if($this_body == 'crowdmap-contact') { ?> class="active" <?php } ?>>Contact Us</a></li>
            </ul>
            <?php if( ! is_int($mhi_user_id)) { ?>
            <div id="login-box">
                <p>Have an account?<a class="sign-in rounded" href="#">Sign In </a></p>
            </div>
            <?php }else{ ?>
           	<div id="login-box">
                <p><a href="<?php echo url::site() ?>mhi/manage" class="rounded">Manage Your Account</a> or <a href="<?php echo url::site() ?>mhi/logout" class="rounded">Logout</a></p>
            </div>
            <?php } ?>
            <div id="login-form" class="rounded shadow">
                <?php print form::open(url::site().'mhi/', array('id' => 'frm-MHI-Login', 'name' => 'frm-Login')); ?>
                    <p>
                        <label for="username">E-mail</label>
                        <input type="text" name="username" class="text rounded" id="username" title="username" value="<?php echo $form['username'] ?>" />
                    </p>
                    <p>
                        <label for="password">Password</label>
                        <input type="password" name="password" class="text rounded" id="password" title="password" value="" />
                        <?php if($form_error === true) { ?>
                        	<div class="error">Your username and/or<br/>password were incorrect.</div>
                        <?php } ?>
                    </p>
                    <p>
                        <input class="btn_sign-in rounded" type="submit" value="Sign In" />
                    </p>
                    <p class="forgot-password">
                        <a href="<?php echo url::site() ?>mhi/reset_password">Forgot Password?</a>
                    </p>
                <?php print form::close(); ?>
            </div>
        </div>