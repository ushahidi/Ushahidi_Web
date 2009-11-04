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

	<?php
		$numGraphs = count($graph_data) - 1;
	?>
	
	var graphData = [];
	var choiceContainer = [];
	var plotContainer = [];
	var overviewContainer = [];
	
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
			mode: "time",
			tickDecimals: 0
		},
		yaxis: {
			tickDecimals: 0
		},
		selection: {
			mode: "x"
		}
	};
	
	var overviewoptions = {
        legend: { 
        	show: false
        },
        lines: { 
        	show: true, 
        	lineWidth: 1 
        },
        shadowSize: 0,
        xaxis: { 
        	ticks: [], 
        	mode: "time" 
        },
        yaxis: { 
        	ticks: [], 
        	min: 0
        },
        selection: { 
        	mode: "x"
        }
    };
	
	<?php
	$i = 0;
	while($i <= $numGraphs) {
	?>
		graphData[<?=$i?>] = <?=$graph_data[$i]?>;
		choiceContainer[<?=$i?>] = $("#choices<?=$i?>");
		plotContainer[<?=$i?>] = $("#plotarea<?=$i?>");
		overviewContainer[<?=$i?>] = $("#overview<?=$i?>");
	
		// hard-code color indices to prevent them from shifting as
	    // countries are turned on/off
	    if(<?php if(isset($custom_colors[$i]) && $custom_colors[$i] == 'true') { echo 'false'; }else{ echo 'true'; } ?>){
		    var i = 0;
		    $.each(graphData[<?=$i?>], function(key, val) {
		        val.color = i;
		        ++i;
		    });
	    }
	    
	    // insert checkboxes
	    $.each(graphData[<?=$i?>], function(key, val) {
	        choiceContainer[<?=$i?>].append('<br/><input type="checkbox" name="' + key +
	                               '" checked="checked" >' + val.label + '</input>');
	    });
	    choiceContainer[<?=$i?>].find("input").click(plotAccordingToChoices<?=$i?>);
		
		var plot<?=$i?> = $.plot( plotContainer[<?=$i?>], graphData[<?=$i?>], options );
		var overview<?=$i?> = $.plot(overviewContainer[<?=$i?>], graphData[<?=$i?>], overviewoptions);
	
	    // now connect the two
	    
	    plotContainer[<?=$i?>].bind("plotselected", function (event, ranges) {
	        // do the zooming
	        plot<?=$i?> = $.plot(plotContainer[<?=$i?>], graphData[<?=$i?>],
	                      $.extend(true, {}, options, {
	                          xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
	                      }));
	
	        // don't fire event on the overview to prevent eternal loop
	        overview<?=$i?>.setSelection(ranges, true);
	    });
	    
	    overviewContainer[<?=$i?>].bind("plotselected", function (event, ranges) {
	        plot<?=$i?>.setSelection(ranges);
	    });
	    
	    function plotAccordingToChoices<?=$i?>() {
	        var data = [];
	
	        choiceContainer[<?=$i?>].find("input:checked").each(function () {
	            var key = $(this).attr("name");
	            if (key && graphData[<?=$i?>][key])
	                data.push(graphData[<?=$i?>][key]);
	        });
	
	        if (data.length > 0) {
	            $.plot(plotContainer[<?=$i?>], data, options);
	            $.plot(overviewContainer[<?=$i?>], data, overviewoptions);
	        }
	    }
	
	    plotAccordingToChoices<?=$i?>();

	<?php
		$i++;
	}
	?>

});