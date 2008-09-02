/*
 * Javascript Helper functions
 * 
 */

// Add/Remove Form Fields
function addFormField(div, field, hidden_id) {
	var id = document.getElementById(hidden_id).value;
	$("#" + div).append("<div class=\"row link-row second\" id=\"" + field + "_" + id + "\"><input type=\"text\" name=\"" + field + "[]\" class=\"text long\" /><a href=\"#\" class=\"add\" onClick=\"addFormField('" + div + "','" + field + "','" + hidden_id + "'); return false;\">add</a><a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" + field + "_" + id + "\"); return false;'>remove</a></div>");

	$("#" + field + "_" + id).highlightFade({
		speed:1000
	});
	
	id = (id - 1) + 2;
	document.getElementById(hidden_id).value = id;
}

function removeFormField(id) {
	$(id).remove();
}
