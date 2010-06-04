<div class="slider-holder">
	<form action="">
		<input type="hidden" value="0" name="currentCat" id="currentCat">
		<fieldset>
			<div class="play"><a href="#" id="playTimeline">PLAY</a></div>
			<label for="startDate">From:</label>
			<select name="startDate" id="startDate"><?php echo $startDate; ?></select>
			<label for="endDate">To:</label>
			<select name="endDate" id="endDate"><?php echo $endDate; ?></select>
		</fieldset>
	</form>
</div>
<div id="graph" class="graph-holder"></div>