<!-- start submit comments block -->
<div class="comment-block">
	
	<h5><?php echo Kohana::lang('ui_main.leave_a_comment');?></h5>
	<?php
	if ($form_error)
	{
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
	?>
	<?php print form::open(NULL, array('id' => 'commentForm', 'name' => 'commentForm')); ?>
	<?php
	if ( ! $user)
	{
		?>
		<div class="report_row">
			<strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
			<?php print form::input('comment_author', $form['comment_author'], ' class="text"'); ?>
			</div>

			<div class="report_row">
			<strong><?php echo Kohana::lang('ui_main.email'); ?>:</strong><br />
			<?php print form::input('comment_email', $form['comment_email'], ' class="text"'); ?>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="report_row">
			<strong><?php echo $user->name; ?></strong>
		</div>
		<?php
	}
	?>
	<div class="report_row">
		<strong><?php echo Kohana::lang('ui_main.comments'); ?>:</strong><br />
		<?php print form::textarea('comment_description', $form['comment_description'], ' rows="4" cols="40" class="textarea long" ') ?>
	</div>
	<div class="report_row">
		<strong><?php echo Kohana::lang('ui_main.security_code'); ?>:</strong><br />
		<?php print $captcha->render(); ?><br />
		<?php print form::input('captcha', $form['captcha'], ' class="text"'); ?>
	</div>
	<?php
	// Action::comments_form - Runs right before the end of the comment submit form
	Event::run('ushahidi_action.comment_form');
	?>
	<div class="report_row">
		<input name="submit" type="submit" value="<?php echo Kohana::lang('ui_main.reports_btn_submit'); ?> <?php echo Kohana::lang('ui_main.comment'); ?>" class="btn_blue" />
	</div>
	<?php print form::close(); ?>
	
</div>
<!-- end submit comments block -->