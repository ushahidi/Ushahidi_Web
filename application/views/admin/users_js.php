/*
		* Categories Javascript
		*/
		// Categories JS
		function fillFields(id, username, name, role, email)
		{
			$("#user_id").attr("value", unescape(id));
			$("#username").attr("value", unescape(username));
			$("#name").attr("value", unescape(name));
			$('#role').attr("value",unescape( role ) );
			$('#email').attr("value",unescape( email ) );
			
		}
		
		// Form Submission
		function userAction ( action, confirmAction, id )
		{
			var statusMessage;
			var answer = confirm('Are You Sure You Want To ' 
				+ confirmAction + ' users?')
			if (answer){
				// Set Category ID
				$("#user_id").attr("value", id);
				// Set Submit Type
				$("#action").attr("value", action);		
				// Submit Form
				$("#userMain").submit();			

			}
		}