function emailTest() {
	$('#test_loading').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
	$('#test_status').html('');
	$.post("<?php echo url::site() . 'admin/settings/test_email/' ?>", {  },
		function(data){
			if (data.status == 'success'){
				$('#test_status').html(data.message);
			} else {
				$('#test_status').html(data.message);
			}
			$('#test_loading').html('');
	  	}, "json");
}