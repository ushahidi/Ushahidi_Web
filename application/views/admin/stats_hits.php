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

	<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/stats/hits">Hit Summary</a> <a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a> <a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a> <a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a></h2>
	
	<?php echo $traffic_chart; ?>
	
	<?php
		$labels = array();
		if($raw_data) {
			foreach($raw_data as $label => $data_array) {
				echo "<div style=\"width:200px;float:left;\"><h3>$label</h3>";
				$data_array = array_reverse($data_array);
				foreach($data_array as $timestamp => $count) {
					$date = date('M jS, Y',($timestamp/1000));
					echo "$date: $count<br/>";
				}
				echo "</div>";
			}
			echo "<div style=\"clear:both;\"></div>";
		}
	?>
		
</div>

