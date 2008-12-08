/*
 * Categories Javascript
 */
// Categories JS
function fillFields(id, category_title, category_description, category_color, locale)
{
	$("#category_id").attr("value", unescape(id));
	$("#category_title").attr("value", unescape(category_title));
	$("#category_description").attr("value", unescape(category_description));
	$("#category_color").attr("value", unescape(category_color));
	$("#locale").attr("value", unescape(locale));
}

// Ajax Submission
function catAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Category ID
		$("#category_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#catListing").submit();
	}
}