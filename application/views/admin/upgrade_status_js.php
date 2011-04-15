var i=0;
var backup = <?php echo ($backup) ? 1 : 0; ?>;
$(document).ready(function() {
	upgrade_error = false;
	for (i=0; i < 8; i++) {
		if (backup == 0 && i == 4){
			i = i + 1;
		} else if (backup == 1 && i == 5) {
			i = i + 1;
		}
		$.ajax({
			url: "<?php echo url::site()."admin/upgrade/status/";?>"+i,
			async: false,
			dataType: "json",
			success: function(data) {
				if (data.status == 'success'){
					$('#upgrade_log').append("<div class=\"upgrade_log_message log_success\">"+data.message+"</div>");
				} else if (data.status == 'error') {
					$('#upgrade_log').append("<div class=\"upgrade_log_message log_error\">"+data.message+" <a href=\"<?php echo $log_file; ?>\" target=\"_blank\">Log File</a></div>");
					upgrade_error = true;
				} else {
					upgrade_error = true;
				}
			}
		});
			
		if (upgrade_error) {
			break;
		}
	}
});