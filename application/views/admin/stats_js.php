/**
 * Stats js file.
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
	
$(document).ready(function() {
	
	var graphData = <?php echo $all_graphs ?>;
	
	var options = {  
		legend: {  
			show: true,
			position: "ne",
			margin: 10
			},  
		points: {  
			show: true,  
			radius: 3  
			},  
		lines: {  
			show: true  
			},
		xaxis: {
			mode: "time"
		},
		selection: {
			mode: "x"
		}
	};
	
	var overviewoptions = {
        legend: { show: false },
        lines: { show: true, lineWidth: 1 },
        shadowSize: 0,
        xaxis: { ticks: [], mode: "time" },
        yaxis: { ticks: [], min: 0, max: 40 },
        selection: { mode: "x" }
    };
	
	// hard-code color indices to prevent them from shifting as
    // countries are turned on/off
    var i = 0;
    $.each(graphData, function(key, val) {
        val.color = i;
        ++i;
    });
    
    // insert checkboxes 
    var choiceContainer = $("#choices");
    $.each(graphData, function(key, val) {
        choiceContainer.append('<br/><input type="checkbox" name="' + key +
                               '" checked="checked" >' + val.label + '</input>');
    });
    choiceContainer.find("input").click(plotAccordingToChoices);
	
	var plotarea = $("#plotarea");  
	//plotarea.css("height", "250px");  
	//plotarea.css("width", "500px");
	
	var plot = $.plot( plotarea, graphData, options );
	var overview = $.plot($("#overview"), graphData, overviewoptions);

    // now connect the two
    
    $("#plotarea").bind("plotselected", function (event, ranges) {
        // do the zooming
        plot = $.plot($("#plotarea"), graphData,
                      $.extend(true, {}, options, {
                          xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                      }));

        // don't fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });
    
    $("#overview").bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
    });
    
    
    
    
    
    
    

    
    function plotAccordingToChoices() {
        var data = [];

        choiceContainer.find("input:checked").each(function () {
            var key = $(this).attr("name");
            if (key && graphData[key])
                data.push(graphData[key]);
        });

        if (data.length > 0) {
            $.plot($("#plotarea"), data, options);
            $.plot($("#overview"), data, overviewoptions);
        }
    }

    plotAccordingToChoices();

    
    
    
    


});