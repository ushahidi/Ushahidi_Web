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
	<h2>
		<?php admin::reports_subtabs("upload"); ?>
	</h2>
	<!-- report-form -->
	<div class="report-form">

		<div class = "green-box">
			<h3><?php echo Kohana::lang('ui_main.upload_successful');?></h3>
		</div>
		<div class="upload_container">
   			<p><?php echo Kohana::lang('ui_main.successfuly_imported');?> <?php echo $imported; ?> of <?php echo $rowcount; ?> <?php echo Kohana::lang('ui_main.reports');?>.</p>


		<?php if(count($notices)){  ?>  
			<h3><?php echo Kohana::lang('ui_main.notices');?></h3>	
			<ul>
			<?php foreach($notices as $notice)  { ?>
				<li><?php echo $notice ?></li>
				<?php } }?>
			</ul>
		</div>
	</div>
</div>