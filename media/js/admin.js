(function($) {
	info_search = function() {
		$("#info-search").submit();
	}
	show_addedit = function(toggle) {
		var addEditForm = $("#addedit");
		if (toggle)
		{
			addEditForm.toggle(400);
		}
		else
		{
			addEditForm.show(400);
		}
		// Clear fields, but not buttons or the CSRF token.
		$(':input', '#addedit')
			.not(':button, :submit, :reset, #action, :checkbox, [name="form_auth_token"]')
			.val('')
			.removeAttr('selected');

		// Reset checkbox separately to avoid wiping its value
		$(':checkbox', '#addedit').removeAttr('checked');

		$("a.add").focus();
	}
})(jQuery);
