<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<kml xmlns="http://www.opengis.net/kml/2.2">
	<Document>
		<name><?php echo $kml_name; ?></name>
		<?php
		foreach ($categories as $category)
		{
			?>
			<Style id="category_<?php echo $category->id; ?>">
				<IconStyle>
					<color><?php echo "ff".$category->category_color; ?></color>
					<scale>0.8</scale>		
					<Icon>
						<href><?php
						echo url::base()."plugins/kml/views/circle_border.png";
						?></href>
					</Icon>
				</IconStyle>
			</Style>
			<?php
		}
		
		foreach ($items as $item)
		{
			?>
			<Placemark>
				<?php
				// Get the first category that this placemarker is assigned to
				foreach ($item->category as $item_category)
				{
					echo "<styleUrl>#category_".$item_category->id."</styleUrl>";
					break;
				}
				?>
				<name><?php echo htmlspecialchars($item->incident_title);?></name>
				<description>
					<?php
					echo htmlspecialchars(text::limit_words($item->incident_description, 50, "...")); 
					echo "<BR /><a href=\"".url::base().'reports/view/'.$item->id."\">More...</a>";
					?>
				</description>
				<Point>
					<coordinates><?php echo $item->location->longitude.",".$item->location->latitude; ?></coordinates>
				</Point>
			</Placemark>
			<?php
		}
		?>
	</Document>
</kml>