function fillFields(id, name, description, access_level, reports_view, reports_edit, reports_evaluation, reports_comments, reports_download, reports_upload, messages, messages_reporters, stats, settings, manage, users)
{
	show_addedit();
	$("#role_id").attr("value", unescape(id));
	$("#name").attr("value", unescape(name));
	$("#description").attr("value", unescape(description));
	$("#access_level").attr("value", unescape(access_level));

	$("#reports_view").attr("checked", B(unescape(reports_view)));
	$("#reports_edit").attr("checked", B(unescape(reports_edit)));
	$("#reports_evaluation").attr("checked", B(unescape(reports_evaluation)));
	$("#reports_comments").attr("checked", B(unescape(reports_comments)));
	$("#reports_download").attr("checked", B(unescape(reports_download)));
	$("#reports_upload").attr("checked", B(unescape(reports_upload)));
	$("#messages").attr("checked", B(unescape(messages)));
	$("#messages_reporters").attr("checked", B(unescape(messages_reporters)));
	$("#stats").attr("checked", B(unescape(stats)));
	$("#settings").attr("checked", B(unescape(settings)));
	$("#manage").attr("checked", B(unescape(manage)));
	$("#users").attr("checked", B(unescape(users)));
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
