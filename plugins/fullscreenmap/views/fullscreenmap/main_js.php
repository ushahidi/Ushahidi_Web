<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	var orig_width = $("#map").width();
	var orig_height = $("#map").height();
	
	currZoom = map.getZoom();
	currCenter = map.getCenter();
	
	$(".fullscreenmap_click").colorbox({
		width:"100%", 
		height:"100%", 
		inline:true, 
		href:"#map",
		// Resize Map DIV and Refresh
		onComplete:function(){
		    $("#map").width("99%");
			$("#map").height("99%");
//			$("#map").append(<?php echo $categories_view;?>);
//			$(".fullscreenmap_cats").draggable( { handle: 'h2' } );
			map.setCenter(currCenter, currZoom, false, false);
		},
		// Return DIV to original state
		onClosed:function(){
			$("#map").width(orig_width);
			$("#map").height(orig_height);
			$("#map").show();
			map.setCenter(currCenter, currZoom, false, false);
		}
	});
});
</script>