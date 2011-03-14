<div id="container">

	<div class="showcase">

		<div class="checkins">
			
			<div id="cilist"></div>
			
			<div id="downloads">
				<p class="dl-btns">
					<h3>Checkin with the app</h3>
					<div>You can submit your own checkins to this deployment by downloading the application pointing it at <?php echo url::site(); ?>.</div>
					<a href="http://download.ushahidi.com/dl/ios" class="dl-btn dl-ios">iOS</a> <a href="http://download.ushahidi.com/dl/android" class="dl-btn dl-android">Android</a>
				</p>
			</div>
            
        </div><!--/checkins-->
        
        <div id="mapContainer">
    	<?php								
			// Map Blocks
			echo $div_map;
			echo $div_timeline;
			if(isset($_GET['widget'])){
				echo '<div style="clear:both;"></div><a href="'.url::site().'" target="_top" class="fullsite">See the full version</a>';
			}
		?>
    	</div><!--/map-->
    	
    	<script type="text/javascript">
			showCheckins();
		</script>
    
    </div><!--/showcase-->
    	
    <div id="sidebar">
				
		<div class="report" style="display:none;">
			
		</div>
		
		<script type="text/javascript">
				
			function cilisting() {
				jsonurl = '<?php echo url::site()."api/?task=checkin&action=get_ci&sqllimit=1000&orderby=checkin.checkin_date&sort=DESC"; ?>';
				$.getJSON(jsonurl, function(data) {
					$('div#cilist').html("");
					
					var user_colors = new Array();
					// Get colors
					$.each(data.payload.users, function(i, payl) {
						user_colors[payl.id] = payl.color;
					});
					
					$.each(data.payload.checkins, function(i,item){
						
						if(i == 0)
						{
							$('div#cilist').append("<div class=\"checkin\" class=\"ci_id_"+item.id+"\"style=\"border:none\"><a name=\"ci_id_"+item.id+"\" />");
						}else{
							$('div#cilist').append("<div class=\"checkin\" class=\"ci_id_"+item.id+"\" style=\"padding-bottom:5px;margin-bottom:5px;\"><a name=\"ci_id_"+item.id+"\" />");
						}
						
						if(item.media === undefined)
						{
							// Tint the color a bit
							$('div#cilist').append("<div class=\"colorblock shorterblock\" style=\"background-color:#"+user_colors[item.user]+";\"><div class=\"colorfade\"></div></div>");
						}else{
							// Show image
							$('div#cilist').append("<div class=\"colorblock tallerblock\" style=\"background-color:#"+user_colors[item.user]+";\"><div class=\"imgblock\"><a href=\""+item.media[0].link+"\" rel=\"lightbox-group1\" title=\""+item.msg+"\"><img src=\""+item.media[0].thumb+"\" width=\"100\" /></a></div></div>");
						}
						
						$('div#cilist').append("<div style=\"float:right;width:24px;height:24px;margin-right:10px;\"><a class=\"moredetails\" reportid=\""+item.id+"\" href=\"javascript:externalZeroIn("+item.lon+","+item.lat+",16,"+item.id+");\"><img src=\"<?php echo url::site(); ?>/themes/ci_cumulus/images/earth_trans.png\" width=\"24\" height=\"24\" /></a></div>");
						
						$.each(data.payload.users, function(j,useritem){
							if(useritem.id == item.user){
								$('div#cilist').append("<h3 style=\"font-size:14px;width:340px;padding-top:0px;\">"+useritem.name+"</h3>");
							}
						});
						
						var utcDate = item.date.replace(" ","T")+"Z";
						
						if(item.msg == "")
						{
							$('div#cilist').append("<div class=\"cimsg\"><small><em>"+$.timeago(utcDate)+"</em></small></div>");
						}else{
							$('div#cilist').append("<div class=\"cimsg\">"+item.msg+"<br/><small><em>"+$.timeago(utcDate)+"</em></small></div>");
						}
						$('div#cilist').append("<div style=\"clear:both\"></div></div>");
					});
				});
			}
			
			cilisting();

			
		</script>
	</div>
