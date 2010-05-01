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
            	<li><a href="<?php echo url::base() ?>mhi/features"<?php if($this_body == 'mhi-features') { ?> class="active" <?php } ?>>Features</a></li>
                <li><a href="<?php echo url::base() ?>mhi/about"<?php if($this_body == 'mhi-about') { ?> class="active" <?php } ?>>About</a></li>
                <li><a class="contact.html" href="#">Contact Us</a></li>
            </ul>
            <div id="login-box">
            	<?php
            		if($mhi_user_id)
            		{
            			?>
            			<p>You are logged in.<a class="sign-in rounded" href="<?php echo url::base() ?>mhi/logout">Log Out </a></p>
            			<?php
            		}else{
            			?>
            			<p>Have an account?<a id="btn_sign-in" class="sign-in rounded" href="#">Sign In </a></p>
            			<?php
            		}
            	?>
            </div>
            <div id="login-form" class="rounded box-shadow">
            	<form method="POST" name="frm_login" style="line-height: 100%; margin-top: 0; margin-bottom: 0">
                	<p class="error">
					<?php
                        if ($form_error) {

                            foreach ($errors as $error_item => $error_description)
                            {
                                print (!$error_description) ? '' : "&#8226;&nbsp;" . $error_description . "<br />";
                            }
                        }
                     ?>
                	</p>
                	<p>
                    	<label for="username"><?php echo Kohana::lang('ui_main.username');?></label>
                    	<input name="username" class="text rounded" id="username" type="text" title="username" value="" />
                   	</p>
                    <p>
                    	<label for="password"><?php echo Kohana::lang('ui_main.password');?></label>
                    	<input name="password" class="text rounded" id="password" type="password" title="password" value="" />
                   	</p>
                    <p>
                    	<input id="submit" name="submit" class="btn_sign-in rounded" type="submit" value="Sign in" />
                    </p>
                    <p class="forgot-password">
                    	<a href="#">Forgot Password?</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
	<div id="wrapper">