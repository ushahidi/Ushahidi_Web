<?php 
/**
 * API log view page.
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
<div class="bg">
	<h2><?php echo $title; ?> <span>(<?php echo $total_items; ?>)</span></h2>
	<!-- tabs -->
	<div class="tabs">
		<!-- tabset -->
		<ul class="tabset">
			<li><a href="<?php echo url::base() ?>admin/apilogs" <?php if($this_page == "apilogs" ) echo "class=\"active\""; ?> >Logs</a></li>
			<li><a href="<?php echo url::base() ?>admin/apilogs/apibanned" <?php if($this_page == "apibanned" ) echo "class=\"active\""; ?>>API Banned</a></li>
		</ul>
		<!-- tab -->
		<div class="tab">
			<ul>
				<li><a href="#" onclick="apiLogAction('d','DELETE', '');">DELETE</a></li>
				<li><a href="#" onclick="apiLogAction('x','DELETE ALL ', '000');">DELETE ALL </a></li>
			</ul>
		</div>
	</div> 
	<?php
	if ($form_error)
	{
	?>
		<!-- red-box -->
		<div class="red-box">
			<h3>Error!</h3>
			<ul>Please verify that you have checked an item</ul>
		</div>
	<?php
	}

	if ($form_saved)
	{
	?>
		<!-- green-box -->
		<div class="green-box" id="submitStatus">
			<h3>API Logs <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
		</div>
		<?php
	}
	?>
	<!-- report-table -->
	<?php print form::open(NULL, array('id' => 'apiLogMain', 'name' => 'apiLogMain')); ?>
		<input type="hidden" name="action" id="action" value="">
		<input type="hidden" name="api_log_id[]" id="api_log_single" value="">
		<div class="table-holder">
			<table class="table">
				<thead>
					<tr>
						<th class="col-1">
						<input id="checkallapilogs" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'api_log_id[]' )" /></th>
						<th class="col-2">Parameters Used</th>
						<th class="col-3">Task Perfomed</th>
						<th class="col-3">Total Records</th>
						<th class="col-3">IP Address</th>
						<th class="col-3">DateTime</th>
						<th class="col-4">Actions</th>
					</tr>
				</thead>
				<tfoot>
					<tr class="foot">
						<td colspan="7">
							<?php echo $pagination; ?>
						</td>
					</tr>
				</tfoot>
			<tbody>
				<?php
					if ($total_items == 0)
					{
				?>
						<tr>
							<td colspan="7" class="col">
								<h3>No Results To Display!</h3>
							</td>
						</tr>
					<?php	
					}
					foreach ($api_logs as $api_log)
					{
						$api_log_id = $api_log->id;
						$api_task = $api_log->api_task;
						$api_parameters = $api_log->api_parameters;
						$api_records = $api_log->api_records;
						$api_ipaddress = $api_log->api_ipaddress;
						$api_date = $api_log->api_date;						
					?>
						<tr>
							<td class="col-1"><input name="api_log_id[]" id="api_log" value="<?php echo $api_log_id; ?>" type="checkbox" class="check-box"/></td>
							<td class="col-2"><?php echo implode(",",unserialize($api_parameters));?></td>
							<td class="col-3"><?php echo $api_task;?></td>
							<td class="col-3"><?php echo $api_records;?></td>
							<td class="col-3"><?php echo $api_ipaddress;?></td>
							<td class="col-3"><?php echo $api_date; ?></td>
							<td class="col-4">
								<ul>	
								 <li><a href="#" class="del" onclick="apiLogAction('d','DELETE', '<?php echo $api_log_id; ?>');">Delete</a></li>
								</ul>
							</td>
						</tr>
					<?php
					}
				?>
			</tbody>
		</table>
	</div>
	<?php print form::close(); ?>
</div>