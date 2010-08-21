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
<div id="ushahidi_install_container" class="advanced">
<div class="bg">
	<h2><?php echo $title; ?></h2>
	<div class="table-holder">
   		<p>
   		<?php //TODO: migrate this to i18n/en_US/ ?>
   		<p>This upgrade uitily is going to upgrade your database to Ushahidi's current 
	database version.</p>
       		
    <p>Also, you can ask it to backup your current database before it does the database upgrade.</p>
    
    Follow the file upgrade process below. After that is done, click on the "Continue" button to proceed to 
    upgrade the database. This upgrade process assumes you have not done any customization to the core files of Ushahidi, otherwise 
    you have to manually merge the changes to current code base of ushahidi you are going to download.
    
    	<?php print form::open(NULL, array('id' => 'upgradeMain', 'name' => 'upgradeMain')); ?>
      	<p><?php echo Kohana::lang('upgrade.upgrade_text_1');?>.</p>

       	<p><?php echo Kohana::lang('upgrade.upgrade_text_2');?>.</p>

       	<p><?php echo Kohana::lang('upgrade.upgrade_text_3');?>:</p>
       	<ul>
       		<li>
            	<strong>applications/config/</strong>
            </li>
           	<li>
            	<strong>applications/cache/</strong>
          	</li>
          	<li>
            	<strong>applications/logs/</strong>
          	</li>
          	<li>
             	<strong><?php echo Kohana::config('upload.relative_directory'); ?></strong>
          	</li>
        </ul>
       	<p><?php echo Kohana::lang('upgrade.upgrade_text_4');?>.</p>
        
        <p><input type="submit" id="upgrade" name="submit" value="<?php echo Kohana::lang('upgrade.upgrade_continue_btn_text');?>" class="login_btn" /></p>                
        <?php print form::close();?>
                              
      </p>
	</div>
</div>
</div>
