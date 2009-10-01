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
	<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/stats/hits">Hit Summary</a> <a href="<?php print url::base() ?>admin/stats/country">Country Breakdown</a></h2>
	
	<div>
		<style type="text/css">
		/* Fixes legend */
		.legend table { width: auto; } 
		</style>
		<div style="width:500px">
			<div id="plotarea" style="height:250px;width:500px;" class="graph-holder"></div>
			<div id="overview" style="margin-left:50px;margin-top:20px;width:350px;height:50px;float:left;"></div>
			<div id="choices" style="float:right;">Show:</div>
			<div style="clear:both"></div>
		</div>
	</div>
	
</div>

