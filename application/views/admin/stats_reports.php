<?php 
/**
 * Feedback view page.
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
	<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/stats/hits">Hit Summary</a> <a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a> <a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a></h2>
	
	<div>
		<div style="width:500px;float:left;">
			<div id="plotarea0" style="height:250px;width:500px;" class="graph-holder"></div>
			<div id="overview0" style="margin-left:50px;margin-top:20px;width:350px;height:50px;float:left;"></div>
			<div id="choices0" style="float:right;">Show:</div>
			<div style="clear:both"></div>
		</div>
		<div style="width:500px;float:left;">
			<div id="plotarea1" style="height:250px;width:500px;" class="graph-holder"></div>
			<div id="overview1" style="margin-left:50px;margin-top:20px;width:350px;height:50px;float:left;"></div>
			<div id="choices1" style="float:right;">Show:</div>
			<div style="clear:both"></div>
		</div>
		<div style="border:solid 2px #444444;margin-left:25px;">
			<table>
				<tr><th>Date</th>
				<?php
				$date_range = array();
				$keys = array();
				foreach($raw_data as $name => $data) {
					echo '<th>'.$name.'</th>';
					$keys[] = $name;
					if(count($date_range) == 0) {
						foreach($data as $timestamp => $arr) $date_range[] = $timestamp;
					}
				}
				?>
				</tr>
				<?php
				foreach($date_range as $timestamp) {
					echo '<tr><td>'.date('M jS, Y',$timestamp).'</td>';
					foreach($keys as $name) {
						echo '<td>'.$raw_data[$name][$timestamp].'</td>';
					}
					echo '</tr>';
				}
				?>
			</table>
		</div>
	</div>
	
</div>

