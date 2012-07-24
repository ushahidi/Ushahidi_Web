<?php 
/**
 * Dashboard view page.
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
				<h2><?php echo $title; ?></h2>
				
				<div id="need_to_upgrade" style="display:none;"></div>
				<?php echo $version_sync; ?>
				<?php echo $security_info; ?>
				
				<!-- column -->
				<div class="column">
					
					<!-- box -->
					<div class="box">
						<h3><?php echo Kohana::lang('ui_main.reports_timeline');?></h3>
						<ul class="inf" style="margin-bottom:10px;">
							<li class="none-separator"><?php echo Kohana::lang('ui_main.view');?>:<a href="<?php print url::site() ?>admin/dashboard/?range=1"><?php echo Kohana::lang('ui_main.today');?></a></li>
							<li><a href="<?php print url::site() ?>admin/dashboard/?range=31"><?php echo Kohana::lang('ui_main.past_month');?></a></li>
							<li><a href="<?php print url::site() ?>admin/dashboard/?range=365"><?php echo Kohana::lang('ui_main.past_year');?></a></li>
							<li><a href="<?php print url::site() ?>admin/dashboard/?range=0"><?php echo Kohana::lang('ui_main.all');?></a></li>
						</ul>
						<div class="chart-holder" style="clear:both;padding-left:5px;">
							<?php echo $report_chart; ?>
							<?php if($failure != ''){ ?>
								<div class="red-box" style="width:400px;">
									<h3><?php echo Kohana::lang('ui_main.error');?></h3>
									<ul><li><?php echo $failure; ?></li></ul>
								</div>
							<?php } ?>
						</div>
					</div>
					
					<!-- info-container -->
					<div class="info-container">
						<div class="i-c-head">
							<h3><?php echo Kohana::lang('ui_main.recent_reports');?></h3>
							<ul>
								<li class="none-separator"><a href="<?php echo url::site() . 'admin/reports' ?>"><?php echo Kohana::lang('ui_main.view_all');?></a></li>
								<li><a href="<?php echo url::site(); ?>feed" class="rss-icon"><?php echo Kohana::lang('ui_main.rss');?></a></li>
							</ul>
						</div>
						<?php
						if ($reports_total == 0)
						{
						?>
						<div class="post">
							<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
						</div>
						<?php	
						}
						foreach ($incidents as $incident)
						{
							$incident_id = $incident->id;
							$incident_title = strip_tags($incident->incident_title);
							$incident_description = text::limit_chars(strip_tags($incident->incident_description), 150, '...');
							$incident_date = $incident->incident_date;
							$incident_date = date('g:i A', strtotime($incident->incident_date));
							$incident_mode = $incident->incident_mode;	// Mode of submission... WEB/SMS/EMAIL?
							
							if ($incident_mode == 1)
							{
								$submit_mode = "mail";
							}
							elseif ($incident_mode == 2)
							{
								$submit_mode = "sms";
							}
							elseif ($incident_mode == 3)
							{
								$submit_mode = "mail";
							}
							elseif ($incident_mode == 4)
							{
								$submit_mode = "twitter";
							}
							
							// Incident Status
							$incident_approved = $incident->incident_active;
							if ($incident_approved == '1')
							{
								$incident_approved = "ok";
							}
							else
							{
								$incident_approved = "none";
							}
							
							$incident_verified = $incident->incident_verified;
							if ($incident_verified == '1')
							{
								$incident_verified = "ok";
							}
							else
							{
								$incident_verified = "none";
							}
							?>
							<div class="post">
								<ul class="post-info">
									<li><a href="#" class="<?php echo $incident_approved; ?>"><?php echo utf8::strtoupper(Kohana::lang('ui_main.approved'));?>:</a></li>
									<li><a href="#" class="<?php echo $incident_verified ?>"><?php echo utf8::strtoupper(Kohana::lang('ui_main.verified'));?>:</a></li>
									<li class="last"><a href="#" class="<?php echo $submit_mode; ?>"><?php echo utf8::strtoupper(Kohana::lang('ui_main.source'));?>:</a></li>
								</ul>
								<h4><strong><?php echo $incident_date; ?></strong><a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>"><?php echo $incident_title; ?></a></h4>
								<p><?php echo $incident_description; ?></p>
							</div>
							<?php
						}
						?>
						<a href="<?php echo url::site() . 'admin/reports' ?>" class="view-all"><?php echo Kohana::lang('ui_main.view_all_reports');?></a>
					</div>
				</div>
				<div class="column-1">
					<!-- box -->
					<div class="box">
						<h3><?php echo Kohana::lang('ui_main.quick_stats');?></h3>
						<ul class="nav-list">
							<li>
								<a href="<?php echo url::site() . 'admin/reports' ?>" class="reports"><?php echo Kohana::lang('ui_main.reports');?></a>
								<strong><?php echo number_format($reports_total); ?></strong>
								<ul>
									<li><a href="<?php echo url::site() . 'admin/reports?status=a' ?>"><?php echo Kohana::lang('ui_main.not_approved');?></a><strong>(<?php echo $reports_unapproved; ?>)</strong></li>
									
								</ul>
							</li>
							<li>
								<a href="<?php echo url::site() . 'admin/manage' ?>" class="categories"><?php echo Kohana::lang('ui_main.categories');?></a>
								<strong><?php echo number_format($categories); ?></strong>
							</li>
							<li>
								<span class="locations"><?php echo Kohana::lang('ui_main.locations');?></span>
								<strong><?php echo $locations; ?></strong>
							</li>
							<li>
								<a href="<?php echo url::site() . 'admin/manage/feeds' ?>" class="media"><?php echo Kohana::lang('ui_main.news_feeds');?></a>
								<strong><?php echo number_format($incoming_media); ?></strong>
							</li>
							<li>
								<a href="<?php echo url::site() . 'admin/messages' ?>" class="messages"><?php echo Kohana::lang('ui_main.messages');?></a>
								<strong><?php echo number_format($message_count); ?></strong>
								<ul>
									<?php
									foreach ($message_services as $service) {
										echo "<li><a href=\"".url::site() . 'admin/messages/index/'.$service['id']."\">".$service['name']."</a><strong>(".$service['count'].")</strong></li>";
									}
									?>
								</ul>
							</li>
						</ul>
					</div>
					<!-- info-container -->
					<div class="info-container">
						<div class="i-c-head">
							<h3><?php echo Kohana::lang('ui_main.news_feeds');?></h3>
							<ul>
								<li class="none-separator"><a href="<?php echo url::site() . 'admin/manage/feeds' ?>"><?php echo Kohana::lang('ui_main.view_all');?></a></li>
								<li><a href="<?php echo url::site(); ?>feeds" class="rss-icon"><?php echo Kohana::lang('ui_main.rss');?></a></li>
							</ul>
						</div>
						<?php
						foreach ($feeds as $feed)
						{
							$feed_id = $feed->id;
							$feed_title = $feed->item_title;
							$feed_description = text::limit_chars(strip_tags($feed->item_description), 150, '...', True);
							$feed_link = $feed->item_link;
							$feed_date = date('M j Y', strtotime($feed->item_date));
							$feed_source = "NEWS";
							?>
							<div class="post">
								<h4><a href="<?php echo $feed_link; ?>" target="_blank"><?php echo $feed_title ?></a></h4>
								<em class="date"><?php echo $feed_source; ?> - <?php echo $feed_date; ?></em>
								<p><?php echo $feed_description; ?></p>
							</div>
							<?php
						}
						?>
						<a href="<?php echo url::site() . 'admin/manage/feeds' ?>" class="view-all"><?php echo Kohana::lang('ui_main.view_all');?> <?php echo Kohana::lang('ui_main.incoming_media');?></a>
					</div>
				</div>
			</div>

