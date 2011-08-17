<div id="content">
	<div class="content-bg">
		<div style="padding:25px;">

			<div style="float:left;height:250px;width:160px;text-align:center;">
				<div><img src="<?php echo members::gravatar($user->email,160); ?>" width="160" height="160" /></div>
				<div style="width:160px;height:20px;background-color:#<?php echo $user->color; ?>"></div>
				<?php if($logged_in_user){ ?>
					<div><?php echo Kohana::lang('ui_main.this_is_your_profile'); ?><br/><a href="<?php echo url::site();?>members/"><?php echo Kohana::lang('ui_main.manage_your_account'); ?></a></div>
				<?php }else{ ?>
					<div><?php echo Kohana::lang('ui_main.is_this_your_profile'); ?>
					<?php if($logged_in_id){ ?>
						<a href="<?php echo url::site();?>logout/front"><?php echo Kohana::lang('ui_admin.logout');?></a>
					<?php }else{ ?>
						<a href="<?php echo url::site();?>members/"><?php echo Kohana::lang('ui_main.login'); ?></a>
					<?php } ?>
					</div>
				<?php } ?>
			</div>

			<div style="float:left;padding-left:25px;width:400px;">
				<h4><?php echo $user->username; ?> <span style="background-color:#585858;font-size:18px;padding:6px;-moz-border-radius:15px;border-radius:15px;color:#FFFFFF;"><?php echo $reputation; ?></span></h4>

				<div class="report-additional-reports">
					<h4><?php echo Kohana::lang('ui_main.reports_by_this_user');?></h4>
					<?php foreach($reports as $report) { ?>
						<div class="rb_report">
							<h5><a href="<?php echo url::site(); ?>reports/view/<?php echo $report->id; ?>"><?php echo $report->incident_title; ?></a></h5>
							<p class="r_date r-3 bottom-cap"><?php echo date('H:i M d, Y', strtotime($report->incident_date)); ?></p>
							<p class="r_location"><?php echo $report->location->location_name; ?></p>
						</div>
					<?php } ?>
				</div>

			</div>

			<?php if(count($badges) > 0) { ?>
			<div style="float:left;padding-left:25px;width:275px;">

				<h4><?php echo Kohana::lang('ui_main.badges');?></h4>

				<?php foreach($badges as $badge) { ?>

					<div class="badge r-5">
					<img src="<?php echo url::base().Kohana::config('upload.relative_directory').'/'.$badge['img_m']; ?>" alt="<?php echo Kohana::lang('ui_main.badge').' '.$badge['id'];?>" width="80" height="80" style="margin:5px;" />
					<br/><strong><?php echo $badge['name']; ?></strong>
				</div>

				<?php } ?>

				<div style="clear:both;"></div>

			</div>
			<?php } ?>

			<div style="clear:both;"></div>

		</div>
	</div>
</div>
