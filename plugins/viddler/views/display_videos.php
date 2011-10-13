<div class="report-media">
	<?php
	foreach($videos as $video) {
		if($video->embed_small != NULL){
			echo $video->embed.'<br/>';
		}
	} ?>
</div>