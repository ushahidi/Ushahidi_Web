<?php 
/**
 * Reports translate view page.
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
				<h2><?php print $title; ?> <span></span><a href="<?php print url::base() ?>admin/reports">View Reports</a></h2>
				<?php print form::open(NULL, array('id' => 'reportForm', 'name' => 'reportForm')); ?>
					<input type="hidden" name="save" id="save" value="">
					<!-- report-form -->
					<div class="report-form">
						<?php
						if ($form_error) {
						?>
							<!-- red-box -->
							<div class="red-box">
								<h3>Error!</h3>
								<ul>
								<?php
								foreach ($errors as $error_item => $error_description)
								{
									// print "<li>" . $error_description . "</li>";
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
								<h3>Your Report Translation Has Been Saved!</h3>
							</div>
						<?php
						}
						?>
						<div class="head">
							<input type="image" src="<?php print url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
							<input type="image" src="<?php print url::base() ?>media/img/admin/btn-save-report.gif" class="save-rep-btn" />
						</div>
						<!-- f-col -->
						<div class="f-col-full">
							<div class="row">
								<div class="translation">
									<h4>Translate To?</h4>
									<span class="sel-holder nofloat">
									<?php print form::dropdown('locale', $locale_array, $form['locale']) ?>
									</span>
								</div>						
							</div>
							<div class="row">
								<h4>Original Title</h4>
								<?php print form::input('orig_title', $orig_title, ' readonly="readonly" class="text title"'); ?>
								<div class="translation">
									<h4>Translated Title</h4>
									<?php print form::input('incident_title', $form['incident_title'], ' class="text title nofloat"'); ?>
									<div style="clear:both;"></div>
								</div>
							</div>
							<div class="row">
								<h4>Original Description <span>Please include as much detail as possible.</span></h4>
								<?php print form::textarea('orig_description', $orig_description, ' readonly="readonly" rows="12" cols="40" ') ?>
								<div class="translation">
									<h4>Translated Description</h4>
									<?php print form::textarea('incident_description', $form['incident_description'], ' rows="12" cols="40" class="nofloat"') ?>
									<div style="clear:both;"></div>
								</div>
							</div>
						</div>
						
						<input id="save_only" type="image" src="<?php print url::base() ?>media/img/admin/btn-save-report.gif" class="save-rep-btn" />
						<input id="save_close" type="image" src="<?php print url::base() ?>media/img/admin/btn-save-and-close.gif" class="save-close-btn" />
						<input id="cancel" type="image" src="<?php print url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
					</div>
				<?php print form::close(); ?>
			</div>
