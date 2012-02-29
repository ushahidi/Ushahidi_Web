function fillFields(id, name, description, access_level, reports_view, reports_edit, reports_evaluation, reports_comments, reports_download, reports_upload, messages, messages_reporters, stats, settings, manage, users)
{
	show_addedit();
	$("#role_id").attr("value", decodeURIComponent(id));
	$("#name").attr("value", decodeURIComponent(name));
	$("#description").attr("value", decodeURIComponent(description));
	$("#access_level").attr("value", decodeURIComponent(access_level));

	$("#reports_view").attr("checked", B(decodeURIComponent(reports_view)));
	$("#reports_edit").attr("checked", B(decodeURIComponent(reports_edit)));
	$("#reports_evaluation").attr("checked", B(decodeURIComponent(reports_evaluation)));
	$("#reports_comments").attr("checked", B(decodeURIComponent(reports_comments)));
	$("#reports_download").attr("checked", B(decodeURIComponent(reports_download)));
	$("#reports_upload").attr("checked", B(decodeURIComponent(reports_upload)));
	$("#messages").attr("checked", B(decodeURIComponent(messages)));
	$("#messages_reporters").attr("checked", B(decodeURIComponent(messages_reporters)));
	$("#stats").attr("checked", B(decodeURIComponent(stats)));
	$("#settings").attr("checked", B(decodeURIComponent(settings)));
	$("#manage").attr("checked", B(decodeURIComponent(manage)));
	$("#users").attr("checked", B(decodeURIComponent(users)));
}

// Ajax Submission
function rolesAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction)
	if (answer){
		// Set Role ID
		$("#role_id_main").attr("value", id);
		// Set Submit Type
		$("#role_action_main").attr("value", action);		
		// Submit Form
		$("#roleListing").submit();
	}
}

function B( objAny ){
	// Test argument for true / false,
	if (objAny == 1) {
		return(true);
	} else {
		return(false);
	}
}
