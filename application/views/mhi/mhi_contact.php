<?php
/**
 * MHI - Contact
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
                <h2>Contact Us</h2>
                
                <?php if ($success_message != '') { ?>
					<div style="background-color:#95C274;border:4px #8CB063 solid;padding:2px 8px 1px 8px;margin:10px;"><?php echo $success_message; ?></div>
				<?php } ?>
				
				<?php if ($form_error) { ?>
					<div style="background-color:#C27474;border:4px #B06363 solid;padding:2px 8px 1px 8px;margin:10px;"><ul><?php 
                        foreach ( $errors as $error_item => $error_description )
                        {
                            print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
                        }?>
                        </ul>
                        </div>
				<?php } ?>
                
                <p class="intro-para">Before contacting us, please read our <a href="<?php echo url::site(); ?>mhi/about/faq">FAQ</a>.</p>
                <p>If you are having an issue with your Crowdmap account or deployment, please provide as many details as possible including the URL to your deployment, your username, etc. Also, keep in mind that we will never ask for your password. This should be kept to yourself!</p>
				
                <?php print form::open(url::site().'mhi/contact', array('id' => 'frm-Contact', 'name' => 'frm-Contact', 'class' => 'frm-content')); ?>
                	
                	<p>
			        	<label for="contact_email">Email</label><br/>
			        	<?php print form::input('contact_email', $form['contact_email'], 'maxlength="100" id="contact_email" size="30" autocomplete="off"');?>
			        </p>
			        
			        <p>
			        	<!-- <label for="contact_subject">Topic</label><br/> -->
			        	<select name="contact_subject" id="contact_subject">
			        		<option value="Topic Not Selected">[ Select a Topic ]</option>
			        		<option value="Bug Report">Bug Report</option>
			        		<option value="Suggestion">Suggestion</option>
			        		<option value="Other">Other</option>
			        	</select>
			        </p>
                	
                	<p>
			        	<label for="contact_message">Message</label><br/>
                        <?php print form::textarea('contact_message', $form['contact_message'], ' rows="4" cols="40" id="contact_message" autocomplete="off"') ?>

			        </p>
                    <p>
					    <label for="contact_captcha">Security Code:</label><br />
					    <?php print $captcha->render(); ?><br />
						<?php print form::input('contact_captcha',$form['contact_captcha'], 'id="contact_captcha"');?>
                    </p>
                	<p>
			        	<input class="button" type="submit" value="Send" />
			        </p>
                
                <?php print form::close(); ?>
                
                <p>You may also send us an email at <a href="mailto:support@crowdmap.com">support@crowdmap.com</a></p>
                
                
                
            </div></div>
            <div class="twocol-right">
                <!-- CB: We'll just leave this empty for now.
                
                <div class="side-bar-module rounded shadow">
                    <p>Sign-up Sidebar promo to go here</p>
                </div>
                -->
            </div>
            <div style="clear:both;"></div>
        </div>
