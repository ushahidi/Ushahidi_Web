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
					<a href="<?php echo url::base() . 'admin/addons' . '">' . Kohana::lang('ui_main.plugins') . '</a>' ?>
					<a href="<?php echo url::base() . 'admin/addons/themes' . '" class="active">' . Kohana::lang('ui_main.themes') . '</a>' ?>
				</h2>
				<?php print form::open(); ?>
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
							<h3><?php echo Kohana::lang('ui_main.configuration_saved');?></h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3><?php echo Kohana::lang('ui_main.theme_settings');?></h3>
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->
					<div class="sms_holder">
						<!-- Default Theme -->
						<div class="theme_holder">
							<div class="theme_screenshot"><?php
								echo "<img src=\"".url::file_loc('img')."media/img/default_theme.png\" width=240 height=150 border=0>";
							?></div>
							<strong><?php echo Kohana::lang('ui_main.theme_default');?></strong><BR />
							<?php echo Kohana::lang('ui_main.theme_default');?>.<BR />
							<strong><u><?php echo Kohana::lang('ui_main.version');?></u></strong>: 1.0<BR />
							<strong><u><?php echo Kohana::lang('ui_main.demo');?></u></strong>: http://www.ushahidi.com<BR />
							<strong><u><?php echo Kohana::lang('ui_main.contact');?></u></strong>: team@ushahidi.com<BR />
							<strong><u><?php echo Kohana::lang('ui_main.location');?></u></strong>: 
							<div class="theme_select">
								<input type="radio" name="site_style" value="" <?php
								if ($form['site_style'] == "")
								{
									echo "checked = \"checked\"";
								}
								?> /><?php echo Kohana::lang('ui_main.select_theme');?>
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
								<strong><u><?php echo Kohana::lang('ui_main.version');?></u></strong>: <?php echo $theme['Version'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.demo');?></u></strong>: <?php echo $theme['Demo'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.contact');?></u></strong>: <?php echo $theme['Author_Email'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.location');?></u></strong>: <i>/themes/<?php echo $theme['Template_Dir'] ?>/</i>
								<div class="theme_select">
									<input type="radio" name="site_style" value="<?php echo $theme['Template_Dir'] ?>" <?php
									if ($theme['Template_Dir'] == $form['site_style'])
									{
										echo "checked = \"checked\"";
									}
									?> /><?php echo Kohana::lang('ui_main.select_theme');?>
								</div>												
							</div>
							<?php
						}						
						?>
						<div style="clear:both;"></div>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
