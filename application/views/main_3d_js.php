<?php
/**
 * Main 3d js file.
 * 
 * Handles javascript stuff related to the 3d map.
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

var ge;
	google.load("earth", "1");
	
	function init() {
		google.earth.createInstance('map3d', initCB, failureCB);
	}
	
	function initCB(instance) {
		ge = instance;
		ge.getWindow().setVisibility(true);
		
		// add a navigation control
		ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);
		
		// add some layers
		ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
		ge.getLayerRoot().enableLayerById(ge.LAYER_ROADS, true);
		
		var link = ge.createLink('');
		var href = '<?php echo url::base(); ?>api/?task=3dkml'
		link.setHref(href);
		
		var networkLink = ge.createNetworkLink('');
		networkLink.set(link, true, false); // Sets the link, refreshVisibility, and flyToView.
		
		// Create a new LookAt
		var lookAt = ge.createLookAt('');
		
		// Set the position values
		lookAt.setLatitude(<?php echo $latitude; ?>);
		lookAt.setLongitude(<?php echo $longitude; ?>);
		lookAt.setRange(30000.0); //default is 0.0
		
		// Add 45 degrees to the current tilt
		lookAt.setTilt(lookAt.getTilt() + 45.0);
		
		// Update the view in Google Earth
		ge.getView().setAbstractView(lookAt);
		
		ge.getFeatures().appendChild(networkLink);
	}
	
	function failureCB(errorCode) {
	}
	
	google.setOnLoadCallback(init);