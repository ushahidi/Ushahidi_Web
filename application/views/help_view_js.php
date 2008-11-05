/*
		* View Reports Javascript
		*/
		jQuery(function() {
			/*
			Send Message JS
			*/			
			// Ajax Validation
			$("#sendMessage").validate({
				rules: {
					name: {
						required: true,
						minlength: 3
					},
					email: {
						email: true
					},
					phone: {
						minlength: 3
					},
					message: {
						required: true,
						minlength: 3
					},
					captcha: {
						required: true
					}
				},
				messages: {
					name: {
						required: "Please enter your Name",
						minlength: "Your Name must consist of at least 3 characters"
					},
					email: {
						required: "Please enter an Email Address",
						email: "Please enter a valid Email Address"
					},
					phone: {
						minlength: "Your Phone number is invalid"
					},
					message: {
						required: "Please enter a Comment",
						minlength: "Your Comment must be at least 3 characters long"
					},
					captcha: {
						required: "Please enter the Security Code"
					}
				}
			});
		});