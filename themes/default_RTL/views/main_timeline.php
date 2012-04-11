<div class="slider-holder">
	<form action="">
		<input type="hidden" value="0" name="currentCat" id="currentCat"/>
		<fieldset>
			<div class="play"><a href="#" id="playTimeline"><?php echo Kohana::lang('ui_main.play'); ?></a></div>
			<label for="startDate"><?php echo Kohana::lang('ui_main.from'); ?>:</label>
			<select name="startDate" id="startDate"><?php echo $startDate; ?></select>
			<label for="endDate"><?php echo Kohana::lang('ui_main.to'); ?>:</label>
			<select name="endDate" id="endDate"><?php echo $endDate; ?></select>
		</fieldset>
	</form>
</div>
<div id="graph" class="graph-holder"></div>