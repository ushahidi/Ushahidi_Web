function fillFields(id, scheduler_name, scheduler_weekday, 
	scheduler_day, scheduler_hour, scheduler_minute)
{
	$('#add_edit_form').show();
	$("#scheduler_id").val(decodeURIComponent(id));
	$("#scheduler_name").val(decodeURIComponent(scheduler_name));
	$("#scheduler_weekday").val(decodeURIComponent(scheduler_weekday));
	$("#scheduler_day").val(decodeURIComponent(scheduler_day));
	$("#scheduler_hour").val(decodeURIComponent(scheduler_hour));
	$("#scheduler_minute").val(decodeURIComponent(scheduler_minute));
}


// Ajax Submission
function schedulerAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#scheduler_id_action").val(id);
		// Set Submit Type
		$("#action").val(action);
		// Submit Form
		$("#schedulerListing").submit();
	}
}
