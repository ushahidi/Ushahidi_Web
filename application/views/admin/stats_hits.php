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
		<style type="text/css">
		/* Fixes legend */
		.legend table { width: auto; } 
		</style>
		<div style="width:500px;float:left;">
			<div id="plotarea0" style="height:250px;width:500px;" class="graph-holder"></div>
			<div id="overview0" style="margin-left:50px;margin-top:20px;width:350px;height:50px;float:left;"></div>
			<div id="choices0" style="float:right;">Show:</div>
			<div style="clear:both"></div>
		</div>
		<div style="float:left;width:400px;border:solid 2px #444444;margin-left:25px;">
			<table>
				<tr><th>Date</th><th>Uniques</th><th>Visits</th><th>Pageviews</th></tr>
				<?php
				$unique_total = 0;
				$visit_total = 0;
				$pageview_total = 0;
				foreach($raw_data as $timestamp => $data) {
					if($data['uniques'] != 0 && $data['visits'] != 0 && $data['pageviews'] != 0){
						echo '<tr><td>'.date('M jS, Y',$timestamp).'</td>'
							.'<td>'.$data['uniques'].'</td><td>'.$data['visits'].'</td>'
							.'<td>'.$data['pageviews'].'</td></tr>';
						$unique_total += $data['uniques'];
						$visit_total += $data['visits'];
						$pageview_total += $data['pageviews'];
					}
				}
				?>
				<tr>
					<td><strong>Total</strong></td>
					<td><?php echo $unique_total; ?></td>
					<td><?php echo $visit_total; ?></td>
					<td><?php echo $pageview_total; ?></td>
				</tr>
			</table>
		</div>
	</div>
	
</div>

