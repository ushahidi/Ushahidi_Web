<div id="content">
	<div class="content-bg">
		<div class="big-block">
			<h1><?php echo html::escape($page_title) ?></h1>
			<div class="page_text"><?php 
			echo $page_description;
			Event::run('ushahidi_action.page_extra', $page_id);
			?></div>
		</div>
	</div>
</div>
