<script type="text/javascript" charset="utf-8">
function analysisMapme(newlon, newlat) {
	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
	
  if (!isNaN(newlat) && !isNaN(newlon))
  {
    // Clear the map first
    point = new OpenLayers.Geometry.Point(newlon, newlat);
    OpenLayers.Projection.transform(point, proj_4326,proj_900913);

    f = new OpenLayers.Feature.Vector(point);
    vlayer.addFeatures(f);

    // create a new lat/lon object
    myPoint = new OpenLayers.LonLat(newlon, newlat);
    myPoint.transform(proj_4326, map.getProjectionObject());

    // display the map centered on a latitude and longitude
    map.setCenter(myPoint, <?php echo $default_zoom; ?>);
  }
	
	// Update Form Value
	$("#latitude").attr("value", lat);
	$("#longitude").attr("value", lon);
}
$(document).ready(function() {
	$('a#analysis_toggle').click(function() {
		$('#analysis_report_details').toggle(400);
		return false;
	});
});
</script>