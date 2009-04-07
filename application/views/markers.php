<?php 
/**
 * Markers.
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

<?php
header ("Content-Type:text/xml");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<kml xmlns=\"http://www.opengis.net/kml/2.2\">\n";
echo "<Document>\n";
echo "<name>Markers.kml</name>\n";
echo "<open>1</open>";
echo $style_map;
echo $placemarks;
echo "</Document>\n";
echo "</kml>\n";
?>