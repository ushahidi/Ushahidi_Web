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
	<h2><?php print $title; ?> <span></span><a href="<?php print url::base() ?>admin/reports/upload">Upload Reports</a><a href="<?php print url::base() ?>admin/reports">View Reports</a><a href="<?php print url::base() ?>admin/reports/edit">Create New Report</a></h2>
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
		?>
		<!-- column -->
		<div class="download_container">
			<p>Reports will be downloaded in CSV format.</p>
			<span style="font-weight: bold; color: #00699b; display: block; padding-bottom: 5px;">Choose data points to download:</span>
			<?php print form::open(NULL, array('id' => 'reportForm', 'name' => 'reportForm')); ?>
			<table class="data_points">
				<tr>
					<td colspan="2">
						<input type="checkbox" id="data_all" name="data_all" checked="checked" onclick="CheckAll(this.id)" /><strong>SELECT ALL</strong>
						<div id="form_error1"></div>
					</td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_point[]', '1', TRUE); ?>Approved Reports</td>
					<td><?php print form::checkbox('data_include[]', '1', TRUE); ?>Include Location Information</td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_point[]', '2', TRUE); ?>Verified Reports</td>
					<td><?php print form::checkbox('data_include[]', '2', TRUE); ?>Include Description</td>
				</tr>
				<tr>
					<td><?php print form::checkbox('data_point[]', '3', TRUE); ?>Reports Awaiting Approval</td>
					<td><?php print form::checkbox('data_include[]', '3', TRUE); ?>Include Categories</td>
				</tr>
                                <tr>
                                        <td><?php print form::checkbox('data_point[]', '4', TRUE); ?>Reports Awaiting Verification</td>
                                        <td><?php print form::checkbox('data_include[]','4',TRUE); ?>Include Latitude</td>
                                </tr>
                                <tr>
                                        <td><?php print form::checkbox('data_include[]','5',TRUE); ?>Include Longitude</td>
                                </tr>
				<tr>
					<td colspan="2">
						<div class="date-box">
							<h4>From: <span>(mm/dd/yyyy)</span></h4>
							<?php print form::input('from_date', $form['from_date'], ' class="text"'); ?>											    
						</div>
						<div class="date-box">
							<h4>To: <span>(mm/dd/yyyy)</span></h4>
							<?php print form::input('to_date', $form['to_date'], ' class="text"'); ?>											    
						</div>
						<div id="form_error2"></div>
					</td>
				</tr>
			</table>
			<input id="save_only" type="image" src="<?php print url::base() ?>media/img/admin/btn-download.gif" class="save-rep-btn" />
			<?php print form::close(); ?>
		</div>
	</div>
</div>
