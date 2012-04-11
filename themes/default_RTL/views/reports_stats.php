<div id="report_stats">
	<table>
		<tr>
			<th><?php echo Kohana::lang('ui_main.total_reports');?></th>
			<th><?php echo Kohana::lang('ui_main.avg_reports_per_day');?></th>
			<th>% <?php echo Kohana::lang('ui_main.verified');?></th>
		</tr>
		<tr>
			<td><?php echo $total_reports; ?></td>
			<td><?php echo $avg_reports_per_day; ?></td>
			<td><?php echo $percent_verified; ?></td>
		</tr>
	</table>
</div>