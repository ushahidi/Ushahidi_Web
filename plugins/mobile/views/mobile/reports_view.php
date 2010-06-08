<div class="report_info">
	<div class="verified <?php
	if ($incident->incident_verified == 1)
	{
		echo "verified_yes";
	}
	?>">
	Verified
	<br /><?php
	if ($incident->incident_verified == 1)
	{
		echo "<span>YES</span>";
	}
	else
	{
		echo "<span>NO</span>";
	}
	?></div>
	<h2>This is the title</h2>
	<ul class="details">
		<li>
			<small>Location</small>: 
			<?php echo $incident->location->location_name; ?>
		</li>
		<li>
			<small>Date</small>: 
			<?php echo date('M j Y', strtotime($incident->incident_date)); ?>
		</li>
		<li>
			<small>Time</small>: 
			<?php echo date('H:i', strtotime($incident->incident_date)); ?>
		</li>		
		<li>
			<small>Description</small>: <br />
			<?php echo $incident->incident_description; ?>
		</li>
	</ul>
</div>
<div style="clear:both;"></div>
<div id="map_canvas"></div>