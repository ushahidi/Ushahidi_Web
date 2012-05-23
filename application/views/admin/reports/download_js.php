/**
 * Download reports js file.
 *
 * Handles javascript stuff related to download reports function.
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

		$(document).ready(function() {
			$("#from_date").datepicker({ 
				showOn: "both", 
				buttonImage: "<?php echo $calendar_img; ?>", 
				buttonImageOnly: true 
			});
			
			$("#to_date").datepicker({ 
				showOn: "both", 
				buttonImage: "<?php echo $calendar_img; ?>", 
				buttonImageOnly: true 
			});
			
			
			$("#reportForm").validate({
				rules: {
					"data_active[]": {
						required: true,
						range: [0,1]
					},
					"data_verified[]": {
						required: true,
						range: [0,1]
					},
					"data_include[]": {
						range: [1,5]
					},
					from_date: {
						date: true
					},
					to_date: {
						date: true
					}
				},
				messages: {
					"data_verified[]": {
						required: "<?php echo addslashes(Kohana::lang('report.data_verified.required'));?>",
						range: "<?php echo addslashes(Kohana::lang('report.data_verified.between'));?>"
					},
					"data_active[]": {
						required: "<?php echo addslashes(Kohana::lang('report.data_active.required'));?>",
						range: "<?php echo addslashes(Kohana::lang('report.data_active.between'));?>"
					},
					"data_include[]": {
						range: "<?php echo addslashes(Kohana::lang('report.data_include.between'));?>"
					},
					from_date: {
						date: "<?php echo addslashes(Kohana::lang('report.from_date.date_mmddyyyy'));?>"
					},
					to_date: {
						date: "<?php echo addslashes(Kohana::lang('report.to_date.date_mmddyyyy'));?>"
					}
				},
				errorPlacement: function(error, element) {
					if (element.attr("name") == "data_point" || element.attr("name") == "data_include")
					{
						error.appendTo("#form_error1");
					}else if (element.attr("name") == "from_date" || element.attr("name") == "to_date"){
						error.appendTo("#form_error2");
					}else{
						error.insertAfter(element);
					}
				}
			});			
		});
		
		// Check All / Check None
		function CheckAll( id )
		{
			//$("INPUT[name='data_point'][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
			//$("INPUT[name='data_include'][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
			$("td > input:checkbox").attr('checked', $('#' + id).is(':checked'));
		}
