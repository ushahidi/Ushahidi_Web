<div class="bg">
	
	<h2>
		<?php admin::manage_subtabs("publiclisting"); ?>
	</h2>
	
	<iframe src ="http://tracker.ushahidi.com/list/manage.php?id=<?php echo $encoded_stat_id; ?>&key=<?php echo $encoded_stat_key; ?>" width="100%" height="700" border="0" style="border:0px;">
	<p>Error: Your browser does not support iframes.</p>
	</iframe>
	
</div>