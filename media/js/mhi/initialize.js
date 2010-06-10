$(function(){
		   
	//Sign-in toggle	   
	$('.sign-in').toggle(function(){
		//show the dagum form	
		$("#login-form").show()
		//add the active class to sign-in link
		$(this).addClass("active")
	}, function(){
		//hide the dagum form
		$("#login-form").hide()
		//remove the active class from the sign-in link
		$(this).removeClass("active")
	});
	
});