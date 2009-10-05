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
		<!-- tabs -->
		<div class="tabs">
		<?php
		    if ($form_error) {
		?>
		    <!-- red-box -->
		    <div class="red-box">
		        <h3>Error!</h3>
			    <ul>Please verify that you have checked an item.</ul>
		    </div>
		    <?php
		}

	        if ($form_saved) {
		?>
		    <!-- green-box -->
		    <div class="green-box" id="submitStatus">
		            <h3>Messages <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
		    </div>
		    <?php
	        }
		?>
            <div class="table-holder">
                    <p>
                            <?php print form::open(NULL, array('id' => 'upgradeMain', 'name' => 'upgradeMain')); ?>
                            
                            <p>The instructions below detail how to update your Ushahidi instance.<p>

                            <p>Step 1: Download the most recent ushahidi build from <a href="http://download.ushahidi.com/">http://download.ushahidi.com</a>.</p>

                            <p>Step 2: Depending on the operating system running the webserver, use your preferred tool/mode (i.e: telnet, ftp, ssh) to login to the webserver and replace the contents of all the folders with the newest from the recent build. All EXCEPT:<p>
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
                            <p>Step 3: Click on the button below to update the necessary tables.</p>
                            <a href="#" class="btn_save">Upgrade</a>                           
                    </p>
            </div>
        </div>
</div>
