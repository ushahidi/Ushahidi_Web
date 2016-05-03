<div class="analysis-assessment-list">
	<h4>Assessment For the Following Reports:</h4>
	<ul>
	<?php
	$html = "";
	foreach ($a_ids as $a_id)
	{
		$analysis_incident = ORM::factory('incident')->find($a_id);
		if ($analysis_incident->loaded)
		{
			echo "<li>";
			echo "<a href=\"".url::site()."admin/reports/edit/".$a_id."\" target=\"_blank\">".$analysis_incident->incident_title."</a> <span class=\"analysis-mapme\">[<a href=\"javascript:analysisMapme('".$analysis_incident->location->longitude."','".$analysis_incident->location->latitude."')\">add to map</a>]</span>";
			echo "<input type=\"hidden\" name=\"a_id[]\" value=\"".$analysis_incident->id."\">";
			echo "</li>";
			
			$html .= "<div class=\"detail\"><h4>".$analysis_incident->incident_title."</h4>";
			$html .= "<div class=\"desc\" id=\"desc_".$a_id."\">".nl2br($analysis_incident->incident_description)."</div>";
			$html .= "</div>";
		}
	}
	?>
	</ul>
</div>
<h4><a href="#" id="analysis_toggle" class="new-cat">View Assessment Report Details</a></h4>
<div id="analysis_report_details">
	<?php echo $html; ?>
</div>