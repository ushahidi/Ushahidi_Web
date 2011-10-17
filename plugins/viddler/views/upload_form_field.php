<script type="text/javascript">	
	// Check filesize
	function checkFilesize(idname)
	{
		if (typeof FileReader !== "undefined") {
			var limit = <?php echo $maximum_filesize; ?>;
		    var size = document.getElementById(idname).files[0].size;
		    if(limit < size){
		    	var display_limit = limit / 1024;
		    	var display_size = size / 1024;
		    	alert('<?php echo Kohana::lang('ui_main.file_over_max_allowed'); ?> <?php echo Kohana::lang('ui_main.maximum_filesize'); ?>: '+display_limit+'kb.');
		    	// Remove the selection from the form field
		    	document.getElementById(idname).value = '';
		    }
		}
	}
</script>

<div id="divVideo" class="report_row">
	<h4><?php echo Kohana::lang('ui_main.upload_video'); ?></h4>
	<input type="file" name="incident_video_file" id="incident_video_file" value="" class="text long2" onchange="checkFilesize('incident_video_file')" />
	<div style="clear:both;"><?php echo Kohana::lang('ui_main.maximum_filesize'); ?>: <?php echo number_format($maximum_filesize/1024); ?>kb</div>
</div>