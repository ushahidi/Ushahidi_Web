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
                <h2>
                    <?php admin::settings_subtabs("api"); ?>
                </h2>

            	<!-- tabs -->
            	<div class="tabs">
                    <!-- tabset -->
                    <ul class="tabset">
                        <li><a href="<?php echo url::site() ?>admin/settings/api" ><?php echo Kohana::lang('ui_admin.api_settings'); ?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/settings/api/log" <?php if ($this_page == 'apilogs') echo "class=\"active\""?>><?php echo Kohana::lang('ui_admin.api_logs');?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/settings/api/banned"><?php echo Kohana::lang('ui_admin.api_banned'); ?></a></li>
                    </ul>
                    <!-- /tabset -->

            		<!-- tab -->
            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="apiLogAction('d','DELETE', '');"><?php echo utf8::strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
            				<li><a href="#" onclick="apiLogAction('x','DELETE ALL ', '000');"><?php echo utf8::strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
            			</ul>
            		</div>
            	</div>
            	<!-- /tabs -->
            	
            	<?php if ($form_error) : ?>
            		<!-- red-box -->
            		<div class="red-box">
            			<h3><?php echo Kohana::lang('ui_main.error');?></h3>
            			<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
            		</div>
            	<?php endif; ?>
            	
                <?php if ($form_saved): ?>
            		<!-- green-box -->
            		<div class="green-box" id="submitStatus">
            			<h3><?php echo Kohana::lang('ui_admin.api_logs');?> <?php echo $form_action; ?> 
            			    <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a>
            			</h3>
            		</div>
            	<?php endif; ?>
            	
            	<!-- report-table -->
            	<?php print form::open(NULL, array('id' => 'apiLogMain', 'name' => 'apiLogMain')); ?>
            		<input type="hidden" name="action" id="action" value="" />
            		<input type="hidden" name="api_log_id[]" id="api_log_single" value="" />
            		<input type="hidden" name="api_log_ipaddress" id="api_log_ipaddress" value="" />
            		<div class="table-holder">
            			<table class="table">
            				<thead>
            					<tr>
            						<th class="col-1">
            						<input id="checkallapilogs" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'api_log_id[]' )" /></th>
            						<th class="col-2"><?php echo Kohana::lang('ui_admin.parameters_used');?></th>
            						<th class="col-3"><?php echo Kohana::lang('ui_admin.task_performed');?></th>
            						<th class="col-3"><?php echo Kohana::lang('ui_admin.total_records');?></th>
            						<th class="col-3"><?php echo Kohana::lang('ui_admin.ip_address');?></th>
            						<th class="col-3"><?php echo Kohana::lang('ui_admin.date_time');?></th>
            						<th class="col-4"><?php echo Kohana::lang('ui_admin.actions');?></th>
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
            				<?php if ($total_items == 0): ?>
            					<tr>
            						<td colspan="7" class="col">
            							<h3><?php echo Kohana::lang('ui_admin.no_result_display_msg');?></h3>
            						</td>
            					</tr>
            				<?php endif; ?>
            				
            				<?php
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
            							<td class="col-1">
            							    <input name="api_log_id[]" id="api_log" value="<?php echo $api_log_id; ?>" type="checkbox" class="check-box"/>
            							</td>
            							<td class="col-2">
            							    <?php echo (is_array(@unserialize($api_parameters)))
            							        ? implode(",",@unserialize($api_parameters))
            							        : @unserialize($api_parameters); 
            							    ?>
            							</td>
            							<td class="col-3"><?php echo $api_task;?></td>
            							<td class="col-3"><?php echo $api_records;?></td>
            							<td class="col-3"><?php echo $api_ipaddress;?></td>
            							<td class="col-3"><?php echo $api_date; ?></td>
            							<td class="col-4">
            								<ul>
            								    <li>
            								        <?php if ( ! isset($api_log->ban_id)): ?>
            								        <a href="#" class="del" onclick="apiLogAction('b', 'BAN', '<?php echo $api_log_id; ?>');">
            								            <?php echo utf8::strtoupper(Kohana::lang('ui_admin.banip_action')); ?>
            								        </a>
            								        <?php else: ?>
            								            <span><?php echo utf8::strtoupper(Kohana::lang('ui_admin.banip_action')); ?></span>
            								        <?php endif; ?>
            								    </li>
            								    <li>
            								        <a href="#" class="del" onclick="apiLogAction('d','DELETE', '<?php echo $api_log_id; ?>');">
            								            <?php echo utf8::strtoupper(Kohana::lang('ui_admin.delete_action')) ;?>
            								        </a>
            								    </li>
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