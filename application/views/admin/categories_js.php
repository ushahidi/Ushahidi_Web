/*
 * Categories Javascript
 */
// Categories JS
function fillFields(id, category_title, category_description, category_color)
{
	$("#category_id").attr("value", unescape(id));
	$("#category_title").attr("value", unescape(category_title));
	$("#category_description").attr("value", unescape(category_description));
	$("#category_color").attr("value", unescape(category_color));
}

// Ajax Submission
function userAction ( action, id, category_title, category_description,
 category_color, confirmAction )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Submit Type
		$("#action").attr("value", action);
		// Set form values for deletion so it passes the validation test
		$("#category_id").attr("value",id);
		$("#category_title").attr("value", unescape(category_title));
		$("#category_description").attr("value",
		 	unescape(category_description));
		$('#category_color').attr("value",unescape( category_color ) );
		
		// Submit Form
		$("#catMain").submit();			
	
	} else{
		return false;
	}
}