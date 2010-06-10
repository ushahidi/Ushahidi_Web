<div class="report_list">
	<div class="block">
		<?php
		if ($category AND $category->loaded)
		{
			$color_css = 'class="swatch" style="background-color:#'.$category->category_color.'"';
			echo '<h2 class="other"><a href="#"><div '.$color_css.'></div>'.$category->category_title.'</a></h2>';
		}
		?>
		<div class="list">
			<ul>
				<?php
				if ($incidents->count())
				{
					foreach ($incidents as $incident)
					{
						$incident_date = $incident->incident_date;
						$incident_date = date('M j Y', strtotime($incident->incident_date));
						echo "<li><strong><a href=\"".url::base()."mobile/reports/view/".$incident->id."\">".$incident->incident_title."</a></strong>";
						echo "&nbsp;&nbsp;<i>$incident_date</i></li>";
					}
				}
				else
				{
					echo "<li>No Reports Found</li>";
				}
				?>
			</ul>
		</div>
	</div>
</div>