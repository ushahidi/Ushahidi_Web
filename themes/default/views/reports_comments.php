<?php if(count($incident_comments) > 0) { ?>

<div class="report-comments">
					
	<h5><?php echo Kohana::lang('ui_main.comments'); ?></h5>

	<?php foreach($incident_comments as $comment) { ?>
		<div class="report-comment-box">
	
			<div>
				<strong><?php echo $comment->comment_author; ?></strong>&nbsp;(<?php echo date('M j Y', strtotime($comment->comment_date)); ?>)
			</div>
			
			<div><?php echo $comment->comment_description; ?></div>
	
	
	
				<div style="float:left;">
					<?php echo Kohana::lang('ui_main.reports_description');?>:&nbsp;
					<a href="javascript:rating('<?php echo $comment->id; ?>','add','comment','cloader_<?php echo $comment->id; ?>')"><img id="cup_<?php echo $comment->id; ?>" src="<?php echo url::base(); ?>media/img/up.png" alt="UP" title="UP" border="0" /></a>&nbsp;
					<a href="javascript:rating('<?php echo $comment->id; ?>','subtract','comment','cloader_<?php echo $comment->id; ?>')"><img id="cdown_<?php echo $comment->id; ?>" src="<?php echo url::base(); ?>media/img/down.png" alt="DOWN" title="DOWN" border="0" /></a>&nbsp;
				</div>
	
				<div class="rating_value" id="crating_<?php echo $comment->id; ?>" style="float:left;">
					<?php echo $comment->comment_rating; ?>
				</div>
	
				<div id="cloader_<?php echo $comment->id; ?>" class="rating_loading" ></div>
	
				<div style="clear:both;"></div>
	
	
		</div>
	<?php } ?>
	
</div>

<?php } ?>