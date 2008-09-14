/*
		* Categories Javascript
		*/
		// Categories JS
		function fillFields(id, username, name, role)
		{
			$("#user_id").attr("value", unescape(id));
			$("#username").attr("value", unescape(username));
			$("#name").attr("value", unescape(name));
			$("#role").attr("value", unescape(role));
		}