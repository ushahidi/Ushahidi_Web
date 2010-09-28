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