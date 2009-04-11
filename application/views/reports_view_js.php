<?php
/**
 * Reports view js file.
 *
 * Handles javascript stuff related to reports view function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		jQuery(function() {
			var moved=false;

			// Photoslider
			photos = ["<?php echo join($incident_photos, '","'); ?> "];
			FOTO.Slider.baseURL = "<?php echo url::base() . 'media/uploads/'; ?>";
			FOTO.Slider.bucket = {  
         		'default': {}  
     		}; 
     		for(var i = 0; i<photos.length; i++) {
     			FOTO.Slider.bucket['default'][i] = {'main': photos[i], 
     			                                    'thumb': photos[i].replace('.jpg', '_t.jpg')};
     		}
     		FOTO.Slider.reload('default');  
			FOTO.Slider.preloadImages('default');
			
	
			// Now initialise the map
			var options = {
			units: "dd"
			, numZoomLevels: 16
			, controls:[]};
			var map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				var map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth");
			}
			else if (default_map == 3)
			{
				var map_layer = new OpenLayers.Layer.Yahoo("yahoo");
			}
			else if (default_map == 4)
			{
				var map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap");
			}
			else
			{
				var map_layer = new OpenLayers.Layer.Google("google");
			}
	
			map.addLayer(map_layer);
	
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
				
			var style = new OpenLayers.Style({
				pointRadius: "10",
				fillColor: "${color}",
				fillOpacity: 1,
				strokeColor: "#000000",
				strokeWidth: 1,
				strokeOpacity: 1
			}, 
			{
				context: 
				{
					color: function(feature) 
					{
						return "#" + feature.attributes.color;
					}
				}
			});
			
			// Create the single marker layer
			var markers = new OpenLayers.Layer.GML("single report", "<?php echo url::base() . 'json/?i=' . $incident_id ?>", 
			{
				format: OpenLayers.Format.GeoJSON,
				projection: new OpenLayers.Projection("EPSG:4326"),
				styleMap: new OpenLayers.StyleMap({"default":style})
			});
			
			// Create neighboring marker layer
			var markers_2 = new OpenLayers.Layer.GML("neighboring reports", "<?php echo url::base() . 'json/?n=yes' ?>", 
			{
				format: OpenLayers.Format.GeoJSON,
				projection: new OpenLayers.Projection("EPSG:4326"),
				styleMap: new OpenLayers.StyleMap({"default":style})
			});
			
			map.addLayers([markers_2, markers]);
			
			selectControl = new OpenLayers.Control.SelectFeature(markers,
                {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
			selectControl2 = new OpenLayers.Control.SelectFeature(markers_2,
                {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});			

            map.addControl(selectControl);
			map.addControl(selectControl2);
            selectControl.activate();
			selectControl2.activate();

			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
	
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);
			
			
			function onPopupClose(evt) {
	            selectControl.unselect(selectedFeature);
	        }
	        function onFeatureSelect(feature) {
	            selectedFeature = feature;
	            // Since KML is user-generated, do naive protection against
	            // Javascript.
	            var content = "<div class=\"infowindow\"><h2>"+feature.attributes.name + "</h2>" + feature.attributes.description + "</div>";
	            if (content.search("<script") != -1) {
	                content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/</g, "&lt;");
	            }
	            popup = new OpenLayers.Popup.FramedCloud("chicken", 
	                                     feature.geometry.getBounds().getCenterLonLat(),
	                                     new OpenLayers.Size(100,100),
	                                     content,
	                                     null, true, onPopupClose);
	            feature.popup = popup;
	            map.addPopup(popup);
	        }
	        function onFeatureUnselect(feature) {
	            map.removePopup(feature.popup);
	            feature.popup.destroy();
	            feature.popup = null;
	        }
			
			
			/*
			Add Comments JS
			*/			
			// Ajax Validation
			$("#commentForm").validate({
				rules: {
					comment_author: {
						required: true,
						minlength: 3
					},
					comment_email: {
						required: true,
						email: true
					},
					comment_description: {
						required: true,
						minlength: 3
					},
					captcha: {
						required: true
					}
				},
				messages: {
					comment_author: {
						required: "Please enter your Name",
						minlength: "Your Name must consist of at least 3 characters"
					},
					comment_email: {
						required: "Please enter an Email Address",
						email: "Please enter a valid Email Address"
					},
					comment_description: {
						required: "Please enter a Comment",
						minlength: "Your Comment must be at least 3 characters long"
					},
					captcha: {
						required: "Please enter the Security Code"
					}
				}
			});
		});
		
		jQuery(window).bind("load", function() {
			jQuery("div#slider1").codaSlider()
			// jQuery("div#slider2").codaSlider()
			// etc, etc. Beware of cross-linking difficulties if using multiple sliders on one page.
		});
		
		function rating(id,action,type,loader)
		{
			$('#' + loader).html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			$.post("<?php echo url::base() . 'reports/rating/' ?>" + id, { action: action, type: type },
				function(data){
					if (data.status == 'saved'){
						if (type == 'original') {
							$('#oup_' + id).attr("src","<?php echo url::base() . 'media/img/'; ?>gray_up.png");
							$('#odown_' + id).attr("src","<?php echo url::base() . 'media/img/'; ?>gray_down.png");
							$('#orating_' + id).html(data.rating);
						}
						else if (type == 'comment')
						{
							$('#cup_' + id).attr("src","<?php echo url::base() . 'media/img/'; ?>gray_up.png");
							$('#cdown_' + id).attr("src","<?php echo url::base() . 'media/img/'; ?>gray_down.png");
							$('#crating_' + id).html(data.rating);
						}
					} else {
						alert('ERROR!');
					}
					$('#' + loader).html('');
			  	}, "json");
		}