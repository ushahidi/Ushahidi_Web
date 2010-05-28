<?php 
/**
 * Laconica view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Messages Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<div class="bg">
	<h2><?php echo $title; ?> <a href="<?php print url::site() ?>admin/messages">SMS</a> <a href="<?php print url::site() ?>admin/messages/twitter"><?php echo Kohana::lang('ui_main.twitter');?></a> <a href="<?php print url::site() ?>admin/messages/laconica"><?php echo Kohana::lang('ui_main.laconica');?></a> </h2>
	<!-- tabs -->
	<div class="tabs">
		<!-- tabset -->
		<ul class="tabset">
			<li><a href="?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.inbox');?></a></li>
			<li><a href="?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.outbox');?></a></li>
		</ul>
		<!-- tab -->
		<div class="tab">
			<ul>
				<li><!-- <a href="#" onClick="submitIds()">DELETE</a> --> <a href="#"><?php echo strtoupper(Kohana::lang('ui_main.delete_disabled'));?></a></li>
			</ul>
		</div>
	</div>
	<?php 
	if ($form_error) {
	?>
		<!-- red-box -->
		<div class="red-box">
			<h3><?php echo Kohana::lang('ui_main.error');?></h3>
			<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
		</div>
	<?php
	}

	if ($form_saved) {
	?>
		<!-- green-box -->
		<div class="green-box" id="submitStatus">
			<h3><?php echo Kohana::lang('ui_main.messages');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
		</div>
	<?php
	}
	?>
	<!-- report-table -->
	<?php  
		print form::open(NULL, array('id' => 'messagesMain', 'name' => 'messagesMain')); ?>
		<input type="hidden" name="action" id="action" value="">
		<div class="table-holder">
			<table class="table">
				<thead>
					<tr>
						<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'message_id[]' )" /></th>
						<th class="col-2"><?php echo Kohana::lang('ui_main.message_details');?></th>
						<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
						<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
					</tr>
				</thead>
				<tfoot>
					<tr class="foot">
						<td colspan="4">
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
							<td colspan="4" class="col">
								<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
							</td>
						</tr>
					<?php	
					}
					
					foreach ($laconica_mesgs as $laconica_mesg)
					{
						$laconica_mesg_id = $laconica_mesg->id;
						$laconica_mesg_from = $laconica_mesg->laconica_mesg_from;
						$incident_id = $laconica_mesg->incident_id;
						$laconica_mesg_link = $laconica_mesg->laconica_mesg_link;
						$laconica_mesg_description = $laconica_mesg->laconica_mesg;
						$laconica_mesg_date = date('Y-m-d', 
						    strtotime($laconica_mesg->laconica_mesg_date));
						?>
						<tr>
							<td class="col-1"><input name="message_id[]" id="message_id" value="<?php echo $laconica_mesg_id; ?>" type="checkbox" class="check-box"/></td>
							<td class="col-2">
								<div class="post">
									<p><?php echo $laconica_mesg_description; ?></p>
								</div>
								<ul class="info">
									<li class="none-separator"><?php echo Kohana::lang('ui_main.from');?>: <strong><a href="<?php echo $laconica_mesg_link; ?>" target="_blank"><?php echo $laconica_mesg_from; ?></a></strong>
								</ul>
							</td>
							<td class="col-3"><?php echo $laconica_mesg_date; ?></td>
							<td class="col-4">
								<ul>
									<?php
									if ($incident_id != 0) {
										echo "<li class=\"none-separator\"><a href=\"". url::site() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>".Kohana::lang('ui_main.view_report')."</strong></a></li>";
									}
									else
									{
										echo "<li class=\"none-separator\"><a href=\"". url::site() . 'admin/reports/edit?tid=' . $laconica_mesg_id ."\">".Kohana::lang('ui_main.create_report')."?</a></li>";
									}
									?>
									<li>
                                    <!-- <a href="<?php echo url::site().'admin/messages/delete/'.$laconica_mesg_id ?>" onclick="return confirm(<?php echo Kohana::lang('ui_main.action_confirm');?>)" class="del">Delete</a> --></li>
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
