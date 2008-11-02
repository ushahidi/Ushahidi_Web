/*
 * Organizations Javascript
 */
// Organizations JS
function fillFields(id, organization_name, organization_website,
 organization_description )
{
	$("#organization_id").attr("value", unescape(id));
	$("#organization_name").attr("value", unescape(organization_name));
	$("#organization_website").attr("value", unescape(organization_website));
	$("#organization_description").attr("value", 
		unescape(organization_description));
}

// Ajax Submission
function orgAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Category ID
		$("#org_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#orgListing").submit();			
	
	} else{
		return false;
	}
}
