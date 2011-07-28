/**
 * Dashboard js file.
 *
 * Handles javascript stuff related to dashboard function.
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

// Graph
var allGraphData = [<?php echo $all_graphs ?>];

$(document).ready(function() {
	var startTime = new Date("<?php echo $current_date; ?>");
	var endTime = new Date(startTime.getFullYear() + '/'+ (startTime.getMonth()+2) + '/01');
	var timelineOptions = {startTime: startTime, 	endTime: endTime, 
	                       categoryId: 'ALL', graphData: allGraphData[0]['ALL'], active: 'all',
	                       url: "<?php echo url::base() . 'json/timeline/' ?>"
	};
	$.timeline(timelineOptions).plot();
});


function graphSwitch(timeframe)
{
    var startTime;
    var endTime;

	$('#graph').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
	if (timeframe == 'day') {
		startTime = new Date("<?php echo date('Y/m/d'); ?>");
		endTime = new Date("<?php echo date('Y/m/d 23:59:59'); ?>");
	} else if (timeframe == 'year') {
		startTime = new Date("<?php echo date('Y/01/01'); ?>");
		endTime = new Date("<?php echo date('Y/12/t 23:59:59'); ?>");
	} else if (timeframe == 'month') {
		startTime = new Date("<?php echo date('Y/m/01'); ?>");
		endTime = new Date("<?php echo date('Y/m/t 23:59:59'); ?>");
	}
	$.timeline({categoryId: 'ALL', startTime: startTime, endTime: endTime,
	            graphData: allGraphData[0]['ALL'], active: 'all',
	            url: "<?php echo url::base() . 'json/timeline/' ?>"
	}).plot();
	return false;
}
