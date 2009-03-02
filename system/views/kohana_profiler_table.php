<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<table class="kp-table">
<?php
foreach ($rows as $row):

$class = empty($row['class']) ? '' : ' class="'.$row['class'].'"';
$style = empty($row['style']) ? '' : ' style="'.$row['style'].'"';
?>
	<tr<?php echo $class; echo $style; ?>>
		<?php
		foreach ($columns as $index => $column)
		{
			$class = empty($column['class']) ? '' : ' class="'.$column['class'].'"';
			$style = empty($column['style']) ? '' : ' style="'.$column['style'].'"';
			$value = $row['data'][$index];
			$value = (is_array($value) OR is_object($value)) ? '<pre>'.html::specialchars(print_r($value, TRUE)).'</pre>' : html::specialchars($value);
			echo '<td', $style, $class, '>', $value, '</td>';
		}
		?>
	</tr>
<?php

endforeach;
?>
</table>