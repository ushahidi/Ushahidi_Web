<?php
/**
 * Version Sync View File.
 *
 * Used to render the HTML for warning is db or software version are mismatches
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Robbie Mackay <rm@robbiemackay.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

<?php 
if ( Kohana::config('config.enable_ver_sync_warning') == TRUE)
{
	if ( ( url::current() != "admin/upgrade") AND (Kohana::config('version.ushahidi_version') != Kohana::config('settings.ushahidi_version')))
	{ ?>
	<div id="version-sync-software" class="update-info">
		<?php echo Kohana::lang('upgrade.upgrade_warning_software_version'); ?><br />
		version.php: <?php echo Kohana::config('version.ushahidi_version') ?> &nbsp; <?php echo Kohana::lang('upgrade.upgrade_database'); ?>  <?php echo Kohana::config('settings.ushahidi_version') ?>
	</div>
	<?php
	}
	if (( url::current() != "admin/upgrade") AND (Kohana::config('version.ushahidi_db_version') != Kohana::config('settings.db_version')))
	{ ?>
	<div id="version-sync-db" class="update-info">
		<?php echo Kohana::lang('upgrade.upgrade_warning_db_version'); ?><br />
		version.php: <?php echo Kohana::config('version.ushahidi_db_version') ?> &nbsp; <?php echo Kohana::lang('upgrade.upgrade_database'); ?>  <?php echo Kohana::config('settings.db_version') ?>
	</div>
<?php
	}
} ?>