<?php 
/**
 * Reports submit form.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Analysis Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<br>								
<div class="f-col-bottom-1">
	<h3><?php echo Kohana::lang('analysis.information_evaluation');?></h3>
	<div class="report_row">
		<h4><?php echo
		Kohana::lang('analysis.report_edit_dropdown_1_title');?>:</h4>
		<?php print form::dropdown('incident_source', 
		array(""=> Kohana::lang('analysis.report_edit_dropdown_1_default'), 
		"1"=> Kohana::lang('analysis.report_edit_dropdown_1_item_1'), 
		"2"=> Kohana::lang('analysis.report_edit_dropdown_1_item_2'), 
		"3"=> Kohana::lang('analysis.report_edit_dropdown_1_item_3'), 
		"4"=> Kohana::lang('analysis.report_edit_dropdown_1_item_4'), 
		"5"=> Kohana::lang('analysis.report_edit_dropdown_1_item_5'), 
		"6"=> Kohana::lang('analysis.report_edit_dropdown_1_item_6')
		)
		, 'incident_source') ?>									
	</div>
	<div class="report_row">
		<h4><?php echo Kohana::lang('analysis.report_edit_dropdown_2_title');?>:</h4>
		<?php print form::dropdown('incident_information', 
		array(""=> Kohana::lang('analysis.report_edit_dropdown_1_default'), 
		"1"=> Kohana::lang('analysis.report_edit_dropdown_2_item_1'), 
		"2"=> Kohana::lang('analysis.report_edit_dropdown_2_item_2'), 
		"3"=> Kohana::lang('analysis.report_edit_dropdown_2_item_3'), 
		"4"=> Kohana::lang('analysis.report_edit_dropdown_2_item_4'), 
		"5"=> Kohana::lang('analysis.report_edit_dropdown_2_item_5'), 
		"6"=> Kohana::lang('analysis.report_edit_dropdown_2_item_6')
		)
		, 'incident_information') ?>									
	</div>								
</div>
<br/>
