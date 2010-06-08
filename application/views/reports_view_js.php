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
		var map;
		jQuery(function() {
			var moved=false;

			/*
			- Initialize Map
			- Uses Spherical Mercator Projection			
			*/
			var proj_4326 = new OpenLayers.Projection('EPSG:4326');
			var proj_900913 = new OpenLayers.Projection('EPSG:900913');
			var options = {
				units: "dd",
				numZoomLevels: 16,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326
				};
			map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 3)
			{
				map_layer = new OpenLayers.Layer.Yahoo("yahoo", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 4)
			{
				map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else
			{
				map_layer = new OpenLayers.Layer.Google("google", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
	
			map.addLayer(map_layer);
	
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition(
					{ div: 	document.getElementById('mapMousePosition'), numdigits: 5 
				}));    
			map.addControl(new OpenLayers.Control.Scale('mapScale'));
			map.addControl(new OpenLayers.Control.ScaleLine());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			
			// Set Feature Styles
			style = new OpenLayers.Style({
				pointRadius: "8",
				fillColor: "${color}",
				fillOpacity: "1",
				strokeColor: "#000000",
				strokeWidth: 1,
				strokeOpacity: 0.8
			},
			{
				context: 
				{
					color: function(feature)
					{
						if ( typeof(feature) != 'undefined' && 
							feature.data.id == <?php echo $incident_id; ?>)
						{
							return "#CC0000";
						}
						else
						{
							return "#FF9933";
						}
					}
				}
			});
			
			// Create the single marker layer
			var markers = new OpenLayers.Layer.GML("single report", "<?php echo url::site() . 'json/single/' . $incident_id; ?>", 
			{
				format: OpenLayers.Format.GeoJSON,
				projection: map.displayProjection,
				styleMap: new OpenLayers.StyleMap({"default":style, "select": style})
			});
			
			map.addLayer(markers);
			
			selectControl = new OpenLayers.Control.SelectFeature(markers,
															{onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});			

			map.addControl(selectControl);
			selectControl.activate();

			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			myPoint.transform(proj_4326, map.getProjectionObject());
			
			// display the map centered on a latitude and longitude (Google zoom levels)

			map.setCenter(myPoint, 10);			
			
			function onPopupClose(evt) {
	            selectControl.unselect(selectedFeature);
	        }
	        function onFeatureSelect(feature) {
	            selectedFeature = feature;
				// Lon/Lat Spherical Mercator
				zoom_point = feature.geometry.getBounds().getCenterLonLat();
				lon = zoom_point.lon;
				lat = zoom_point.lat;
	            var content = "<div class=\"infowindow\"><div class=\"infowindow_list\"><ul><li>"+feature.attributes.name + "</li></ul></div>";
				content = content + "\n<div class=\"infowindow_meta\"><a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", 1)'>Zoom&nbsp;In</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", -1)'>Zoom&nbsp;Out</a></div>";
				content = content + "</div>";
				// Since KML is user-generated, do naive protection against
	            // Javascript.
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
		
		function zoomToSelectedFeature(lon, lat, zoomfactor){
			var lonlat = new OpenLayers.LonLat(lon,lat);
			map.panTo(lonlat);
			// Get Current Zoom
			currZoom = map.getZoom();
			// New Zoom
			newZoom = currZoom + zoomfactor;
			map.zoomTo(newZoom);
		}
		
		jQuery(window).bind("load", function() {
			jQuery("div#slider1").codaSlider()
			// jQuery("div#slider2").codaSlider()
			// etc, etc. Beware of cross-linking difficulties if using multiple sliders on one page.
		});
		
		function rating(id,action,type,loader)
		{
			$('#' + loader).html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			$.post("<?php echo url::site() . 'reports/rating/' ?>" + id, { action: action, type: type },
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