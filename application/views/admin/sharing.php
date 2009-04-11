<?php 
/**
 * Sharing view page.
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
				<h2><?php echo $title; ?> <a href="<?php echo url::base() . 'admin/settings/site' ?>">Site</a><a href="<?php echo url::base() . 'admin/settings' ?>">Map</a><a href="<?php echo url::base() . 'admin/settings/sms' ?>">SMS</a><a href="<?php echo url::base() . 'admin/settings/sharing' ?>" class="active">Sharing</a></h2>
				<div class="report-form">
					<div class="head">
						<h3>SMS Setup Options</h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->
					<div class="final_container">
						<h4>Share Data With Other Organizations <sup><a href="#">?</a></sup></h4>
						<p>In order to share data with other organizations, you will need to give them your Ushahidi Data Sharing Key.</p>
						<div class="final_l">
							<span class="dark_red_span">My Ushahidi Data Sharing Key:</span>
							<p class="sync_key_2">8esA3k234sKp341m</p>
						</div>
						<div class="final_r">
							<span style="font-weight: bold; color: #00699b; display: block; padding-bottom: 5px;">Choose data points to share with other organizations:</span>
							<table class="data_points">
								<tr>
									<td><input type="checkbox" checked="checked" />Location</td>
									<td><input type="checkbox" checked="checked" />Title</td>
									<td><input type="checkbox" checked="checked" />Time</td>
									<td><input type="checkbox" />Media</td>
								</tr>
								<tr>
									<td><input type="checkbox" />Category</td>
									<td><input type="checkbox" />Report</td>
									<td><input type="checkbox" checked="checked" />Date</td>
									<td><input type="checkbox" checked="checked" />Personal Data</td>
								</tr>
							</table>
						</div>
					</div>
		
					<div class="final_container">
						<h4>Share Data With Ushahidi <sup><a href="#">?</a></sup></h4>
						<p style="width: 500px;">Sharing data with Ushahidi is important.  Please contribute to the cause and share your data with the world.  You may stop sharing your data at any time.</p>
						<div class="final_l">
							<span class="dark_red_span_2">My Ushahidi Data Sharing Key:</span>
								<table class="p_replace">
									<tr>
										<td>By selecting this checkmark, I agree to share my data with the Global Ushahidi Instance. <a href="#" style="font-size: 10px;">Terms and Conditions &raquo; </a>
										</td>
										<td align="center" class="special">
											<input type="checkbox" name="" />
										</td>
									</tr>
								</table>
				
						</div>
						<div class="final_r">
							<span style="font-weight: bold; color: #00699b; display: block; padding-bottom: 5px;">Choose data points to share with other organizations:</span>
							<table class="data_points">
								<tr>
									<td><input type="checkbox" checked="checked" />Location</td>
									<td><input type="checkbox" checked="checked" />Title</td>
									<td><input type="checkbox" checked="checked" />Time</td>
									<td><input type="checkbox" />Media</td>
								</tr>
								<tr>
									<td><input type="checkbox" />Category</td>
									<td><input type="checkbox" />Report</td>
									<td><input type="checkbox" checked="checked" />Date</td>
									<td><input type="checkbox" checked="checked" />Personal Data</td>
								</tr>
							</table>
						</div>
					</div>
		
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1" style="padding-right: 20px;">ACTIVE</th>
									<th class="col-2">ORGANIZATION DETAILS</th>
									<th class="col-32" style="width: 150px;">DATE ADDED</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="col-1"><input  type="checkbox" class="check-box"/></td>
									<td class="col-2">
										<span class="span_cl1">United Nations Relief Fund</span>
										<span class="span_cl2">Data Sharing Key</span><span class="span_cl3">123455678</span><br />
										<span class="span_cl4">Data points to display:</span>
							
										<table class="data_points">
											<tr>
												<td><input type="checkbox" checked="checked" />Location</td>
												<td><input type="checkbox" checked="checked" />Title</td>
												<td><input type="checkbox" checked="checked" />Time</td>
												<td><input type="checkbox" />Media</td>
											</tr>
											<tr>
												<td><input type="checkbox" />Category</td>
												<td><input type="checkbox" />Report</td>
												<td><input type="checkbox" checked="checked" />Date</td>
												<td><input type="checkbox" checked="checked" />Personal Data</td>
											</tr>
										</table>
										<a href="#" class="dark_red">Edit organization</a>
									</td>
									<td class="col-32">Added on August 22, 2008</td>
								</tr>
								<tr>
									<td class="col-1"><input  type="checkbox" class="check-box"/></td>
									<td class="col-2">
										<span class="span_cl1">Rainbow Coalition</span>
										<span class="span_cl2">Data Sharing Key</span><span class="span_cl3">123455678</span><br />
										<span class="span_cl4">Data points to display:</span>
							
										<table class="data_points">
											<tr>
												<td><input type="checkbox" checked="checked" />Location</td>
												<td><input type="checkbox" checked="checked" />Title</td>
												<td><input type="checkbox" checked="checked" />Time</td>
												<td><input type="checkbox" />Media</td>
											</tr>
											<tr>
												<td><input type="checkbox" />Category</td>
												<td><input type="checkbox" />Report</td>
												<td><input type="checkbox" checked="checked" />Date</td>
												<td><input type="checkbox" checked="checked" />Personal Data</td>
											</tr>
										</table>
										<a href="#" class="dark_red">Edit organization</a>
									</td>
									<td class="col-32">Added on August 22, 2008</td>
								</tr>
							</tbody>
						</table>						
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-cancel.gif" class="cancel-btn" />
				</div>
			</div>