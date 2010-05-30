<?php
/**
 * MHI - This is the page where users go when they login.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Page View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<div class="twocol-left">
	<div class="shadow">
		
		<h3>Your Sites</h3>
		
		<a href="<?php echo url::base() ?>mhi/logout"><?php echo Kohana::lang('ui_admin.logout');?></a>

		<a href="<?php echo url::base() ?>mhi/signup">Create New Instance</a>
		
		<a href="<?php echo url::base() ?>mhi/account">Account Settings</a>
		
		<a href="<?php echo url::base() ?>mhi/account">Your Sites</a>
		
		<table><tbody>
		<?php foreach($sites as $site) { ?>
			<tr>
				<td><a href="http://<?php echo $site->site_domain.'.'.$domain_name; ?>" target="_blank"><?php echo $site->site_domain.'.'.$domain_name; ?></a></td>
				<td><?php if($site->site_active == 1) { echo 'Active'; }else{ echo 'Pending Activation'; } ?></td>
			</tr>
		<?php } ?>
		</tbody></table>
		
	</div>
</div>
<div class="twocol-right">
	<div class="side-bar-module">
		<h4>Side Bar</h4>
		<div class="side-bar-content">
			<p>Content goes here. This will scroll with the page.</p>
		</div>
	</div>
</div>