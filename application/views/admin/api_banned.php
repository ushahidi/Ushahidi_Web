<?php 
/**
 * API banned view page.
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
                        <li><a href="<?php echo url::site() ?>admin/settings/api/log"><?php echo Kohana::lang('ui_admin.api_logs');?></a></li>
                        <li>
                            <a href="<?php echo url::site() ?>admin/settings/api/apibanned" <?php if ($this_page == 'apibanned') echo "class=\"active\""?>>
                                <?php echo Kohana::lang('ui_admin.api_banned'); ?>
                            </a>
                        </li>
                    </ul>
                    <!-- /tabset -->

            		<!-- tab -->
            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="apiBannedAction('d','UNBAN', '');"><?php echo strtoupper(Kohana::lang('ui_admin.api_unban')); ?></a></li>
            				<li><a href="#" onclick="apiBannedAction('x','UNBAN ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.api_unban_all')); ?></a></li>
            			</ul>
            		</div>
            		<!-- /tab -->
            	</div> 
            	<!-- /tabs -->
	
            	<?php if ($form_error): ?>
            		<!-- red-box -->
            		<div class="red-box">
            			<h3>Error!</h3>
            			<ul>Please verify that you have checked an item</ul>
            		</div>
            	<?php endif; ?>

                <?php if ($form_saved): ?>
            		<!-- green-box -->
            		<div class="green-box" id="submitStatus">
            			<h3>API <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
            		</div>
            	<?php endif; ?>
            	
            	<!-- report-table -->
            	<?php print form::open(NULL, array('id' => 'apiBannedMain', 'name' => 'apiBannedMain')); ?>
            		<input type="hidden" name="action" id="action" value="">
            		<input type="hidden" name="api_banned_id[]" id="api_banned_single" value="">
            		<div class="table-holder">
            			<table class="table">
            				<thead>
            					<tr>
            						<th class="col-1">
            						<input id="checkallapibanned" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'api_banned_id[]' )" /></th>
            						<th class="col-3">IP Address</th>
            						<th class="col-3">DateTime</th>
            						<th class="col-4">Actions</th>
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
            			<?php if ($total_items == 0): ?>
            			    <tr>
            					<td colspan="4" class="col">
            						<h3>No Results To Display!</h3>
            					</td>
            				</tr>
            			<?php endif; ?>	
            				
            				<?php	
            					foreach ($api_bans as $api_ban)
            					{
            						$api_ban_id = $api_ban->id;
            						$banned_ipaddress = $api_ban->banned_ipaddress;
            						$banned_date = $api_ban->banned_date;						
            					?>
            						<tr>
            							<td class="col-1"><input name="api_banned_id[]" id="api_banned" value="<?php echo $api_ban_id; ?>" type="checkbox" class="check-box"/></td>
            							<td class="col-3"><?php echo $banned_ipaddress;?></td>
            							<td class="col-3"><?php echo $banned_date;?></td>
            							<td class="col-4">
            								<ul>	
            								 <li><a href="#" class="del" onclick="apiBannedAction('d','UNBAN', '<?php echo $api_ban_id; ?>');">Unban</a></li>
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