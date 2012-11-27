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
			<?php print form::open(NULL, array('id' => 'reportForm', 'name' => 'reportForm')); ?>
			<p style="font-weight: bold; color:#00699b; display: block;padding-bottom: 5px;"><?php echo Kohana::lang('ui_admin.select_download_format'); ?></p>
			<div id="form_error_format"></div>
			<p>
				<span><?php print form::radio('format','csv', TRUE); ?><?php echo Kohana::lang('ui_admin.csv')?></span>
				<span><?php print form::radio('format','xml', FALSE); ?><?php echo Kohana::lang('ui_admin.xml') ?></span>
			</p>
			<span style="font-weight: bold; color: #00699b; display: block; padding-bottom: 5px;"><?php echo Kohana::lang('ui_main.choose_data_points');?>:</span>
			<table class="data_points">
				<tr>
					<td colspan="2">
						<input type="checkbox" id="data_all" name="data_all" onclick="CheckAll(this.id)"  checked="checked" /><strong><?php echo utf8::strtoupper(Kohana::lang('ui_main.select_all'));?></strong>
						<div id="form_error1"></div>
					</td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_active[]', '1', in_array(1, $form['data_active'])); ?><?php echo Kohana::lang('ui_main.approved_reports');?></td>
					<td><?php print form::checkbox('data_verified[]', '1', in_array(1, $form['data_verified'])); ?><?php echo Kohana::lang('ui_main.verified_reports');?></td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_active[]', '0', in_array(0, $form['data_active'])); ?><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.awaiting_approval');?></td>
					<td><?php print form::checkbox('data_verified[]', '0', in_array(0, $form['data_verified'])); ?><?php echo Kohana::lang('ui_main.reports');?> <?php echo Kohana::lang('ui_main.awaiting_verification');?></td>
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
			<span  style="font-weight: bold; color:#00699b; display: block;padding-bottom: 5px;"><?php echo Kohana::lang('ui_main.additional_data');?>:</span>
			<table class="data_points">
				<tr>
					<td colspan="2">
						<input type="checkbox" id="data_include_all" name="data_include_all" onclick="CheckAll(this.id)" checked="checked"/><strong><?php echo utf8::strtoupper(Kohana::lang('ui_main.select_all'));?></strong>
						<div id="form_error1"></div>
					</td>
				</tr>

				<tr>
					<td><?php print form::checkbox('data_include[]', '2', in_array(2, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_description');?></td>
					<td><?php print form::checkbox('data_include[]', '1', in_array(1, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_location_information');?></td>
				</tr>
				<tr>
				<td><?php print form::checkbox('data_include[]', '3', in_array(3, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_categories');?></td>
					<td><?php print form::checkbox('data_include[]','4',in_array(4, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_latitude');?></td>
				</tr>
				
				<tr>
					<td><?php print form::checkbox('data_include[]','6',in_array(6, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_custom_fields');?></td>
					<td><?php print form::checkbox('data_include[]','5',in_array(5, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_longitude');?></td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_include[]','7',in_array(7, $form['data_include'])); ?><?php echo Kohana::lang('ui_main.include_personal_info');?></td>
					<td></td>
				</tr>
			</table>
			<input id="save_only" type="submit" value="<?php echo utf8::strtoupper(Kohana::lang('ui_main.download'));?>" class="save-rep-btn" />
			<?php print form::close(); ?>
		</div>
	</div>
</div>
