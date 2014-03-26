<div class="slider-holder">
	<?php echo form::open(NULL, array('method' => 'get')); ?>
		<input type="hidden" value="0" name="currentCat" id="currentCat"/>
		<fieldset>
			<!-- HT: More info link -->
			<a class="f-clear" href="#" id="timelineMoreLink" style="font-size: 11px;"><?php echo Kohana::lang('ui_main.more_information'); ?></a>
			<!-- HT: Manual time interval for timeline select input -->
			<label for="intervalDate"><?php echo Kohana::lang('ui_main.interval'); ?>:</label>
			<select name="intervalDate" id="intervalDate"><?php echo $intervalDate; ?></select>
			<!-- HT: End of manual time interval for timeline select input -->
			<label for="startDate"><?php echo Kohana::lang('ui_main.from'); ?>:</label>
			<select name="startDate" id="startDate"><?php echo $startDate; ?></select>
			<label for="endDate"><?php echo Kohana::lang('ui_main.to'); ?>:</label>
			<select name="endDate" id="endDate"><?php echo $endDate; ?></select>
		</fieldset>
	<?php echo form::close(); ?>
</div>
<?php if (Kohana::config('settings.enable_timeline')): ?>
<div id="graph" class="graph-holder"></div>
<?php endif; ?>