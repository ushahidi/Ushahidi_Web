/*
		* Messages Javascript
		*/
		
		function limitChars(textid, limit, infodiv)
		{
			var text = $('#'+textid).val();	
			var textlength = text.length;
			if(textlength > limit)
			{
				$('#' + infodiv).html('You cannot write more then '+limit+' characters!');
				$('#'+textid).val(text.substr(0,limit));
				return false;
			}
			else
			{
				$('#' + infodiv).html('You have '+ (limit - textlength) +' characters left.');
				return true;
			}
		}
		
		function showReply(id)
		{
			if (id) {
				$('#' + id).toggle(400);
			}
		}
		
		function sendMessage(id, loader)
		{
			$('#' + loader).html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			$.post("<?php echo url::base() . 'admin/messages/send/' ?>", { to_id: id, message: $("#message_" + id).attr("value") },
				function(data){
					if (data.status == 'sent'){
						$('#reply_' + id).hide();
					} else {
						$('#replyerror_' + id).show();
						$('#replyerror_' + id).html(data.message);
						alert('ERROR!');
					}
					$('#' + loader).html('');
			  	}, "json");
		}
		
		function cannedReply(id, field)
		{
			var autoreply;
			$("#" + field).attr("value", "");
			if (id == 1) {
				autoreply = "Thank you for sending a message to Ushahidi. What is the closest town or city for your last message?";
			}else if (id == 2) {
				autoreply = "Thank you for sending a message to Ushahidi. Can you send more information on the incident?"
			};
			$("#" + field).attr("value", autoreply);		
		}

        function submitIds()
        {
            if (confirm("Delete cannot be undone. Are you sure you want to continue?"))
                $('#messagesMain').submit(); 
        }

		
