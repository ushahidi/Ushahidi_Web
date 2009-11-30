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
		<a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a> 
		<a href="<?php print url::base() ?>admin/stats/reports" class="active">Report Stats</a> 
		<a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a>
	</h2>
	
	<?php echo $reports_chart; ?>
	<?php echo $report_status_chart; ?>

</div>

