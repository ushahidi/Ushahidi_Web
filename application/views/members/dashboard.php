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
				<!-- column -->
				<div class="column">

					<!-- welcome box -->
					<?php if($hidden_welcome_fields['needinfo']) { ?>
					<div class="box">
						<h3><?php echo Kohana::lang("ui_main.more_information"); ?></h3>
						<?php echo form::open('/members/profile/'); ?>

						<div class="welcome-form">

							<?php echo form::hidden($hidden_welcome_fields); ?>

							<div class="row" style="padding-top:10px;">
								<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.profile_name"); ?>"><?php echo Kohana::lang('ui_main.full_name');?></a></h4>
								<?php echo form::input('name', $user->name, ' class="text long2"'); ?>
							</div>

							<div class="row" style="padding-top:10px;">
								<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.profile_public_url"); ?>"><?php echo Kohana::lang('ui_main.public_profile_url');?></a></h4>
								<span style="float:left;"><?php echo url::base().'profile/user/'; ?></span>
								<?php echo form::input('username', $user->username, ' class="text short2"'); ?>
							</div>

							<div class="row" style="padding-top:10px;">
								<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.profile_public"); ?>"><?php echo Kohana::lang('ui_main.public_profile');?>:</a></h4>
								<?php
									echo form::label('profile_public', Kohana::lang('ui_main.on').': ');
									echo form::radio('public_profile', '1', $profile_public, 'id="profile_public"').'&nbsp;&nbsp;&nbsp;&nbsp;';
									echo form::label('profile_private', Kohana::lang('ui_main.off').': ');
									echo form::radio('public_profile', '0', $profile_private, 'id="profile_private"').'<br />';
								?>
							</div>

							<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />

						</div>

						<?php echo form::close(); ?>
					</div>
					<?php } ?>

					<!-- box -->
					<div class="box">
						<h3><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.reports_timeline');?></h3>
						<ul class="inf" style="margin-bottom:10px;">
							<li class="none-separator"><?php echo Kohana::lang('ui_main.view');?>:<a href="<?php echo url::site() ?>members/dashboard/?range=1"><?php echo Kohana::lang('ui_main.today');?></a></li>
							<li><a href="<?php echo url::site() ?>members/dashboard/?range=31"><?php echo Kohana::lang('ui_main.past_month');?></a></li>
							<li><a href="<?php echo url::site() ?>members/dashboard/?range=365"><?php echo Kohana::lang('ui_main.past_year');?></a></li>
							<li><a href="<?php echo url::site() ?>members/dashboard/?range=0"><?php echo Kohana::lang('ui_main.all');?></a></li>
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
								<li class="none-separator"><a href="<?php echo url::site() . 'members/reports' ?>"><?php echo Kohana::lang('ui_main.view_all');?></a></li>
								<li><a href="#" class="rss-icon"><?php echo Kohana::lang('ui_main.rss');?></a></li>
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
							$incident_title = $incident->incident_title;
							$incident_description = text::limit_chars($incident->incident_description, 150, '...');
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
								<h4><strong><?php echo $incident_date; ?></strong><a href="<?php echo url::site() . 'members/reports/edit/' . $incident_id; ?>"><?php echo $incident_title; ?></a></h4>
								<p><?php echo $incident_description; ?></p>
							</div>
							<?php
						}
						?>
						<a href="<?php echo url::site() . 'members/reports' ?>" class="view-all"><?php echo Kohana::lang('ui_main.view_all_reports');?></a>
					</div>
				</div>
				<div class="column-1">
					<!-- box -->
					<div class="box">
						<h3><?php echo Kohana::lang('ui_admin.my_profile');?></h3>
						<ul class="inf" style="margin-bottom:10px;">
							<li class="none-separator"><a href="<?php echo url::site() ?>members/profile"><?php echo Kohana::lang('ui_main.edit');?></a></li>
						</ul>
						<div class="member_profile">
							<div class="member_photo"><img src="<?php echo members::gravatar($user->email); ?>" width="80" /></div>
							<div class="member_info">
								<div class="member_info_row"><span class="member_info_label"><?php echo Kohana::lang('ui_admin.name');?>:</span> <?php echo html::specialchars($user->name); ?></div>

								<?php if(count($user->openid) > 0) { ?>
								<div class="member_info_row"><span class="member_info_label"><?php echo Kohana::lang('ui_admin.openids');?></span>:
									<ul>
										<?php
										foreach ($user->openid as $openid)
										{
											$openid_server = parse_url($openid->openid_server);
											echo "<li>".$openid->openid_email." (".$openid_server["host"].")</li>";
										}
										?>
									</ul>
								</div>
								<?php } ?>

								<?php
									if (isset($user->username) AND // Only show if it's set
										($user->username != '' OR $user->username != NULL) AND // Don't show if the user hasn't set a username
										(valid::email($user->username) == false) AND // Don't show if it's a valid email address because it won't work
										($user->public_profile == 1) // Only show if they've set their profile to be public
									)
									{
								?>
								<div class="member_info_row"><span class="member_info_label"><?php echo Kohana::lang('ui_main.public_profile_url');?></span>:
									<br/><a href="<?php echo url::base().'profile/user/'.$user->username; ?>"><?php echo url::base().'profile/user/'.$user->username; ?></a>
								</div>
								<?php
									}
								?>

								<div class="member_info_row"><span class="member_info_label"><?php echo Kohana::lang('ui_main.profile_color');?></span>:
									<span style="background-color:#<?php echo $user->color; ?>;width:150px;height:10px;display:inline-block;"></span>
								</div>

								<!-- NOTE: Not calculating reputation yet
								<div class="member_info_row"><span class="member_info_label"><?php echo Kohana::lang('ui_admin.reputation');?>:</span> <span class="member_reputation"><?php echo $reputation; ?></span></div> -->

							</div>
						</div>
					</div>

					<!-- badge box -->
					<div class="box">

						<h3><?php echo Kohana::lang('ui_main.badges');?></h3>
						<div style="clear:both;"></div>
						<div style="text-align:center;">
						<?php
							if(count($badges) > 0) {
								foreach($badges as $badge) {
						?>

								<div class="badge">
									<center><img src="<?php echo $badge['img_m']; ?>" alt="<?php echo Kohana::lang('ui_main.badge').' '.$badge['id'];?>" width="80" height="80" style="margin:5px;" /></center>
									<br/><strong><?php echo $badge['name']; ?></strong>
								</div>

						<?php
								}
							}else{
								echo Kohana::lang('ui_main.sorry_no_badges');
							}
						?>
						</div>
						<div style="clear:both;"></div>

					</div>

					<!-- box -->
					<div class="box">
						<h3><?php echo Kohana::lang('ui_main.quick_stats');?></h3>
						<ul class="nav-list">
							<li>
								<a href="<?php echo url::site() . 'members/reports' ?>" class="reports"><?php echo Kohana::lang('ui_admin.my_reports');?></a>
								<strong><?php echo number_format($reports_total); ?></strong>
								<ul>
									<li><a href="<?php echo url::site() . 'members/reports?status=a' ?>"><?php echo Kohana::lang('ui_main.not_approved');?></a><strong>(<?php echo $reports_unapproved; ?>)</strong></li>

								</ul>
							</li>
							<li>
								<a href="<?php echo url::site() . 'members/checkins' ?>" class="checkins"><?php echo Kohana::lang('ui_admin.my_checkins');?></a>
								<strong><?php echo $checkins; ?></strong>
							</li>
							<li>
								<a href="<?php echo url::site() . 'members/alerts' ?>" class="alerts"><?php echo Kohana::lang('ui_admin.my_alerts');?></a>
								<strong><?php echo $alerts; ?></strong>
							</li>
							<li>
								<a href="#" class="votes"><?php echo Kohana::lang('ui_admin.my_votes');?></a>
								<strong><?php echo $votes; ?></strong>
								<ul>
									<li><a href="#"><?php echo Kohana::lang('ui_admin.my_votes_up');?></a><strong>(<?php echo $votes_up; ?>)</strong></li>
									<li><a href="#"><?php echo Kohana::lang('ui_admin.my_votes_down');?></a><strong>(<?php echo $votes_down; ?>)</strong></li>
								</ul>
							</li>
							<li>
								<a href="<?php echo url::site() . 'members/private' ?>" class="messages"><?php echo Kohana::lang('ui_admin.private_messages');?></a>
								<strong><?php echo "0"; ?></strong>
							</li>
						</ul>
					</div>
				</div>
			</div>

