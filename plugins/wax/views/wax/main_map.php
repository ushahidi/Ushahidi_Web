<div id="map" class="map"></div>

<script type="text/javascript">

var tilejson = {
  tilejson: '1.0.0',
  scheme: 'tms',
  tiles: ['http://a.tiles.mapbox.com/mapbox/1.0.0/world-light/{z}/{x}/{y}.png'],
  grids: ['http://a.tiles.mapbox.com/mapbox/1.0.0/geography-class/{z}/{x}/{y}.grid.json'],
  formatter: function(options, data) { return data.NAME }
};

var map = new L.Map('map')
  .addLayer(new wax.leaf.connector(tilejson))
  .setView(new L.LatLng(51.505, -0.09), 1);
wax.leaf.interaction(map, tilejson);

var circleLocation = new L.LatLng(51.508, -0.11),
    circleOptions = {
        color: 'red', 
        fillColor: '#f03', 
        fillOpacity: 0.5
    };
    
var circle = new L.Circle(circleLocation, 500, circleOptions);
map.addLayer(circle);

</script>

