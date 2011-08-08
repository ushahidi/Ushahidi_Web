<h2>Check In</h2>

<div>
	<div>Hello, <?php echo $loggedin_name; ?>!</div>
	<div id="location_status">...</div>
	
	<table width="100%" border="0" cellspacing="3" cellpadding="4" id="citable" style="display:none;">
	<form action="<?php echo url::site() ?>mobileci/ci" method="post" id="checkinform" enctype="multipart/form-data">
		<input type="hidden" name="lat" id="lat" value="" />
		<input type="hidden" name="lon" id="lon" value="" />
		<tr>
			<td><strong><?php echo Kohana::lang('ui_main.message');?>:</strong><br />
			<input type="text" name="message" id="message" value="" /></td>
		</tr>
		<tr>
			<td><strong>Photo:</strong><br />
			<input type="file" id="photo" name="photo" id="photo" /></td>
		</tr>
		<tr>
			<td><small>These fields are not required to check in.</small></td>
		</tr>
		<tr>
			<td><input type="submit" id="submit" value="Check In" /></td>
		</tr>
	</form>
	</table>
	
	
		
	</form>
</div>

<div>
	<br/><small><a href="<?php echo url::site() ?>logout">Logout</a></small>
</div>

<?php echo html::script('plugins/mobileci/views/js/gears_init', true); ?>
<?php echo html::script('plugins/mobileci/views/js/geo', true); ?>

<script type="text/javascript">
	
	function geo_success(loc) {
		$("#location_status").html('Location Found: '+loc.coords.latitude+','+loc.coords.longitude);
		$("#citable").css('display','inline');
		$("#lat").val(loc.coords.latitude);
		$("#lon").val(loc.coords.longitude);
	}
	
	function geo_error() {
		$("#location_status").html('Sorry, location search failed. Please try again.');
	}
	
	if (geo_position_js.init()) {
		$("#location_status").html('Please wait while we locate your device.');
		geo_position_js.getCurrentPosition(geo_success, geo_error);
	}else{
		$("#location_status").html('Sorry, your browser does not support geolocation.');
	}
	
</script>