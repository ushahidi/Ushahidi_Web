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
		
		// Ajax Submission
		function userAction ( action, user_id,username, 
			name, role, email, confirmAction )
		{
			var statusMessage;
			var answer = confirm('Are You Sure You Want To ' + confirmAction + ' items?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);

				// Set form values for deletion so it passes the validation test
				$("#user_id").attr("value",user_id);
				$("#username").attr("value", unescape(username));
				$("#name").attr("value", unescape(name));
				$('#role').attr("value",unescape( role ) );
				$('#email').attr("value",unescape( email ) );
				
				// Submit Form
				$("#userMain").submit();			
			}
			else{
				return false;
			}
		}