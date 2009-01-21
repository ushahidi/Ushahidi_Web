/*
 * Dashboard Javascript
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

	$('#graph').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
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
