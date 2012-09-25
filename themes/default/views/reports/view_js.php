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
 * @module     Reports Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

// Set the base url
Ushahidi.baseURL = "<?php echo url::site(); ?>";

jQuery(window).load(function() {

	<?php echo map::layers_js(FALSE); ?>

	// Configuration for the map
	var mapConfig = {

		// Zoom level
		zoom: <?php echo ($incident_zoom) ? $incident_zoom : intval(Kohana::config('settings.default_zoom')); ?>,

		// Map center
		center: {
			latitude: <?php echo $latitude; ?>,
			longitude: <?php echo $longitude; ?>
		},

		// Map controls
		mapControls: [
			new OpenLayers.Control.Navigation({ dragPanOptions: { enableKinetic: true } }),
			new OpenLayers.Control.Zoom(),
			new OpenLayers.Control.MousePosition(),
			new OpenLayers.Control.ScaleLine(),
			new OpenLayers.Control.Scale('mapScale'),
			new OpenLayers.Control.LayerSwitcher(),
			new OpenLayers.Control.Attribution()
		],

		// Base layers
		baseLayers: <?php echo map::layers_array(FALSE); ?>

	};

	// Set Feature Styles
	var style1 = new OpenLayers.Style({
		pointRadius: "8",
		fillColor: "${fillcolor}",
		fillOpacity: "0.7",
		strokeColor: "${strokecolor}",
		strokeOpacity: "0.7",
		strokeWidth: "${strokewidth}",
		graphicZIndex: 1,
		externalGraphic: "${graphic}",
		graphicOpacity: 1,
		graphicWidth: 21,
		graphicHeight: 25,
		graphicXOffset: -14,
		graphicYOffset: -27
	},
	{
		context: 
		{
			graphic: function(feature) {
				if (typeof(feature) != 'undefined' && 
					feature.data.id == <?php echo $incident_id; ?>)
				{
					return "<?php echo url::file_loc('img').'media/img/openlayers/marker.png' ;?>";
				}
				else
				{
					return "<?php echo url::file_loc('img').'media/img/openlayers/marker-gold.png' ;?>";
				}
			},
			fillcolor: function(feature) {
				if ( typeof(feature.attributes.color) != 'undefined' && 
					feature.attributes.color != '' )
				{
					return "#"+feature.attributes.color;
				}
				else
				{
					return "#ffcc66";
				}
			},
			strokecolor: function(feature) {
				if ( typeof(feature.attributes.strokecolor) != 'undefined' && 
					feature.attributes.strokecolor != '')
				{
					return "#"+feature.attributes.strokecolor;
				}
				else
				{
					return "#CC0000";
				}
			},					
			strokewidth: function(feature) {
				if ( typeof(feature.attributes.strokewidth) != 'undefined' && 
					feature.attributes.strokewidth != '')
				{
					return feature.attributes.strokewidth;
				}
				else
				{
					return "3";
				}
			}
		}
	});

	var style2 = new OpenLayers.Style({
		pointRadius: "8",
		fillColor: "#30E900",
		fillOpacity: "0.7",
		strokeColor: "#197700",
		strokeWidth: 3,
		graphicZIndex: 1
	});


	// Styles to use for rendering the markers
	var styleMap = new OpenLayers.StyleMap({
		'default': style1,
		'select': style1,
		'temporary': style2
	});


	// Initialize the map
	var map = new Ushahidi.Map('map', mapConfig);
	map.addLayer(Ushahidi.GEOJSON, {
		name: "Single Report",
		url: "<?php echo 'json/single/'.$incident_id; ?>",
		styleMap: styleMap
	});
	
	// Ajax Validation for the comments
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
	
	// Handles the functionality for changing the size of the map
	// TODO: make the CSS widths dynamic... instead of hardcoding, grab the width's
	// from the appropriate parent divs
	$('.map-toggles a').click(function() {
		var action = $(this).attr("class");
		$('ul.map-toggles li').hide();
		switch(action)
		{
			case "wider-map":
				$('.report-map').insertBefore($('.left-col'));
				$('.map-holder').css({"height":"350px", "width": "900px"});
				$('a[href=#report-map]').parent().hide();
				$('a.taller-map').parent().show();
				$('a.smaller-map').parent().show();
				break;
			case "taller-map":
				$('.map-holder').css("height","600px");
				$('a.shorter-map').parent().show();
				$('a.smaller-map').parent().show();
				break;
			case "shorter-map":
				$('.map-holder').css("height","350px");
				$('a.taller-map').parent().show();
				$('a.smaller-map').parent().show();
				break;
			case "smaller-map":
				$('.report-map').hide().prependTo($('.report-media-box-content'));
				$('.map-holder').css({"height":"350px", "width": "348px"});
				$('a.wider-map').parent().show();
				$('.report-map').show();
				break;
		};
		
		map.trigger("resize");
		return false;
	});

}); // END jQuery(window).load();

jQuery(window).bind("load", function() {
	jQuery("div#slider1").codaSlider()
	// jQuery("div#slider2").codaSlider()
	// etc, etc. Beware of cross-linking difficulties if using multiple sliders on one page.
});


function rating(id,action,type,loader) {
	$('#' + loader).html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
	$.post("<?php echo url::site().'reports/rating/' ?>" + id, { action: action, type: type },
		function(data){
			if (data.status == 'saved'){
				if (type == 'original') {
					$('#oup_' + id).attr("src","<?php echo url::file_loc('img').'media/img/'; ?>gray_up.png");
					$('#odown_' + id).attr("src","<?php echo url::file_loc('img').'media/img/'; ?>gray_down.png");
					$('#orating_' + id).html(data.rating);
				}
				else if (type == 'comment')
				{
					$('#cup_' + id).attr("src","<?php echo url::file_loc('img').'media/img/'; ?>gray_up.png");
					$('#cdown_' + id).attr("src","<?php echo url::file_loc('img').'media/img/'; ?>gray_down.png");
					$('#crating_' + id).html(data.rating);
				}
			} else {
				if(typeof(data.message) != 'undefined') {
					alert(data.message);
				}
			}
			$('#' + loader).html('');
	  	}, "json");
}
		
