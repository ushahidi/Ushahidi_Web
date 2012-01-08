<div id="content">
	<div class="content-bg">
		<!-- start block -->
		<div class="big-block">
			<h1><?php echo Kohana::lang('ui_main.reports_submit_new');?></h1>
			<!-- green-box -->
			<div class="green-box">
				<h3><?php echo Kohana::lang('ui_main.reports_submitted');?></h3>

				<div class="thanks_msg"><a href="<?php echo
					url::site().'reports' ?>"><?php echo Kohana::lang('ui_main.reports_return');?></a><br /><br /><br />
					<?php echo Kohana::lang('ui_main.feedback_reports');?><br /><br />
					<?php 
					print form::open('http://feedback.ushahidi.com/fillsurvey.php?sid=2', array('target'=>'_blank'));
					print form::hidden('alert_code', $_SERVER['SERVER_NAME']);
					print "&nbsp;&nbsp;";
					print form::submit('button', Kohana::lang('ui_main.feedback'), ' class=btn_gray ');
					print form::close();
					?>
				</div>
			</div>
		</div>
		<!-- end block -->
	</div>
</div>