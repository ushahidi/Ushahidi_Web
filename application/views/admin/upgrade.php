<?php 
/**
 * Upgrade overview view page.
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
	<h2><?php echo $title; ?></h2>
	
	<div class="head">
		<h4 class="version">You are currently using Ushahidi v<?php echo $current_version; ?> running on <?php echo $environment; ?></h4>
	</div>
	<div class="head">
		<h4>Available Updates</h4>
	</div>
	<div class="settings_holder">
		<strong><u>Ushahidi <?php echo $release_version ?></u></strong>
        <?php if (is_array($changelogs)) { ?>
		<ul>
            <?php foreach ( $changelogs as $changelog ) { ?>
			<li><?php print $changelog ?></li>
            <?php } ?>
		</ul>
        <?php } ?>
	</div>
	
	<div class="head">
		<h4>Automatic Upgrade</h4>
	</div>
	<div class="settings_holder">
		<?php print form::open(NULL, array('id' => 'upgradeMain', 'name' => 'upgradeMain')); ?>
			<input type="submit" id="upgrade" name="submit" value="<?php echo Kohana::lang('upgrade.upgrade_continue_btn_text');?>" class="login_btn" />
		<?php print form::close();?>
	</div>
	
	<div class="head">
		<h4>Manual Upgrade</h4>
	</div>
	<div class="settings_holder">
		<p><?php echo Kohana::lang('upgrade.upgrade_text_1');?>.</p>
		<p><?php echo Kohana::lang('upgrade.upgrade_text_2');?>.</p>
		<p><?php echo Kohana::lang('upgrade.upgrade_text_3');?>:</p>
		<ul>
			<li> <strong>applications/config/</strong> </li>
			<li> <strong>applications/cache/</strong> </li>
			<li> <strong>applications/logs/</strong> </li>
			<li> <strong><?php echo Kohana::config('upload.relative_directory'); ?></strong> </li>
		</ul>
		<img src="<?php echo url::base() ?>media/img/admin/icon-zip.gif" />&nbsp;<a href="#">Download (.zip)</a>
	</div>	

</div>
