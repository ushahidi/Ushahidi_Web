<?php 
/**
 * Themes view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Themes View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2><?php echo $title; ?> 
					<a href="<?php echo url::base() . 'admin/settings/site' ?>">Site</a>
					<a href="<?php echo url::base() . 'admin/settings' ?>">Map</a>
					<a href="<?php echo url::base() . 'admin/settings/sms' ?>">SMS</a>
					<a href="<?php echo url::base() . 'admin/settings/sharing' ?>">Sharing</a>
					<a href="<?php echo url::base() . 'admin/settings/email' ?>">Email</a>
					<a href="<?php echo url::base() . 'admin/settings/themes' ?>" class="active">Themes</a>
				</h2>
				<?php print form::open(); ?>
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
							<h3>Your Settings Have Been Saved!</h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3>Theme Settings</h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->
					<div class="sms_holder">
						<!-- Default Theme -->
						<div class="theme_holder">
							<div class="theme_screenshot"><?php
								echo "<img src=\"".url::base()."media/img/default_theme.png\" width=240 height=150 border=0>";
							?></div>
							<strong>Default Ushahidi Theme</strong><BR />
							The default Ushahidi Theme.<BR />
							<strong><u>Version</u></strong>: 1.0<BR />
							<strong><u>Demo</u></strong>: http://www.ushahidi.com<BR />
							<strong><u>Contact</u></strong>: team@ushahidi.com<BR />
							<strong><u>Location</u></strong>: 
							<div class="theme_select">
								<input type="radio" name="site_style" value="" <?php
								if ($form['site_style'] == "")
								{
									echo "checked = \"checked\"";
								}
								?> />Select Theme
							</div>												
						</div>
						<!-- / Default Theme -->				
						<?php
						foreach ($themes as $theme)
						{
							?>
							<div class="theme_holder">
								<div class="theme_screenshot"><?php
									if (!empty($theme['Screenshot']))
									{
										echo "<img src=\"".url::base()."themes/".$theme['Template_Dir']."/".
										$theme['Screenshot']."\" width=240 height=150 border=0>";
									}
								?></div>
								<strong><?php echo $theme['Title']." by ".$theme['Author']; ?></strong><BR />
								<?php echo $theme['Description'] ?><BR />
								<strong><u>Version</u></strong>: <?php echo $theme['Version'] ?><BR />
								<strong><u>Demo</u></strong>: <?php echo $theme['Demo'] ?><BR />
								<strong><u>Contact</u></strong>: <?php echo $theme['Author_Email'] ?><BR />
								<strong><u>Location</u></strong>: <i>/themes/<?php echo $theme['Template_Dir'] ?>/</i>
								<div class="theme_select">
									<input type="radio" name="site_style" value="<?php echo $theme['Template_Dir'] ?>" <?php
									if ($theme['Template_Dir'] == $form['site_style'])
									{
										echo "checked = \"checked\"";
									}
									?> />Select Theme
								</div>												
							</div>
							<?php
						}						
						?>
						<div style="clear:both;"></div>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
