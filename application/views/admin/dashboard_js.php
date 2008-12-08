/*
 * Dashboard Javascript
 */

// Graph
// TODO: Re-use this code
var allGraphData = [<?php echo $all_graphs ?>];
var graphData = allGraphData[0]['ALL'];
var dailyGraphData = {};
var graphOptions = {
	xaxis: { mode: "time", timeformat: "%b %y", autoscaleMargin: 3 },
	yaxis: { tickDecimals: 0 },
	points: { show: true},
	bars: { show: true},
	legend: { show: false},
	grid: {
	    color: "#999999"
	}
};

var startTime;
var endTime;

function plotGraph(catId) {
	
	if (!catId || catId == '0') {
	    catId = 'ALL';
	}

	if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 62) {   // monthly
	    if (!graphData) { 
	        graphData = {'data': []};
	    }
		plot = $.plot($("#graph"), [graphData],
		        $.extend(true, {}, graphOptions, {
		            xaxis: { min: startTime.getTime(), max: endTime.getTime() }
		        }));
    } else {   // daily
        var url = "<?php echo url::base() . 'json/timeline/' ?>";
        var startDate = startTime.getFullYear() + '-' + 
                        (startTime.getMonth()+1) + '-'+ startTime.getDate();
        var endDate = endTime.getFullYear() + '-' + 
                        (endTime.getMonth()+1) + '-'+ endTime.getDate();
        url += "?s=" + startDate + "&e=" + endDate;
        $.getJSON(url,
            function(data) {
                dailyGraphData = data;
                if (!dailyGraphData[catId]) { 
                    dailyGraphData[catId] = {};
                    dailyGraphData[catId]['data'] = [];
                }
                plot = $.plot($("#graph"), [dailyGraphData[catId]],
		        $.extend(true, {}, graphOptions, {
		            xaxis: { min: startTime.getTime(), 
		                     max: endTime.getTime(),
		                     mode: "time", 
		                     timeformat: "%d %b",
		                     tickSize: [5, "day"]
		            }
		        }));
            }
        );
    }
}

startTime = new Date("<?php echo $current_date; ?>");
endTime = new Date(startTime.getFullYear() + '/'+ (startTime.getMonth()+2) + '/01');
endTime = new Date(endTime - 1);
plotGraph();



function graphSwitch(timeframe)
{
	$('#graph').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
	if (timeframe == 'day') {
		startTime = new Date("<?php echo date('Y') . '/' . date('m') . '/' . date('d'); ?>");
		endTime = new Date("<?php echo date('Y') . '/' . date('m') . '/' . date('d'); ?>");
	} else if (timeframe == 'year') {
		startTime = new Date("<?php echo date('Y') . '/01/01'; ?>");
		endTime = new Date("<?php echo date('Y') . '/12/' . date('t'); ?>");
	} else if (timeframe == 'month') {
		startTime = new Date("<?php echo date('Y') . '/' . date('m') . '/01'; ?>");
		endTime = new Date("<?php echo date('Y') . '/' . date('m') . '/' . date('t'); ?>");
	}
	plotGraph();
	return false;
}