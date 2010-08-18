<?php 
/**
 * MHI update list.
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
					<?php admin::mhi_subtabs("updatelist"); ?>
				</h2>
				
				<div>
					Use a query like <?php echo url::base(); ?>admin/mhi/updatelist?mhimassupdatedb=100&from_version=27 to do a mass update. mhimassupdatedb is the number to update. from_version are the sites of that version to update.
				</div>
				<hr/>
				
				<table class="table-graph horizontal-bar">
				<?php
				foreach($db_versions as $db => $version) {
					
					if($version == $current_version){
						$background = '#FFFFFF';
					}else{
						$background = '#FFDBE0';
					}
				?>
					<tr>
						<td class="hbItem" style="background-color:<?php echo $background; ?>"><?php echo $db; ?></td>
						<td class="hbItem" style="background-color:<?php echo $background; ?>"><?php echo $version; ?></td>
						<td class="hbItem" style="background-color:<?php echo $background; ?>">
							<?php if($version != $current_version) { ?>
								<?php
								echo form::open(NULL, array('id' => 'mhiUpdate-'.$db, 'name' => 'mhiUpdate-'.$db));
								echo form::hidden('db',$db);
								echo form::hidden('mhiupdatedb','1');
								echo form::submit('submit', 'Bring to version '.($version+1));
								echo form::close(); ?>
							<?php }else{ ?>
								Up to date
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</table>
				
			</div>
