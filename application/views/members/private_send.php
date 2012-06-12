<?php 
/**
 * New Private Message
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Private Message New
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php members::private_subtabs("new"); ?>
				</h2>
				<?php print form::open(); ?>
				<input type="hidden" name="parent_id" value="<?php echo $form['parent_id']; ?>">
				<div class="report-form">
					<?php
					if ($form_error) {
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3><?php echo Kohana::lang('ui_main.error');?></h3>
							<ul>
							<?php
							foreach ($errors as $error_item => $error_description)
							{
								print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
							}
							?>
							</ul>
						</div>
					<?php
					}

					if ($form_saved) {
					?>
						<!-- green-box -->
						<div class="green-box">
							<h3><?php echo Kohana::lang('ui_admin.private_sent');?></h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.send');?>" />
					</div>
					<!-- column -->		
					<div class="sms_holder">
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.private_to"); ?>"><?php echo Kohana::lang('ui_admin.private_to');?></a></h4>
							<?php print form::input('private_to', $form['private_to'], ' class="text long2" '); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.private_subject"); ?>"><?php echo Kohana::lang('ui_admin.private_subject');?></a></h4>
							<?php print form::input('private_subject', $form['private_subject'], ' class="text long2" '); ?>
						</div>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.private_message"); ?>"><?php echo Kohana::lang('ui_admin.private_message');?></a></h4>
							<?php print form::textarea('private_message', $form['private_message'], ' rows="6" cols="40" class="textarea long" '); ?>
						</div>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.send');?>" />
				</div>
				<?php print form::close(); ?>
			</div>
