<div class="grid-wrapper">
<?php
$g = "#00FB34";
$y = "#FCFC39";
$o = "#FF9924";
$r = "#FF1314";

$row_colors = array();
$row_colors[1] = array($g, $g, $y, $y, $o, $y);
$row_colors[2] = array($g, $g, $y, $o, $o, $y);
$row_colors[3] = array($g, $y, $y, $o, $r, $o);
$row_colors[4] = array($y, $y, $o, $o, $r, $r);
$row_colors[5] = array($o, $o, $o, $r, $r, $r);
$row_colors[6] = array($y, $y, $o, $r, $r, $r);

$sourcequal_array = array();
$sourcequal_array[1] = "A";
$sourcequal_array[2] = "B";
$sourcequal_array[3] = "C";
$sourcequal_array[4] = "D";
$sourcequal_array[5] = "E";
$sourcequal_array[6] = "F";

for ($i=1; $i < 7; $i++)
{
	if ($source_qual >= 1 AND $source_qual <= 6
		AND $info_qual >= 1 AND $info_qual <= 6) {
		$incident_qual = utf8::strtoupper($sourcequal_array[$source_qual].$info_qual);
	}
	else
	{
		$incident_qual = "--";
	}
	?>
	<div class="grid-row">
		<div class="left1" style="background-color:<?php echo $row_colors[$i][0]; ?>;">
			<div <?php
				$this_box = utf8::strtoupper($sourcequal_array[$i]."1");
				if ($incident_qual == $this_box)
				{
					echo " class=\"selected\"";
				}
			?>></div>
		</div>
		<div class="left2" style="background-color:<?php echo $row_colors[$i][1]; ?>;">
			<div <?php
				$this_box = utf8::strtoupper($sourcequal_array[$i]."2");
				if ($incident_qual == $this_box)
				{
					echo " class=\"selected\"";
				}
			?>></div>		
		</div>
		<div class="left3" style="background-color:<?php echo $row_colors[$i][2]; ?>;">
			<div <?php
				$this_box = utf8::strtoupper($sourcequal_array[$i]."3");
				if ($incident_qual == $this_box)
				{
					echo " class=\"selected\"";
				}
			?>></div>		
		</div>
		<div class="left4" style="background-color:<?php echo $row_colors[$i][3]; ?>;">
			<div <?php
				$this_box = utf8::strtoupper($sourcequal_array[$i]."4");
				if ($incident_qual == $this_box)
				{
					echo " class=\"selected\"";
				}
			?>></div>		
		</div>
		<div class="left5" style="background-color:<?php echo $row_colors[$i][4]; ?>;">
			<div <?php
				$this_box = utf8::strtoupper($sourcequal_array[$i]."5");
				if ($incident_qual == $this_box)
				{
					echo " class=\"selected\"";
				}
			?>></div>		
		</div>
		<div class="right" style="background-color:<?php echo $row_colors[$i][5]; ?>;">
			<div <?php
				$this_box = utf8::strtoupper($sourcequal_array[$i]."6");
				if ($incident_qual == $this_box)
				{
					echo " class=\"selected\"";
				}
			?>></div>		
		</div>
		<div style="clear:both;"></div>
	</div>
	<?php
}
?>
</div>
<div style="clear:both;">
<div class="grid-key">
	<h4>KEY:</h4>
	<div class="grid-key-row">
		<div class="grid-key-swatch" style="background-color:<?php echo $g; ?>;"></div>
		Accept
	</div>
	<div class="grid-key-row">
		<div class="grid-key-swatch" style="background-color:<?php echo $y; ?>;"></div>
		Tend To Accept
	</div>
	<div class="grid-key-row">
		<div class="grid-key-swatch" style="background-color:<?php echo $o; ?>;"></div>
		Tend To Reject
	</div>
	<div class="grid-key-row">
		<div class="grid-key-swatch" style="background-color:<?php echo $r; ?>;"></div>
		Reject
	</div>			
</div>