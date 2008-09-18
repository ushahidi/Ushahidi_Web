<?php
header ("Content-Type:text/xml");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<kml xmlns="http://www.opengis.net/kml/2.2">';
echo $placemarks;
echo '</kml>';
?>