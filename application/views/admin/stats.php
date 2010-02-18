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
		<a href="<?php print url::base() ?>admin/stats/reports">Report Stats</a> 
		<a href="<?php print url::base() ?>admin/stats/impact">Category Impact</a>
	</h2>
	
	<div>
		<?php
			if($stat_id == 0){ // No stat account created
		?>
				<h1 style="text-align:center">Stats Not Set Up. :o(</h1>
		<?php
			}else{
		?>
		Hello, this is the statistics section. General description going here soon. For now, browse around using the sub category links above.
		<?php
			}
		?>
	</div>
	
</div>
