<?php 
/**
 * MHI admin page.
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
				<h2>
					<?php admin::mhi_subtabs("deployments"); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='p' && $status !='s') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=p" <?php if ($status == 'p') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.pending');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.approved');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="mhiAction('a','APPROVE', '');"><?php echo strtoupper(Kohana::lang('ui_main.approve'));?></a></li>
							<li><a href="#" onclick="mhiAction('u','UNAPPROVE', '');"><?php echo strtoupper(Kohana::lang('ui_main.disapprove'));?></a></li>
							<li><a href="#" onclick="mhiAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<?php 
							if ($status == 's')
							{
								?>
								<li><a href="#" onclick="mhiAction('x','DELETE ALL SPAM', '000');"><?php echo strtoupper(Kohana::lang('ui_main.delete_spam'));?></a></li>
								<?php
							}
							?>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error)
				{
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved)
				{
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_admin.instances'); ?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'mhiMain', 'name' => 'mhiMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="instance_id[]" id="instance_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallinstances" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'instance_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_admin.instance_details');?></th>
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
								if ($total_items == 0) {
								?>
									<tr>
										<td colspan="4" class="col">
											<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
										</td>
									</tr>
								<?php	
								}
								
								foreach ($instances as $instance) {
									$instance_id = $instance->id;
									$instance_owner = $instance->lastname.', '.$instance->firstname;
									$instance_active = $instance->site_active;
									$instance_email = $instance->email;
									$instance_domain = $instance->site_domain;
									$instance_date = date('Y-m-d', strtotime($instance->site_dateadd));
									?>
									<tr>
										<td class="col-1"><input name="instance_id[]" id="instance" value="<?php echo $instance_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<h4><?php echo $instance_domain; ?>.<?php echo $domain_name; ?></h4>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.email');?>: <strong><?php echo $instance_email; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.name');?>: <strong><?php echo $instance_owner; ?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $instance_date; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><?php
												if ($instance_active)
												{
													?><a href="#" class="status_yes" onclick="mhiAction('u','UNAPPROVE', '<?php echo $instance_id; ?>');"><?php echo Kohana::lang('ui_main.approved');?></a><?php
												}
												else
												{
													?><a href="#" class="status_no" onclick="mhiAction('a','APPROVE', '<?php echo $instance_id; ?>');"><?php echo Kohana::lang('ui_main.approve');?></a><?php
												}
												?></li>
												<li>
												<li><a href="#" class="del" onclick="mhiAction('d','DELETE', '<?php echo $instance_id; ?>');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
											</ul>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>
			</div>
