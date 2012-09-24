<?php 
/**
 * Reports download view page.
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
	<h2>
		<?php admin::reports_subtabs("download"); ?>
	</h2>
	<!-- report-form -->
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
					// print "<li>" . $error_description . "</li>";
					print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
				}
				?>
				</ul>
			</div>
		<?php
		}
		?>
		<!-- column -->
		<div class="download_container">
			<p><?php echo Kohana::lang('ui_main.reports_download_csv');?>.</p>
			<span style="font-weight: bold; color: #00699b; display: block; padding-bottom: 5px;"><?php echo Kohana::lang('ui_main.choose_data_points');?>:</span>
			<?php print form::open(NULL, array('id' => 'reportForm', 'name' => 'reportForm')); ?>
			<table class="data_points">
				<tr>
					<td colspan="2">
						<input type="checkbox" id="data_all" name="data_all" onclick="CheckAll(this.id)" /><strong><?php echo utf8::strtoupper(Kohana::lang('ui_main.select_all'));?></strong>
						<div id="form_error1"></div>
					</td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_active[]', '1', FALSE); ?><?php echo Kohana::lang('ui_main.approved_reports');?></td>
					<td><?php print form::checkbox('data_include[]', '1', FALSE); ?><?php echo Kohana::lang('ui_main.include_location_information');?></td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_verified[]', '1', FALSE); ?><?php echo Kohana::lang('ui_main.verified_reports');?></td>
					<td><?php print form::checkbox('data_include[]', '2', FALSE); ?><?php echo Kohana::lang('ui_main.include_description');?></td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_active[]', '0', FALSE); ?><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.awaiting_approval');?></td>
					<td><?php print form::checkbox('data_include[]', '3', FALSE); ?><?php echo Kohana::lang('ui_main.include_categories');?></td>
				</tr>
                                <tr>
                                        <td><?php print form::checkbox('data_verified[]', '0', FALSE); ?><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.awaiting_verification');?></td>
                                        <td><?php print form::checkbox('data_include[]','4',FALSE); ?><?php echo Kohana::lang('ui_main.include_latitude');?></td>
                                </tr>
                                <tr>
                                        <td><?php print form::checkbox('data_include[]','5',FALSE); ?><?php echo Kohana::lang('ui_main.include_longitude');?></td>
										<td></td>
                                </tr>
				<!-- Including custom fields in the download process -->
				<tr>
					<td colspan="2"> 
						<div  style="font-weight: bold; color:#00699b; display: block;padding-bottom: 5px;"><?php echo Kohana::lang('ui_main.additional_data');?><div>
					</td>
				</tr>

				 <tr>
                    <td><?php print form::checkbox('data_include[]','6',FALSE); ?><?php echo Kohana::lang('ui_main.include_custom_fields');?></td>
					<td><?php print form::checkbox('data_include[]','7',FALSE); ?><?php echo Kohana::lang('ui_main.include_personal_info');?></td>
                </tr>


				<tr>
					<td colspan="2">
						<div class="date-box">
							<h4><?php echo Kohana::lang('ui_admin.from_date');?>: <span><?php echo Kohana::lang('ui_main.date_format');?></span></h4>
							<?php print form::input('from_date', $form['from_date'], ' class="text"'); ?>											    
						</div>
						<div class="date-box">
							<h4><?php echo Kohana::lang('ui_admin.to_date');?>: <span><?php echo Kohana::lang('ui_main.date_format');?></span></h4>
							<?php print form::input('to_date', $form['to_date'], ' class="text"'); ?>											    
						</div>
						<div id="form_error2"></div>
					</td>
				</tr>
			</table>
			<input id="save_only" type="submit" value="<?php echo utf8::strtoupper(Kohana::lang('ui_main.download'));?>" class="save-rep-btn" />
			<?php print form::close(); ?>
		</div>
	</div>
</div>
