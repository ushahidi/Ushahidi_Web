/*
		* SMS Global Javascript
		*/
		
		// Retrieve Clickatell Balance (AJAX)
		function clickatellBalance()
		{
			$('#balance_loading').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			$.get("<?php echo url::base() . 'admin/settings/smsbalance/' ?>", function(data){
				alert("RESPONSE: " + data);
				$('#balance_loading').html('');
			});
		}