/*
 * Feeds Javascript
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

// Form Submission
function feedAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction + ' items?')
	if (answer){
		// Set Category ID
		$("#feed_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#feedListing").submit();			
	
	} else{
		return false;
	}
}

// Ajax Refresh Feeds
function refreshFeeds()
{
	$('#feeds_loading').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
	$("#action").attr("value", 'r');		
	// Submit Form
	$("#feedListing").submit();
}