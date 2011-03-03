<div id="container">

	<div class="container showcase">

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
		?>
		<script type="text/javascript">
			showCheckins();
		</script>
    	</div><!--/map-->
    
    </div><!--/showcase-->
    	
    <div id="sidebar">
				
		<div class="report" style="display:none;">
			
		</div>
		
		<script type="text/javascript">
			/*
			$('a.moredetails').click(function() {
				var rid = $(this).attr('reportid');
				showreport(rid);
			});
			
			function showreport(reportid) {
				$('div.downloads').slideUp('slow', function() {
					jsonurl = '<?php echo url::site()."api/?task=checkin&action=get_ci&id="; ?>'+reportid;
					$.getJSON(jsonurl, function(data) {
						$.each(data.payload.checkins, function(i,item){
							$('div.report').html("");
							$('div.report').append("<div><h2>????</h2></div><div style=\"text-style:italic;\">"+item.date+"</div>");
							
							$('div.report').append("<div>"+item.msg+"</div>");
							
							if(item.media === undefined)
							{
								// Not set so don't show any media
							}else{
								$.each(item.media, function(j,media){
									$('div.report').append("<div>"+media.medium+"<img src=\""+media.medium+"\" /></div>");
								});
							}
							
							$('div.report').slideDown('slow', function() {
								// Animation complete.
							});
						});
					});
				});
			}
			*/
		</script>
		
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
							$('div#cilist').append("<div class=\"checkin\" style=\"border:none\">");
						}else{
							$('div#cilist').append("<div class=\"checkin\" style=\"padding-bottom:5px;margin-bottom:5px;\">");
						}
						
						if(item.media === undefined)
						{
							// Tint the color a bit
							$('div#cilist').append("<div style=\"width:110px;height:20px;float:left;margin:0px 10px 0px 0px;background-color:#"+user_colors[item.user]+";\"><div style=\"width:100px;height:20px;float:left;margin:0px;background-color:#FFFFFF;opacity:.5;\"></div></div>");
						}else{
							// Show image
							$('div#cilist').append("<div style=\"width:110px;height:55px;float:left;margin:0px 10px 0px 0px;background-color:#"+user_colors[item.user]+";\"><div style=\"width:100px;height:55px;overflow:hidden;float:left;margin:0px;\"><a href=\""+item.media[0].link+"\" rel=\"lightbox-group1\" title=\""+item.msg+"\"><img src=\""+item.media[0].thumb+"\" width=\"100\" /></a></div></div>");
						}
						
						$('div#cilist').append("<div style=\"float:right;width:24px;height:24px;margin-right:10px;\"><a class=\"moredetails\" reportid=\""+item.id+"\" href=\"javascript:externalZeroIn("+item.lon+","+item.lat+",16);\"><img src=\"<?php echo url::site(); ?>/themes/ci_cumulus/images/earth_trans.png\" width=\"24\" height=\"24\" /></a></div>");
						
						$.each(data.payload.users, function(j,useritem){
							if(useritem.id == item.user){
								$('div#cilist').append("<h3 style=\"font-size:14px;width:340px;padding-top:0px;\">"+useritem.name+"</h3>");
							}
						});
						
						if(item.msg == "")
						{
							$('div#cilist').append("<div style=\"padding-left:120px;\"><small><em>"+$.timeago(item.date)+"</em></small></div>");
						}else{
							$('div#cilist').append("<div style=\"padding-left:120px;\">"+item.msg+"<br/><small><em>"+$.timeago(item.date)+"</em></small></div>");
						}
						$('div#cilist').append("<div style=\"clear:both\"></div></div>");
					});
				});
			}
			
			cilisting();

			
		</script>
	</div>
