<?php 
/**
 * Upgrade table upgrade view page.
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
<div id="ushahidi_install_container" class="advanced">
<div class="bg">
	<h2><?php echo $title; ?></h2>
	<div class="table-holder">
   		<p>I have detected that you have an old database version.</p>
       	<p>So, I'm going to upgrade your database from version <?php print $db_version; ?> to the newest database.</p>
       	<p>Click on the "Upgrade" button and just chilax as I perform the magic.</p>
       	<p>Oh, also if you want me to backup your database, just thick the check button below and I will do that for you in a breeze.</p>
       	<?php print form::open(NULL, array('id' => 'upgradeDb', 'name' => 'upgradeDb')); ?>
       	<p>
       		<?php print form::label('chk_db_backup_box', 'Backup database?(<strong style="color:#FF0000;"> Highly recommended. </strong>)');?>
		   	<?php print form::checkbox('chk_db_backup_box', '1');?>
		</p>	
        <p><input type="submit" id="upgrade" name="submit" value="<?php echo Kohana::lang('upgrade.upgrade_db_btn_text');?>" class="login_btn" /></p>                
        <?php print form::close();?>
	</div>
</div>
</div>
