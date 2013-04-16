<?php 
/**
 * Twitter Settings view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Facebook Settings View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">

				<h2>
					<?php admin::settings_subtabs("twitter"); ?>
				</h2>
				<?php print form::open(); ?>
				<div class="report-form">
					<?php
					if ($form_error) {
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3><?php echo Kohana::lang('ui_main.error');?></h3>
							<ul>
							<?php
							foreach ($errors as $error_item => $error_description)
							{
								// print "<li>" . $error_description . "</li>";
								print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
							}
							?>
							</ul>
						</div>
					<?php
					}

					if ($form_saved) {
					?>
						<!-- green-box -->
						<div class="green-box">
							<h3><?php echo Kohana::lang('ui_main.configuration_saved');?></h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3><?php echo Kohana::lang('settings.twitter.title');?></h3>
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
					</div>
					<!-- column -->
		
					<div class="sms_holder">
						<?php
						if ( ! $form_error
							AND ! empty($form['twitter_api_key'])
							AND ! empty($form['twitter_api_key_secret'])
							AND ! empty($form['twitter_token'])
							AND ! empty($form['twitter_token_secret'])
						)
						{
						?>
							<div class="test_settings">
								<div class="tab">
									<ul>
										<li><a
										href="javascript:twitterTest();"><?php echo utf8::strtoupper(Kohana::lang('settings.test_settings'));?></a></li>
										<li id="test_loading"></li>
										<li id="test_status"></li>
									</ul>
								</div>
							</div>
						<?php
						}
						?>

						<div class="row">
							<h4><?php echo Kohana::lang('settings.twitter.description');?>:<br><a href="https://twitter.com/oauth_clients/" target="_blank">https://twitter.com/oauth_clients/</a></h4>
							<h4>For instructions see <a
							href="https://wiki.ushahidi.com/display/WIKI/Configuring+Twitter+on+a+deployment/"target="_blank">https://wiki.ushahidi.com/display/WIKI/Configuring+Twitter+on+a+deployment</a></h4>
							<h4><?php echo Kohana::lang('settings.twitter.api_key');?>:</h4>
							<?php print form::input('twitter_api_key', $form['twitter_api_key'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><?php echo Kohana::lang('settings.twitter.api_key_secret');?>:</h4>
							<?php print form::input('twitter_api_key_secret',$form['twitter_api_key_secret'],'class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><?php echo Kohana::lang('settings.twitter.token');?>:</h4>
							<?php print form::input('twitter_token', $form['twitter_token'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><?php echo Kohana::lang('settings.twitter.token_secret');?>:</h4>
							<?php print form::input('twitter_token_secret',$form['twitter_token_secret'],'class="text long2"'); ?>
						</div>

						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_twitter_configuration"); ?>"><?php echo Kohana::lang('settings.site.twitter_configuration');?></a></h4>
							<div class="row">
								<?php echo Kohana::lang('settings.site.twitter_hashtags');?>
								<?php print form::input('twitter_hashtags', $form['twitter_hashtags'], ' class="text"'); ?>
							</div>
						</div>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
				</div>
				<?php print form::close(); ?>
			</div>
