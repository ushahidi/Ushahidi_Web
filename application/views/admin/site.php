<?php 
/**
 * Site view page.
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
			<div class="bg">
				<h2><?php echo $title; ?> 
					<a href="<?php echo url::base() . 'admin/settings/site' ?>" class="active">Site</a>
					<a href="<?php echo url::base() . 'admin/settings' ?>">Map</a>
					<a href="<?php echo url::base() . 'admin/settings/sms' ?>">SMS</a>
					<a href="<?php echo url::base() . 'admin/settings/sharing' ?>">Sharing</a>
					<a href="<?php echo url::base() . 'admin/settings/email' ?>">Email</a>
					<a href="<?php echo url::base() . 'admin/settings/themes' ?>">Themes</a>
				</h2>
				<?php print form::open(); ?>
				<div class="report-form">
					<?php
					if ($form_error) {
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3>Error!</h3>
							<ul>
							<?php
							foreach ($errors as $error_item => $error_description)
							{
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
							<h3>Your Settings Have Been Saved!</h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3>Site Settings</h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->		
					<div class="sms_holder">
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_name"); ?>">Site Name</a></h4>
							<?php print form::input('site_name', $form['site_name'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_tagline"); ?>">Site Tagline</a></h4>
							<?php print form::input('site_tagline', $form['site_tagline'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_email"); ?>">Site Email Address</a> 
							<br /><span>In order to receive reports by email, please <a href="<?php echo url::base().'admin/settings/email' ;?>">
							configure your email account settings</a>.</span></h4>
							<?php print form::input('site_email', $form['site_email'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_alert_email"); ?>">Alert Email Address</a></h4>
							<?php print form::input('alerts_email', $form['alerts_email'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_locale"); ?>">Site Language</a> (Locale)</h4>
							<span class="sel-holder">
								<?php print form::dropdown('site_language', $locales_array, $form['site_language']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Display Contact Page?</h4>
							<?php print form::dropdown('site_contact_page', $yesno_array, $form['site_contact_page']); ?>
						</div>
						<div class="row">
							<h4>Display How To Help Page?</h4>
							<?php print form::dropdown('site_help_page', $yesno_array, $form['site_help_page']); ?>
						</div>
						<div class="row">
							<h4>Items Per Page - Front End</h4>
							<span class="sel-holder">
								<?php print form::dropdown('items_per_page', $items_per_page_array, $form['items_per_page']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Items Per Page - Admin</h4>
							<span class="sel-holder">
								<?php print form::dropdown('items_per_page_admin', $items_per_page_array, $form['items_per_page_admin']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Allow Users To Submit Reports?</h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_reports', $yesno_array, $form['allow_reports']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Allow Users to Submit Comments to Reports?</h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_comments', $yesno_array, $form['allow_comments']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Include RSS News Feed on Website?</h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_feed', $yesno_array, $form['allow_feed']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Share Site Statistics in API?</h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_stat_sharing', $yesno_array, $form['allow_stat_sharing']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Cluster Reports on Map?</h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_clustering', $yesno_array, $form['allow_clustering']); ?>
							</span>
						</div>
						<div class="row">
							<h4>Default Color For All Categories?</h4>
							<?php print form::input('default_map_all', $form['default_map_all'], ' class="text"'); ?>
							<script type="text/javascript" charset="utf-8">
								$(document).ready(function() {
									$('#default_map_all').ColorPicker({
										onSubmit: function(hsb, hex, rgb) {
											$('#default_map_all').val(hex);
										},
										onChange: function(hsb, hex, rgb) {
											$('#default_map_all').val(hex);
										},
										onBeforeShow: function () {
											$(this).ColorPickerSetColor(this.value);
										}
									})
									.bind('keyup', function(){
										$(this).ColorPickerSetColor(this.value);
									});
								});
							</script>
						</div>						
						<div class="row">
							<h4>Google Analytics</h4>
							Web Property ID - Format: UA-XXXXX-XX &nbsp;&nbsp;
							<?php print form::input('google_analytics', $form['google_analytics'], ' class="text"'); ?>
						</div>
						<div class="row">
							<h4>Twitter Credentials</h4>
							<div class="row">
								Hashtags - Separate with commas
								<?php print form::input('twitter_hashtags', $form['twitter_hashtags'], ' class="text"'); ?>
							</div>
							<div class="row" style="padding-top:5px;">
								Username
								<?php print form::input('twitter_username', $form['twitter_username'], ' class="text"'); ?>
							</div>
							<div class="row" style="padding-top:5px;">
								Password
								<?php print form::password('twitter_password', $form['twitter_password'], ' class="text"'); ?>
							</div>
						</div>
						<div class="row">
							<h4>Laconica Credentials</h4>

							<div class="row">
								Username
								<?php print form::input('laconica_username', $form['laconica_username'], ' class="text"'); ?>
							</div>
								<div class="row" style="padding-top:5px;">
								Password
								<?php print form::password('laconica_password', $form['laconica_password'], ' class="text"'); ?>
							</div>
								<div class="row" style="padding-top:5px;">
								Laconica Site
								<?php print form::input('laconica_site', $form['laconica_site'], 'class="text long2"'); ?>
							</div>
						</div>
						<div class="row">
							<h4>Akismet Key</h4>
							Prevent comment spam using <a href="http://akismet.com/" target="_blank">Akismet</a> from Automattic. <BR />You can get a free API key by registering for a <a href="http://en.wordpress.com/api-keys/" target="_blank">WordPress.com user account</a>.
							<?php print form::input('api_akismet', $form['api_akismet'], ' class="text"'); ?>
						</div>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
