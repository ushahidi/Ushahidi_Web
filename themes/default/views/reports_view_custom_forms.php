<?php if (count($form_field_names) > 0) { ?>
<div class="report-custom-forms-text">
<table>
<?php
	foreach ($form_field_names as $field_id => $field_property)
	{
		if ($field_property['field_type'] == 8)
		{
			echo "</table>";

			if (isset($field_propeerty['field_default']))
			{
				echo "<div class=\"" . $field_property['field_name'] . "\">";
			}
			else
			{
				echo "<div class=\"custom_div\">";
			}

			echo "<h2>" . $field_property['field_name'] . "</h2>";
			echo "<table>";

			continue;
		}
		elseif ($field_property['field_type'] == 9)
		{
			echo "</table></div>";
			continue;
		}

		echo "<tr>";

		// Get the value for the form field
		$value = $field_property['field_response'];

		// Check if a value was fetched
		if ($value == "")
			continue;

		if ($field_property['field_type'] == 1 OR $field_property['field_type'] > 3)
		{
			// Text Field
			// Is this a date field?
			echo "<td><strong>" . html::specialchars($field_property['field_name']) . ": </strong></td>";
			echo "<td class=\"answer\">$value</td>";
		}
		elseif ($field_property['field_type'] == 2)
		{
			// TextArea Field
			echo "<td><strong>" . html::specialchars($field_property['field_name']) . ": </strong></td>";
			echo "<td class=\"answer\">$value</tr>";
		}
		elseif ($field_property['field_type'] == 3)
		{
			echo "<td><strong>" . html::specialchars($field_property['field_name']) . ": </strong></td>";
			echo "<td class=\"answer\">" . date('M d Y', strtotime($value)) . "</td>";
		}
		//echo "</div>";
		echo "</tr>";
	}
?>
</table>
</div>
<?php } ?>