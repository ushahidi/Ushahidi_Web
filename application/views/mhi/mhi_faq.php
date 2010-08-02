<?php
/**
 * MHI - FAQ
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Page View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
		<div id="primary-content">
            <div class="twocol-left"><div class="content-shadow">
                <h2>About Crowdmap</h2>
                <h3>FAQ</h3>
                
                <p class="intro-para">This is where the FAQ is listed. Lorem and such. This page is under development and all answers to questions should be considered ridiculous and you shouldn't believe them.</p>
                
                <p>
                	<ul>
	                	<li><a href="#1">Can I have multiple accounts?</a></li>
						<li><a href="#2">Is there a storage limit?</a></li>
						<li><a href="#3">How does Crowdmap work with SMS reports?</a></li>
						<li><a href="#4">How do I reset my password?</a></li>
						<li><a href="#5">Can I use my own domain name?</a></li>
						<li><a href="#6">Can I advertise on my deployment?</a></li>
						<li><a href="#7">Can I change my username?</a></li>
					</ul>
                </p>
                
                <p>
                	<a name="1"></a>
                	<h4>Can I have multiple accounts?</h4>
                	Yes. Keep in mind that you can have multiple deployments with one account, though!
                </p>
                
                <p>
                	<a name="2"></a>
                	<h4>Is there a storage limit?</h4>
                	While we aren't enforcing a strict limit on storage at this time, we are monitoring deployments and maintain the right to take action to prevent any one single deployment from hogging resources.
                </p>
                
                <p>
                	<a name="3"></a>
                	<h4>How does Crowdmap work with SMS reports?</h4>
                	Your deployment will not automatically support SMS immediately after you create your deployment. To get this to work, you must create an account at <a href="www.clickatell.com">Clickatell</a> or set up <a href="FrontlineSMS">FrontlineSMS</a> to work with your deployment. To get it to work with your deployment, you will need to follow the instructions in your deployment. These can be found here: http://[YOURDEPLOYMENT].crowdmap.com/admin/settings/sms
                </p>
                
                <p>
                	<a name="4"></a>
                	<h4>How do I reset my password?</h4>
                	Go to the <a href="http://crowdmap.com/mhi/reset_password">Password Reset</a> page and submit your email address. We will email you instructions on how to change your password.
                </p>
                
                <p>
                	<a name="5"></a>
                	<h4>Can I use my own domain name?</h4>
                	Currently we are not offering services to bind your domain name to your Crowdmap deployment. We are looking into offering this in the future so keep your eyes open.
                </p>
                
                <p>
                	<a name="6"></a>
                	<h4>Can I advertise on my deployment?</h4>
                	Sure.
                </p>
                
                <p>
                	<a name="7"></a>
                	<h4>How do I change my username, password or name?</h4>
                	Log into your account and go to the <a href="http://crowdmap.com/mhi/account">Account Settings</a>. From here you are able to change your email address, which is also your username. You are also able to change your password and your name.
                </p>
            
            </div></div>
            <div class="twocol-right">
				<!-- right nav -->
				<div class="side-bar-module rounded shadow">
					<h4><a href="<?php echo url::site(); ?>mhi/about/">About</a></h4>
					<div class="side-bar-content">
						<ul class="sub-nav-links">
							<li><a href="<?php echo url::site(); ?>mhi/about/faq">FAQ</a></li>
						</ul>
					</div>
				</div>
				<!-- / right nav -->
            </div>
            <div style="clear:both;"></div>
        </div>