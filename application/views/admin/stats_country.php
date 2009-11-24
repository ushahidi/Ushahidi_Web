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
	
	<h2><?php echo $title; ?> 
		<a href="<?php print url::base() ?>admin/stats/hits">Hit Summary</a> 
		<a href="<?php print url::base() ?>admin/stats/country" class="active">Country Breakdown</a> 
		<a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a> 
		<a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a>
	</h2>
	
	<div>
		<img src="<?php echo $visitor_map; ?>" />
		<table style="width:200px;">
		<tr><th>Country</td><th>Uniques</td></tr>
		<?php
			foreach($countries as $date => $country){
				echo '<tr><td colspan="2"><br/>'.$date.'</td></tr>';
				foreach($country as $code => $arr) {
					echo '<tr><td><img src="'.$arr['logo'].'" /> '.$arr['label'].'</td><td>'.$arr['uniques'].'</td></tr>';
				}
			}
		?>
		</table>
	</div>
	
</div>