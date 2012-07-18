<?php 
/**
 * Upgrade overview view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<div class="bg">
	<h2><?php echo $title; ?></h2>
	<?php if( $release_version == Kohana::config('settings.ushahidi_version') ) { ?>
	<div class="settings_holder">
		<h4><?php print Kohana::lang('upgrade.upgrade_status_info'); ?></h4>
		<p><?php print"You do not need to upgrade."?></p>
	</div>
	<?php } ?>
	<?php if( $release_version > Kohana::config('settings.ushahidi_version') ) { ?>
	<div class="head">
		<h4 class="version"><?php print Kohana::lang('upgrade.upgrade_title_text', array($current_version, $current_db_version, $environment)); ?></h4>
	</div>
	<div class="head">
		<h4><?php print Kohana::lang('upgrade.upgrade_available') ?></h4>
	</div>
	<div class="settings_holder">
		<strong><u><?php print Kohana::lang('upgrade.ushahidi_release_version', array($release_version)); ?></u></strong>
		<?php if (isset($critical)) echo "(<strong style=\"color:#FF0000\">".Kohana::lang('ui_admin.critical_upgrade')."</strong>)";?>
		<?php if (is_array($changelogs)) { ?><br />
		<?php echo Kohana::lang('upgrade.upgrade_db_version', array($release_db_version)); ?>
		<ul>
			<?php foreach ( $changelogs as $changelog ) { ?>
			<li><?php print $changelog ?></li>
			<?php } ?>
		</ul>
		<?php } ?>
	</div>
	
	<div class="head">
		<h4><?php print Kohana::lang('upgrade.upgrade_automatic'); ?> (<?php print Kohana::lang('upgrade.beta'); ?>)</h4>
	</div>
	<div class="settings_holder">
		<?php print form::open(NULL, array('id' => 'upgradeMain', 'name' => 'upgradeMain')); ?>
			<p>
				<?php print form::label('chk_db_backup_box', Kohana::lang('upgrade.upgrade_db_text_5'));?>
				<?php print form::checkbox('chk_db_backup_box', '1', 1);?>
			</p>
			<input type="button" id="upgrade" name="button" value="<?php echo Kohana::lang('upgrade.upgrade_continue_btn_text');?>" class="login_btn" onClick="showFTP();" />
			<div class="report-form ftp-settings" id="ftp_settings">
				<div class="row">
				<h4 style="padding-top:0;"><?php print Kohana::lang('upgrade.upgrade_ftp_text'); ?></h4>
				</div>
				<div class="row">
					<h4><?php print Kohana::lang('upgrade.upgrade_ftp_hostname'); ?></h4>
					<?php print form::input('ftp_server', $ftp_server, ' class="text title_2"'); ?>
				</div>
				<div class="row">
					<h4><?php print Kohana::lang('upgrade.upgrade_ftp_username'); ?></h4>
					<?php print form::input('ftp_user_name', $ftp_user_name, ' class="text title_2"'); ?>
				</div>
				<div class="row">
					<h4><?php print Kohana::lang('upgrade.upgrade_ftp_password'); ?></h4>
					<?php print form::password('ftp_user_pass', "", ' class="text title_2"'); ?>
				</div>
				<div class="row" style="clear:both;margin-top:10px;">
					<input type="submit" id="upgrade" name="submit" value="<?php echo Kohana::lang('upgrade.upgrade_continue_btn_text');?>" class="login_btn" />
				</div>
			</div>
		<?php print form::close();?>
	</div>
	
	<div class="head">
		<h4><?php print Kohana::lang('upgrade.upgrade_manual');?></h4>
	</div>
	<div class="settings_holder">
		<p><?php echo Kohana::lang('upgrade.upgrade_text_1');?>.</p>
		<p><?php echo Kohana::lang('upgrade.upgrade_text_2');?></p>
		<?php if(isset($download)) { $url = "<a href=\"$download\">$download</a></dd>"?>

		<p><?php echo Kohana::lang('upgrade.upgrade_text_3')." ".$url;?></p><?php } ?>

		<p><?php echo Kohana::lang('upgrade.upgrade_text_4');?></p>
	</div>	
	<?php } ?>
</div>
