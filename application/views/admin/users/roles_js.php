// Populates the fields in the add/edit form
function fillFields(id, name, description, access_level, permissions) {

	show_addedit();
	$("#role_id").attr("value", decodeURIComponent(id));
	$("#name").attr("value", decodeURIComponent(name));
	$("#description").attr("value", decodeURIComponent(description));
	$("#access_level").attr("value", decodeURIComponent(access_level));

	for (i = 0; i < permissions.length; i++)
	{
		$("#permission_"+permissions[i]).attr("checked", "checked")
	}
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
