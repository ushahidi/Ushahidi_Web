<?php 
/**
 * Upgrade status view page.
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
		<h4><?php echo Kohana::lang('upgrade.upgrading');?>...</h4>
	</div>
	<div class="settings_holder">
		<div id="upgrade_log"></div>
	</div>
</div>
