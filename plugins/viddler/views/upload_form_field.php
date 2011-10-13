<script type="text/javascript">
	function deleteViddler (id, div)
	{
		var answer = confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to_delete_this_item'); ?>?");
	    if (answer){
			$("#" + div).effect("highlight", {}, 800);
			$.get("<?php echo url::base() . 'admin/viddler/delete/' ?>" + id);
			$("#" + div).remove();
	    }
		else{
			return false;
	    }
	}
	
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
<div class="row link-row">
	<h4><?php echo Kohana::lang('ui_main.upload_video');?></h4>
	<?php foreach($videos as $video) {
		if($video->embed_small != NULL){
		?>
			<div id="viddler_<?php echo $video->viddler_id; ?>">
				<?php echo $video->embed_small; ?>
				&nbsp;&nbsp;<a href="#" onclick="deleteViddler('<?php echo $video->viddler_id; ?>', 'viddler_<?php echo $video->viddler_id; ?>'); return false;"><?php echo Kohana::lang('ui_main.delete'); ?></a>
			</div>
		<?php
		}else{
			echo '<div style="width:200px;height:100px;background-color:#000;color:#FFF;font-weight:bold;padding-top:40px;text-align:center;">'.Kohana::lang('ui_admin.video_encoding').'</div>';
		}
	} ?>
</div>
<div id="divVideo">
	<input type="file" name="incident_video_file" id="incident_video_file" value=""  class="text long" onchange="checkFilesize('incident_video_file')" />
	<div style="clear:both;"><?php echo Kohana::lang('ui_main.maximum_filesize'); ?>: <?php echo number_format($maximum_filesize/1024); ?>kb</div>
</div>