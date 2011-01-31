function fillFields(id, scheduler_name, scheduler_weekday, 
	scheduler_day, scheduler_hour, scheduler_minute)
{
	$('#add_edit_form').show();
	$("#scheduler_id").attr("value", unescape(id));
	$("#scheduler_name").attr("value", unescape(scheduler_name));
	$("#scheduler_weekday").attr("value", unescape(scheduler_weekday));
	$("#scheduler_day").attr("value", unescape(scheduler_day));
	$("#scheduler_hour").attr("value", unescape(scheduler_hour));
	$("#scheduler_minute").attr("value", unescape(scheduler_minute));
}


// Ajax Submission
function schedulerAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#scheduler_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#schedulerListing").submit();
	}
}