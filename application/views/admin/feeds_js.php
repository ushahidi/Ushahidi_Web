/*
 * Categories Javascript
 */
// Categories JS
function fillFields(id, feed_name, feed_url,
 feed_visible )
{
	$("#feed_id").attr("value", unescape(id));
	$("#feed_name").attr("value", unescape(feed_name));
	$("#feed_url").attr("value", unescape(feed_url));
	$("#feed_visible").attr("value", unescape(feed_visible));
	
}

// Ajax Submission
function userAction ( action, id, feed_name, feed_url,feed_active, 
	confirmAction )
{
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	
	if (answer) {
		// Set Submit Type
		$("#action").attr( "value", action );
		// Set form values for deletion so it passes the validation test
		$("#feed_id").attr( "value",unescape( id ) );
		$("#feed_name").attr( "value", unescape(feed_name) );
		$("#feed_url").attr("value",
		 	unescape(feed_url));
		
		$("#feed_active").attr( "value",
		 	unescape( feed_active ));
		
		
		// Submit Form
		$("#feedMain").submit();			
	
	} else{
		return false;
	}
}
