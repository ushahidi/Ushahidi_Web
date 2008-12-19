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

function plotGraph(catId, aStartTime, aEndTime) {	
    var startTime = new Date(aStartTime) || new Date("<?php echo $current_date; ?>");
    var endTime = new Date(aEndTime) || new Date(startTime.getFullYear() + '/'+ (startTime.getMonth()+2) + '/01');
	
	if (!catId || catId == '0') {
	    catId = 'ALL';
	}

	if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124) {   // monthly
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
        var aTimeformat = "%d %b";
        var aTickSize = [5, "day"];

        // plot hourly incidents when period is within 2 days
        if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 2) {
            aTimeformat = "%H:%M";
            aTickSize = [5, "hour"];
            url += "&i=hour";
        } else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 62) { 
            // weekly if period > 2 months
            aTimeformat = "%d %b";
            aTickSize = [5, "day"];
            url += "&i=week";
        }
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
		                     timeformat: aTimeformat,
             			     tickSize: aTickSize
		            }
		        }));
            }
        );
    }
}

var startTime = new Date("<?php echo $current_date; ?>");
var endTime = new Date(startTime.getFullYear() + '/'+ (startTime.getMonth()+2) + '/01');
plotGraph('ALL', startTime, endTime);



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
	plotGraph('ALL', startTime, endTime);
	return false;
}
