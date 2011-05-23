function formatItem(row) {
	return row[0];
}
function formatResult(row) {
	return row[0].replace(/(<.+?>)/gi, '');
}
jQuery(document).ready(function() {
	$("#private_to").autocomplete('<?php echo url::site()."members/private/get_user/"; ?>', {
		multiple: true,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult
	});
});