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
				<h2>
					<?php admin::settings_subtabs("site"); ?>

				</h2>
				<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 'id' => 'siteForm', 'name' => 'siteForm')); ?>
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
						<h3><?php echo Kohana::lang('settings.site.title');?></h3>
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
					</div>
					<!-- column -->
					<div class="sms_holder">
						<div id="need_to_upgrade" style="display:none;"></div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_name"); ?>"><?php echo Kohana::lang('settings.site.name');?></a></h4>
							<?php print form::input('site_name', $form['site_name'], ' class="text long2" maxlength="250"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_tagline"); ?>"><?php echo Kohana::lang('settings.site.tagline');?></a></h4>
							<?php print form::input('site_tagline', $form['site_tagline'], ' class="text long2" maxlength="250"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_banner"); ?>"><?php echo Kohana::lang('settings.site.banner');?></a></h4>
							<?php if($banner_m != NULL) { ?>
								<img src="<?php echo $banner_m; ?>" alt="<?php Kohana::lang('settings.site.banner'); ?>" /><br/>
							<?php } ?>
							<?php echo form::upload('banner_image', '', ''); ?> (&lt;&#61; 250k)
							<br/>
							<?php
								echo form::checkbox('delete_banner_image', '1');
								echo form::label('delete_banner_image', Kohana::lang("settings.site.delete_banner_image"));

							?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_email"); ?>"><?php echo Kohana::lang('settings.site.email_site');?></a>
							<br /><?php echo Kohana::lang('settings.site.email_notice');?></h4>
							<?php print form::input('site_email', $form['site_email'], ' class="text long2"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_message"); ?>"><?php echo Kohana::lang('settings.site.message');?></a></h4>
							<?php print form::textarea('site_message', $form['site_message'], ' style="height:40px;"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_copyright_statement"); ?>"><?php echo Kohana::lang('settings.site.copyright_statement');?></a></h4>
							<?php print form::textarea('site_copyright_statement', $form['site_copyright_statement'], ' style="height:40px;"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_submit_report_message"); ?>"><?php echo Kohana::lang('settings.site.submit_report_message');?></a></h4>
							<?php print form::textarea('site_submit_report_message', $form['site_submit_report_message'], ' style="height:40px;"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_locale"); ?>"><?php echo Kohana::lang('settings.site.language');?></a> (Locale)</h4>
							<span class="sel-holder">
								<?php print form::dropdown('site_language', $locales_array, $form['site_language']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_site_timezone"); ?>"><?php echo Kohana::lang('settings.site.timezone');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('site_timezone',$site_timezone_array, $form['site_timezone']); ?>
							</span>
							<div style="clear:both;"></div>
							<small><?php echo Kohana::lang('ui_admin.server_time').' '.date("m/d/Y H:i:s",time()).' ('.$form['site_timezone'].')'; ?></small>
						</div>

						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_display_contact"); ?>"><?php echo Kohana::lang('settings.site.display_contact_page');?></a></h4>
							<?php print form::dropdown('site_contact_page', $yesno_array, $form['site_contact_page']); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_display_items_per_page"); ?>"><?php echo Kohana::lang('settings.site.items_per_page');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('items_per_page', $items_per_page_array, $form['items_per_page']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_display_items_per_page_admin"); ?>"><?php echo Kohana::lang('settings.site.items_per_page_admin');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('items_per_page_admin', $items_per_page_array, $form['items_per_page_admin']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_blocks_per_row"); ?>"><?php echo Kohana::lang('settings.site.blocks_per_row');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('blocks_per_row', $blocks_per_row_array, $form['blocks_per_row']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_allow_reports"); ?>"><?php echo Kohana::lang('settings.site.allow_reports');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_reports', $yesno_array, $form['allow_reports']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_allow_alerts"); ?>"><?php echo Kohana::lang('settings.site.allow_alerts');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_alerts', $yesno_array, $form['allow_alerts']); ?>
							</span>
						</div>
						<div class="row" id="alerts_selector">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_alert_email"); ?>"><?php echo Kohana::lang('settings.site.email_alerts');?></a></h4>
							<?php print form::input('alerts_email', $form['alerts_email'], ' class="text long2"', 'id = alert_email'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_allow_comments"); ?>"><?php echo Kohana::lang('settings.site.allow_comments');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_comments', $comments_array, $form['allow_comments']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_allow_feed"); ?>"><?php echo Kohana::lang('settings.site.allow_feed');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_feed', $yesno_array, $form['allow_feed']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_share_site_stats"); ?>"><?php echo Kohana::lang('settings.site.share_site_stats');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('allow_stat_sharing', $yesno_array, $form['allow_stat_sharing']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_cache_pages"); ?>"><?php echo Kohana::lang('settings.site.cache_pages');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('cache_pages', $yesno_array, $form['cache_pages']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_cache_pages_lifetime"); ?>"><?php echo Kohana::lang('settings.site.cache_pages_lifetime');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('cache_pages_lifetime', $cache_pages_lifetime_array, $form['cache_pages_lifetime']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_private_deployment"); ?>"><?php echo Kohana::lang('settings.site.private_deployment');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('private_deployment', $yesno_array, $form['private_deployment']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_manually_approve_users"); ?>"><?php echo Kohana::lang('settings.site.manually_approve_users');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('manually_approve_users', $yesno_array, $form['manually_approve_users']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_require_email_confirmation"); ?>"><?php echo Kohana::lang('settings.site.require_email_confirmation');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('require_email_confirmation', $yesno_array, $form['require_email_confirmation']); ?>
							</span>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_checkins"); ?>"><?php echo Kohana::lang('settings.site.checkins');?></a></h4>
							<span class="sel-holder">
								<?php print form::dropdown('checkins', $yesno_array, $form['checkins']); ?>
							</span>
						</div>
						<div class="row">
						<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_google_analytics"); ?>"><?php echo Kohana::lang('settings.site.google_analytics');?></a></h4>
							<?php echo Kohana::lang('settings.site.google_analytics_example');?> &nbsp;&nbsp;
							<?php print form::input('google_analytics', $form['google_analytics'], ' class="text"'); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_twitter_configuration"); ?>"><?php echo Kohana::lang('settings.site.twitter_configuration');?></a></h4>
							<div class="row">
								<?php echo Kohana::lang('settings.site.twitter_hashtags');?>
								<?php print form::input('twitter_hashtags', $form['twitter_hashtags'], ' class="text"'); ?>
							</div>
						</div>
						<div class="row">
							<h4><?php echo Kohana::lang('settings.site.api_akismet');?></h4>
							<?php echo Kohana::lang('settings.site.kismet_notice');?>.
							<?php print form::input('api_akismet', $form['api_akismet'], ' class="text"'); ?>
						</div>
					</div>

					<div class="simple_border"></div>

					<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
				</div>
				<?php print form::close(); ?>
			</div>
