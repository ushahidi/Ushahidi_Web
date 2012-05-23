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
	<h2><?php print $title; ?> <span></span><a href="<?php print url::site() ?>admin/reports/download"><?php echo Kohana::lang('ui_main.download_reports');?></a><a href="<?php print url::site() ?>admin/reports"><?php echo Kohana::lang('ui_main.view_reports');?></a><a href="<?php print url::site() ?>admin/reports/edit"><?php echo Kohana::lang('ui_main.create_report');?></a></h2>
	
	<h3><?php echo Kohana::lang('ui_main.upload_successful');?></h3>
	   <p><?php echo Kohana::lang('ui_main.succesfully_imported');?> <?php echo $imported; ?> of <?php echo $rowcount; ?> <?php echo Kohana::lang('ui_main.reports');?>.</p>

	
	<?php if(count($notices)){  ?>  
	<h3><?php echo Kohana::lang('ui_main.notices');?></h3>	
		<ul>
	<?php foreach($notices as $notice)  { ?>
	<li><?php echo $notice ?></li>

	<?php } }?>
	</ul>
	</div>
</div>