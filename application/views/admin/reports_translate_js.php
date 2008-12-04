/*
		* Translate Reports Javascript
		*/

		/* Dynamic categories */
		$(document).ready(function() {			
			// Action on Save Only
			$("#save_only").click(function () {
				$("#save").attr("value", "1");
			});
			
			// Action on Cancel
			$("#cancel").click(function () {
				window.location.href='<?php echo url::base() . 'admin/reports/' ?>';
				return false;
			});
		});