<?php
/**
 * Current Version View File.
 *
 * Used to render the HTML for the ajax call to find out if this Ushahidi instance is running the latest version
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     John Etherton <john@ethertontech.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

<?php if ((Kohana::config('config.enable_auto_upgrader') == TRUE)) {?>
    <?php if (( !empty($version)) AND (url::current() != "admin/upgrade")) { ?>
        <div id="update-info">
        <?php echo Kohana::lang('ui_admin.ushahidi');?> <?php echo $version; ?>
            <?php echo Kohana::lang('ui_admin.version_available');?>
            <a href="<?php echo url::site() ?>admin/upgrade" title="upgrade ushahidi"><?php echo Kohana::lang('ui_admin.update_link');?></a>
        </div>
    <?php } ?>
<?php }?>

<?php 
if ( Kohana::config('config.enable_ver_sync_warning') == TRUE) {
  if ( ( url::current() != "admin/upgrade") && (Kohana::config('version.ushahidi_version') != Kohana::config('settings.ushahidi_version'))) { ?>
    <div id="update-info">
      <?php echo Kohana::lang('upgrade.upgrade_warning_software_version'); ?><br />
      version.php: <?php echo Kohana::config('version.ushahidi_version') ?> &nbsp; <?php echo Kohana::lang('upgrade.upgrade_database'); ?>  <?php echo Kohana::config('settings.ushahidi_version') ?>
    </div>
  <?php
  }
  if (( url::current() != "admin/upgrade") && (Kohana::config('version.ushahidi_db_version') != Kohana::config('settings.db_version'))) { ?>
    <div id="update-info">
      <?php echo Kohana::lang('upgrade.upgrade_warning_db_version'); ?><br />
      version.php: <?php echo Kohana::config('version.ushahidi_db_version') ?> &nbsp; <?php echo Kohana::lang('upgrade.upgrade_database'); ?>  <?php echo Kohana::config('settings.db_version') ?>
    </div>
<?php
  }
}
?>