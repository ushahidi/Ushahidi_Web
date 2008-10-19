/*
 * Categories Javascript
 */
// Categories JS
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
function userAction ( action, id, organization_name, organization_website,
 organization_description, confirmAction )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	
	if (answer) {
		// Set Submit Type
		$("#action").attr( "value", action );
		// Set form values for deletion so it passes the validation test
		$("#organization_id").attr( "value",unescape( id ) );
		$("#organization_name").attr( "value", unescape(organization_name) );
		$("#organization_website").attr( "value",
		 	unescape( organization_website ));
		$("#organization_description").attr("value",
		 	unescape(organization_description));
		
		// Submit Form
		$("#orgMain").submit();			
	
	} else{
		return false;
	}
}
