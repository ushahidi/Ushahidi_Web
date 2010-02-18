<?php 
/**
 * Reports upload success view page.
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
	<h2><?php print $title; ?> <span></span><a href="<?php print url::base() ?>admin/reports/download">Download Reports</a><a href="<?php print url::base() ?>admin/reports">View Reports</a><a href="<?php print url::base() ?>admin/reports/edit">Create New Report</a></h2>
	
	<h3>Upload succesful</h3>
	   <p>Succesfully imported <?php echo $imported; ?> of <?php echo $rowcount; ?> incident reports.</p>

	
	<?php if(count($notices)){  ?>  
	<h3>Notices</h3>	
		<ul>
	<?php foreach($notices as $notice)  { ?>
	<li><?php echo $notice ?></li>

	<?php } }?>
	</ul>
	</div>
</div>