<?php
/**
 * Sharing_bar js file.
 * 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Sharing Module
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

			// Sharing Layer[s] Switch Action
			$("a[id^='share_']").click(function()
			{
				var shareID = this.id.substring(6);
				
				if ( $("#share_" + shareID).hasClass("active") )
				{
					share_layer = map.getLayersByName("Share_"+shareID);
					if (share_layer)
					{
						for (var i = 0; i <?php echo '<'; ?> share_layer.length; i++)
						{
							map.removeLayer(share_layer[i]);
						}
					}
					$("#share_" + shareID).removeClass("active");
					
				} 
				else
				{
					$("#share_" + shareID).addClass("active");
					
					// Get Current Zoom
					currZoom = map.getZoom();

					// Get Current Center
					currCenter = map.getCenter();
					
					// Add New Layer
					addMarkers('', '', '', currZoom, currCenter, '', shareID, 'shares');
				}
			});

