<?php require APPPATH.'views/admin/utils_js.php' ?>
function messagesAction ( action, confirmAction, message_id )
{
	var statusMessage;
	if( !isChecked( "message" ) && message_id=='' )
	{ 
		alert('Please select at least one message.');
	} else {
		var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
		if (answer){

			// Set Submit Type
			$("#action").attr("value", action);

			if (message_id != '') 
			{
				// Submit Form For Single Item
				$("#message_single").attr("value", message_id);
				$("#messageMain").submit();
			}
			else
			{
				// Set Hidden form item to 000 so that it doesn't return server side error for blank value
				$("#message_single").attr("value", "000");

				// Submit Form For Multiple Items
				$("#messageMain").submit();
			}

		} else {
		//	return false;
		}
	}
}

// Preview Message
function preview ( id ){
	if (id) {
		$('#' + id).toggle(400);
	}
}