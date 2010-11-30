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
		<h4 class="version"><?php print Kohana::lang('upgrade.upgrade_title_text_1') ?><?php print $current_version; ?> <?php print Kohana::lang('upgrade.upgrade_title_text_2') ?> <?php print $environment; ?></h4>
	</div>
	<div class="head">
		<h4><?php print Kohana::lang('upgrade.upgrade_available') ?></h4>
	</div>
	<div class="settings_holder">
		<strong><u>Ushahidi <?php echo $release_version ?></u></strong>
		<?php if (isset($critical)) echo "(<strong style=\"color:#FF0000\">".Kohana::lang('ui_admin.critical_upgrade')."</strong>)";?>
        <?php if (is_array($changelogs)) { ?>
		<ul>
            <?php foreach ( $changelogs as $changelog ) { ?>
			<li><?php print $changelog ?></li>
            <?php } ?>
		</ul>
        <?php } ?>	       
	</div>
	
	<div class="head">
		<h4><?php print Kohana::lang('upgrade.upgrade_automatic'); ?> (BETA!)</h4>
	</div>
	<div class="settings_holder">
		<?php print form::open(NULL, array('id' => 'upgradeMain', 'name' => 'upgradeMain')); ?>
			<p>
		   		<?php print form::label('chk_db_backup_box', Kohana::lang('upgrade.upgrade_db_text_5'));?>
			   	<?php print form::checkbox('chk_db_backup_box', '1');?>
			</p>
		    <input type="button" id="upgrade" name="button" value="<?php echo Kohana::lang('upgrade.upgrade_continue_btn_text');?>" class="login_btn" onClick="showFTP();" />
			<div class="report-form ftp-settings" id="ftp_settings">
				<div class="row">
					<h4 style="padding-top:0;">To Continue with the one-click upgrade, the following information is required for the FTP server that your website is hosted on.</h4>
				</div>
				<div class="row">
					<h4>FTP Hostname: <span>Example: "localhost"</span></h4>
					<?php print form::input('ftp_server', $ftp_server, ' class="text title_2"'); ?>
				</div>
				<div class="row">
					<h4>FTP User Name:</h4>
					<?php print form::input('ftp_user_name', $ftp_user_name, ' class="text title_2"'); ?>
				</div>
				<div class="row">
					<h4>FTP Password:</h4>
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
		<p><?php echo Kohana::lang('upgrade.upgrade_text_2');?>
.</p>
		<p><?php echo Kohana::lang('upgrade.upgrade_text_3');?>:</p>
		<ul>
			<li> <strong>applications/config/</strong> </li>
			<li> <strong>applications/cache/</strong> </li>
			<li> <strong>applications/logs/</strong> </li>
			<li> <strong><?php echo Kohana::config('upload.relative_directory'); ?></strong> </li>
		</ul>
	</div>	

</div>
