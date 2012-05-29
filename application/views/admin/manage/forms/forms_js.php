/**
 * Forms js file.
 *
 * Handles javascript stuff related to forms function.
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
	form_id = '<?php echo $form_id; ?>';
	if (form_id) {
		showForm('formDiv_' + form_id);
		$('#tr_' + form_id).effect("highlight", {}, 3000);
	};
});

function fillFields(id, form_title, form_description,
 form_visible )
{
	show_addedit();
	$("#form_id").attr("value", decodeURIComponent(id));
	$("#form_title").attr("value", decodeURIComponent(form_title));
	$("#form_description").attr("value", decodeURIComponent(form_description));
	$("#form_active").attr("value", decodeURIComponent(form_active));
	
}

// Form Submission
function formAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?');
		
	if (answer){
		// Set Submit Type
		$("#action_" + id).attr("value", action);		
		
		// Submit Form
		$("#form_action_" + id).submit();			
	
	} 
}

// Show Function
function showForm(id)
{
	if (id) {
		$('#' + id).toggle(400);
	}
}

// Toggle Add New Field
function addNewForm(id)
{
	if (id) {
		showForm('formadd_' + id);
		showFormSelected('1',id,'',false);
	}
}

// Ajax Call to Display 'Add New Field Form'
function showFormSelected(id, form_id, field_id, select_disable)
{
	if (id) {
		$('#form_result_' + form_id).html('');
		$('#form_result_' + form_id).hide();
		$('#form_fields_' + form_id).hide(300);
		$('#form_field_' + form_id +' [name=field_type]').val(id);
		$.post("<?php echo url::site() . 'admin/manage/forms/selector' ?>", { selector_id: id, form_id: form_id, field_id: field_id },
			function(data){
				if (data.status == 'success'){
					$('#form_fields_' + form_id).html('');
					$('#form_fields_' + form_id).show(300);
					$('#form_fields_' + form_id).html(data.message);
					$('#form_field_' + form_id +' [name=field_name]').focus();
				}
		  	}, "json");
	};	
}

// Modify Individual Form Fields
function fieldAction( action, confirmAction, field_id, form_id, field_type )
{
	$('#form_fields_current_' + form_id).css({
	"background-image" : "url('<?php echo url::file_loc('img')."media/img/loading_g2.gif"; ?>')",
	"background-position" : "center center",
	"background-repeat" : "no-repeat"
	});
	
	switch(action)
	{
	case 'e':
		$('#formadd_' + form_id).show(400);
		showFormSelected(field_type, form_id, field_id, true);
		$('#form_fields_current_' + form_id).css({
		"background-image" : "none"
		});
		break;
	case 'd':
		var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?');
		if (answer){
			$.post("<?php echo url::site() . 'admin/manage/forms/field_delete' ?>", { form_id: form_id, field_id: field_id },
				function(data){
					if (data.status == 'success'){
						$('#form_fields_current_' + form_id).html('');
						$('#form_fields_current_' + form_id).html(decodeURIComponent(data.response));
						//$('#form_fields_current_' + form_id).effect("highlight", {}, 2000);
						$('#form_fields_current_' + form_id).css({
						"background-image" : "none"
						});
					}
			  	}, "json");
		}
		break;
	case 'mu':
		$.post("<?php echo url::site() . 'admin/manage/forms/field_move' ?>", { form_id: form_id, field_id: field_id, field_position: 'u' },
			function(data){
				if (data.status == 'success'){
					$('#form_fields_current_' + form_id).html('');
					$('#form_fields_current_' + form_id).html(decodeURIComponent(data.response));
					//$('#form_fields_current_' + form_id).effect("highlight", {}, 2000);
					$('#form_fields_current_' + form_id).css({
					"background-image" : "none"
					});
				}
		  	}, "json");
		break;
	case 'md':
		$.post("<?php echo url::site() . 'admin/manage/forms/field_move' ?>", { form_id: form_id, field_id: field_id, field_position: 'd' },
			function(data){
				if (data.status == 'success'){
					$('#form_fields_current_' + form_id).html('');
					$('#form_fields_current_' + form_id).html(decodeURIComponent(data.response));
					//$('#form_fields_current_' + form_id).effect("highlight", {}, 2000);
					$('#form_fields_current_' + form_id).css({
					"background-image" : "none"
					});
				}
		  	}, "json");
		break;
	}
}
