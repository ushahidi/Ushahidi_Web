<?php 
/**
 * MHI activity page.
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
					<?php admin::mhi_subtabs("activity"); ?>
				</h2>
				
				<form name="nav">
					<div>
						Select type of action: 
						<select name="SelectURL" onChange="document.location.href=document.nav.SelectURL.options[document.nav.SelectURL.selectedIndex].value">
						<option value="<?php echo url::base(); ?>admin/mhi/activity/"<?php if($current_log_action_id == FALSE) { ?> selected <?php } ?>>View All</option>
						<?php
							foreach($log_actions as $log_action_id => $action_name)
							{
								echo '<option value="'.url::base().'admin/mhi/activity/'.$log_action_id.'"';
								if($current_log_action_id == $log_action_id) echo ' selected';
								echo '>'.$action_name.'</option>';
							}
						?>
						</select>
					</div>
				</form>
				
				<hr/>
				
				<table class="table-graph horizontal-bar">
				<?php foreach($activity as $action) { ?>
					<tr>
						<td class="hbItem"><?php echo $action['time']; ?></td>
						<td class="hbItem"><?php echo $action['ip']; ?></td>
						<td class="hbItem"><?php echo $action['email']; ?></td>
						<td class="hbDesc">&nbsp;
							<?php echo $action['action']; ?>
							<?php if($action['notes'] != '') { ?>
								- <?php echo $action['notes']; ?>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</table>
				
			</div>
