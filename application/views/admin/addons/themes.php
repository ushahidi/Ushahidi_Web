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
					<a href="<?php echo url::site() . 'admin/addons' . '">' . Kohana::lang('ui_main.plugins') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/addons/themes' . '" class="active">' . Kohana::lang('ui_main.themes') . '</a>' ?>
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
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
					</div>
					<!-- column -->
					<div class="sms_holder">
						<?php
						$i = 1; // Start at 2 because the default theme isn't in this array
						foreach ($themes as $theme_key => $theme)
						{
							?>
							<div class="theme_holder">
								<div class="theme_screenshot"><?php
									if (!empty($theme['Screenshot']))
									{
										echo "<img src=\"".url::site("themes/".$theme_key."/".$theme['Screenshot'])."\" width=240 height=150 border=0>";
									}
								?></div>
								<strong><?php echo $theme['Theme Name']." by ".$theme['Author']; ?></strong><BR />
								<?php echo $theme['Description'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.version');?></u></strong>: <?php echo $theme['Version'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.demo');?></u></strong>: <?php echo $theme['Demo'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.contact');?></u></strong>: <?php echo $theme['Author Email'] ?><BR />
								<strong><u><?php echo Kohana::lang('ui_main.location');?></u></strong>: <i>/themes/<?php echo $theme_key ?>/</i>
								<label class="theme_select" style="display: block;">
									<input type="radio" name="site_style" value="<?php echo $theme_key ?>" <?php
									if ($theme_key == $form['site_style'])
									{
										echo "checked = \"checked\"";
									}
									?> /><?php echo Kohana::lang('ui_main.select_theme');?>
									
								</label>
							</div>
							<?php
							// Make sure the themes don't get bunched up
							if($i % 3 == 0) {
								?><div style="clear:both;"></div><?php
							}
							$i++;
						}						
						?>
						<div style="clear:both;"></div>
						<p class="more_addons"><a href="http://community.ushahidi.com/themes"><?php echo Kohana::lang('ui_admin.get_more_themes'); ?></a></p>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
				</div>
				<?php print form::close(); ?>
			</div>
