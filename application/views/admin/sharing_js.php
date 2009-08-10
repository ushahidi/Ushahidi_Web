// Sharing JS
function fillFields(id, sharing_url, sharing_color, sharing_limits, sharing_type)
{
	$("#sharing_id_action").attr("value", unescape(id));
	$("#sharing_url").attr("value", unescape(sharing_url));
		// Disable This Field
		$("#sharing_url").attr("readonly",true);
		$("#sharing_url").css("background-color", "#ccc");
	$("#sharing_color").attr("value", unescape(sharing_color));
	$("#sharing_limits").attr("value", unescape(sharing_limits));
	$("#sharing_type").attr("value", unescape(sharing_type));
}

// Ajax Submission
function sharingAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction)
	if (answer){
		// Set Category ID
		$("#sharing_id_action").attr("value", id);
		// Set Submit Type
		$("#sharing_action").attr("value", action);		
		// Submit Form
		$("#sharingMain").submit();
	}
}

// Prevent multiple form submission
$(document).ready(function() {
	$('#sharingMain').submit(function() {
		$('#sharing_loading').html('&nbsp;<br /><img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
		if(typeof jQuery.data(this, "disabledOnSubmit") == 'undefined') {
			jQuery.data(this, "disabledOnSubmit", { submited: true });
			$('input[type=submit], input[type=button]', this).each(function() {
				$(this).attr("disabled", "disabled");
				});
				return true;
		}
			else
		{
			return false;
		}
	});
});