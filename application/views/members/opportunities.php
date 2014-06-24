<?php
/**
 * Opportunities view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>

<div class="content-container">
	<div class="new_resource">
		<input type="submit" a href="#" class="new_resource_available" value="new_resource_available">I have a new Resource to share<a/>/>
	</div>


	<!-- content blocks -->
	<div class="content-blocks clearingfix">
		<ul class="content-column" style="width: 946px;">
			<li id="block-news" style="width: 473px;">
				<div class="content-block"><h5>In Search for</h5><table class="table-list">
	<thead>
		<tr>
			<th scope="col">Resource Needed</th>
			<th scope="col">PCV Name</th>
			<th scope="col">Date</th>
			<th scope="col">Best way to contact</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($resource_needed == 0)
		{
		?>
		<tr><td colspan="3"><?php echo Kohana::lang('ui_main.no_results');?></td></tr>

		<?php
		}
		foreach ($reporters as $reporter)
		{
			$reporter_id = $reporter->id;
			$reporter_first = text::limit_chars(html::strip_tags($reporter->$reporter_first), 50, "...", true);
			$reporter_last = text::limit_chars(html::strip_tags($reporter->$reporter_last), 50, "...", true);
			$reporter_email = $reporter->reporter_email;
			$reporter_phone = $reporter->reporter_phone;
			$reporter_date = $reporter->reporter_date;
		?>

		<tr>
			<td scope="col"><?php echo $resource_needed; ?></td>
			<td scope="col"><?php echo $reporter_first + $reporter_last; ?></td>
			<td scope="col"><?php echo $reporter_date; ?></td>
			<td scope="col"><?php echo $reporter_email . "," . $reporter_phone; ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>

	<div class="clear:both;"></div>
	</div></li> <li id="block-reports" style="width: 473px;">
	<div class="content-block"><h5>Resources available</h5><table class="table-list">
	<thead>
		<tr>
			<th scope="col">Resource Available</th>
			<th scope="col">PC Name</th>
			<th scope="col">Date Available</th>
			<th scope="col">Best way to contact</th>
			<th scope="col">Additional Information</th>
		</tr>
	</thead>
		<tbody>
				<?php
		if ($resource_available == 0)
		{
		?>
		<tr><td colspan="4"><?php echo Kohana::lang('ui_main.no_results');?></td></tr>

		<?php
		}
		foreach ($resources as $resource)
		{
			$resource_id = $resource->id;
			$PCV_name = $resource->$PCV_name;
			$resource_available = html::escape($resource->resource_available);
			$date_from = $resource->$date_from;
			$date_until = $resource->$date_until;
			$date_from = $resource->$date_from;
			$add_info = text::limit_chars(html::strip_tags($resource->add_info), 150, "...", true);
		?>

		<tr>
			<td scope="col"><?php echo $resource_available; ?></td>
			<td scope="col"><?php echo $PCV_name; ?></td>
			<td scope="col"><?php echo $date_from; ?></td>
			<td scope="col"><?php echo $date_until; ?></td>
			<td scope="col"><?php echo $contact; ?></td>
			<td scope="col"><?php echo $add_info; ?></td>
		</tr>
<?php
}
?>
		</tbody>
	</table>
</div> 

<div class="new_resource_available">
	<h3><a id ="new_resource_available">I have resources available:</a></h3>
		<div class="report_row">
			<h4>Resources Available</h4>
			<input type="text" id="resource_available" name="resource_available" value="" class="text long">
			<?php print form::input('resource_available', $form['resource_available'], ' class="text"'); ?></div>
		<div class="report_row">
			<h4>PCV or Committee Name</h4>
			<input type="text" id="PCV_name" name="PCV_name" value="" class="text long">
			<?php print form::input('PCV_name', $form['PCV_name'], ' class="text"'); ?></div>
		<div class="report_row">
			<h4>Available from:</h4>
			<input type="text" id="available_from" name="available_from" value="" class="text long">
			<?php print form::input('date_from', $form['date_from'], ' class="date"'); ?></div>
		<div class="report_row">
			<h4>Available until:</h4>
			<input type="text" id="available_from" name="available_from" value="" class="text long">
			<?php print form::input('date_until', $form['date_until'], ' class="date"'); ?></div>
		<div class="report_row">
			<h4>Best way to contact</h4>
			<input type="text" id="add_info" name="add_info" value="" class="text long">
			<?php print form::input('contact', $form['contact'], ' class="text"'); ?></div>
		<div class="report_row">
			<h4>Additional Information</h4>
			<input type="text" id="add_info" name="add_info" value="" class="text long">
			<?php print form::input('add_info', $form['add_info'], ' class="text"'); ?></div>
</div>
<div class="report_row">
			<input name="submit" type="submit" value="Submit" class="btn_submit"> </div>
	<!-- /content blocks -->

</div>