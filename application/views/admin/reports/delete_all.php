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
		<?php admin::reports_subtabs("delete-all"); ?>
	</h2>

	<!-- report-form -->
	<div class="report-form">
		<!-- column -->
		<div class="deleteall_container">
			<h1><?php echo Kohana::lang('ui_admin.delete_all')?></h1>

			<?php if($report_count > 0): ?>
				<?php print form::open(NULL, array('id' => 'reportForm', 'name' => 'reportForm')); ?>

				<p><?php echo Kohana::lang('ui_admin.delete_all_instructions')?></p>
				<p><?php echo Kohana::lang('ui_admin.delete_all_backup')?></p>

				<?php print form::hidden('confirm_delete_all','1'); ?>
				<input type="submit" value="<?php echo Kohana::lang('ui_admin.delete_all_button', $report_count)?>" />
				<?php print form::close(); ?>
			<?php else: ?>
				<p><?php echo Kohana::lang('ui_admin.delete_all_no_reports')?></p>
			<?php endif; ?>
		</div>
	</div>
</div>
