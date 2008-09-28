/*
		* Categories Javascript
		*/
		// Categories JS
		function fillFields(id, username, name, role, email)
		{
			$("#user_id").attr("value", unescape(id));
			$("#username").attr("value", unescape(username));
			$("#name").attr("value", unescape(name));
			$('#role').attr("value",unescape( $('#role').val() ) );
			$('#email').attr("value",unescape( email ) );
			
		}