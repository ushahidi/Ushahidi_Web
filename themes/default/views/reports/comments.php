<?php if(count($incident_comments) > 0): ?>

<div class="report-comments">

	<h5><?php echo Kohana::lang('ui_main.comments'); ?></h5>

	<?php foreach($incident_comments as $comment): ?>
		<div class="report-comment-box">

			<div>
				<strong><?php echo html::specialchars($comment->comment_author); ?></strong>&nbsp;(<?php echo date('M j Y', strtotime($comment->comment_date)); ?>)
			</div>

			<div><?php echo html::specialchars($comment->comment_description); ?></div>

		</div>
	<?php endforeach; ?>

</div>

<?php endif; ?>