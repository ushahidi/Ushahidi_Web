<div id="content">
	<div class="content-bg">
		<!-- start block -->
		<div class="big-block">
			<!-- green-box -->
			<div class="green-box">
				<h3><?php echo Kohana::lang('ui_main.reports_submitted');?></h3>

				<div class="thanks_msg"><a href="<?php echo
					url::site().'reports' ?>"><?php echo Kohana::lang('ui_main.reports_return');?></a><br /><br />
					<a href="<?php echo url::site('reports/submit'); ?>"><?php echo Kohana::lang('ui_main.reports_submit_new');?></a><br /><br />
					<?php echo Kohana::lang('ui_main.feedback_reports');?><a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a><br /><br />
				</div>
			</div>
		</div>
		<!-- end block -->
	</div>
</div>
