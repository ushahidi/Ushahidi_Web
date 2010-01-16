<?php 
/**
 * Messages view page.
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
	<div class="table-holder">
   		<p>
    	<?php print form::open(NULL, array('id' => 'upgradeMain', 'name' => 'upgradeMain')); ?>
      	<p><?php echo Kohana::lang('upgrade.upgrade_text_1');?>.<p>

       	<p><?php echo Kohana::lang('upgrade.upgrade_text_2');?>.</p>

       	<p><?php echo Kohana::lang('upgrade.upgrade_text_3');?>:<p>
       	<ul>
       		<li>
            	/applications/config
            </li>
           	<li>
            	/applications/cache
          	</li>
          	<li>
            	/applications/logs
          	</li>
          	<li>
             	/media/uploads
          	</li>
        </ul>
       	<p><?php echo Kohana::lang('upgrade.upgrade_text_4');?>.</p>
        
        <p><?php echo Kohana::lang('upgrade.upgrade_text_5');?>.</p>
        <p><input type="submit" id="upgrade" name="submit" value="Automatic Upgrade" class="login_btn" /></p>                
        <?php print form::close();?>                           
      </p>
	</div>
</div>
