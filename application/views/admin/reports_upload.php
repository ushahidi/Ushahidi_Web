<?php 
/**
 * Reports upload view page.
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
	<h2><?php print $title; ?> <span></span><a href="<?php print url::base() ?>admin/reports/download">Download Reports</a><a href="<?php print url::base() ?>admin/reports">View Reports</a><a href="<?php print url::base() ?>admin/reports/edit">Create New Report</a></h2>
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
					print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
				}
				?>
				</ul>
			</div>
		<?php
		}
		?>
		<!-- column -->
		<div class="upload_container">
		<p>With the form below, you can import incidents into the Ushahidi engine.</p>
		<h3>Please note</h3>
		<ul>
			<li>Reports must be upladed in CSV format.</li>
			<li>When incident ID already exists in the database, the entry in the CSV file will be ignored.</li>
			<li>It must contain at least Incident Title and Incident Date</li>
		</ul>
			<p>
				A Sample CSV Report
				<p>
					#,INCIDENT TITLE,INCIDENT DATE,LOCATION,DESCRIPTION,CATEGORY,APPROVED,VERIFIED<br />
					"1","Suspected death in Nairobi","2009-05-15 01:06:00","Nairobi","Three cases have been confirmed in C. del Uruguay","DEATHS, CIVILIANS, ",YES,YES<br />
					"2","Looting","2009-03-18 10:10:00","Accra","Looting happening everywhere","RIOTS, DEATHS, PROPERTY LOSS, ",YES,NO<br />
					
				</p>
			</p>
			<?php print form::open(NULL, array('id' => 'uploadForm', 'name' => 'uploadForm', 'enctype' => 'multipart/form-data')); ?>
            <p><b>File to upload</b> <?php echo form::upload(array('name' => 'csvfile'), 'path/to/local/file'); ?></p>
			<button type="submit">Upload</button>
			<?php print form::close(); ?>
		</div>
	</div>
</div>
