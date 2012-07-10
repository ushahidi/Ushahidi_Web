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

<script type="text/javascript">
$(document).ready(function() {
	
	// Sharing Layer[s] Switch Action
	$("a[id^='share_']").click(function() {
		var shareID = this.id.substring(6);
	
		if ( $("#share_" + shareID).hasClass("active")) {
			map.deleteLayer($("#share_" + shareID).html());
			$("#share_" + shareID).removeClass("active");
		
		}  else {
			$("#share_" + shareID).addClass("active");
			map.addLayer(Ushahidi.SHARES, {
							name: $("#share_" + shareID).html(),
							url: "json/share/index/" + shareID
						});
		}
		
		return false;
	});
});
</script>