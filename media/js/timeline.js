
/**
 * Plots a Timeline of Incidents for a specified period and category
 * 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Timeline 
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */


(function($) { // hide the namespace

	function Timeline(options) {
	    this.categoryId = 'ALL';
	    this.startTime = new Date(new Date().getFullYear() + '/01/01');
	    this.endTime = new Date(this.startTime.getFullYear() + '/12/31');
	    this.url = null;
	    this.active = 'true';
	    this.mediaType = null;
	    this.graphOptions = {
			xaxis: { mode: "time", timeformat: "%b %y", autoscaleMargin: 3 },
			yaxis: { tickDecimals: 0 },
			points: { show: true},
			lines: { show: true}, 
			legend: { show: false},
			grid: {
			    color: "#999999"
			}
		};
		this.graphData = [];
	    
		if (options) {
			if (options.categoryId == '0') {
				options.categoryId = 'ALL';
			}
			$.extend(this, options);
			if (!isNaN(this.categoryId)) {
				this.categoryId = gCategoryId;
			}
		}
	    
		this.plot = function() {
			gStartTime = this.startTime;
			gEndTime = this.endTime;
			gCategoryId = this.categoryId;
			gGraphOptions = this.graphOptions;
	    	
			if (!this.url) { 
				plot = $.plot($("#graph"), [this.graphData],
				        $.extend(true, {}, this.graphOptions, {
				            xaxis: { min: this.startTime.getTime(), 
				                     max: this.endTime.getTime() 
				            }
				}));
	        } else {   
				
				var startDate = this.startTime.getFullYear() + '-' + 
				                (this.startTime.getMonth()+1) + '-'+ this.startTime.getDate();
				var endDate = this.endTime.getFullYear() + '-' + 
				                (this.endTime.getMonth()+1) + '-'+ this.endTime.getDate();
				this.url += "?s=" + startDate + "&e=" + endDate;

				// daily
				var aTimeformat = "%d %b";
				var aTickSize = [5, "day"];

				// plot hourly incidents when period is within 2 days
				if ((this.endTime - this.startTime) / (1000 * 60 * 60 * 24) <= 2) {
				    aTimeformat = "%H:%M";
				    aTickSize = [5, "hour"];
				    this.url += "&i=hour";
				} else if ((this.endTime - this.startTime) / (1000 * 60 * 60 * 24) <= 124) { 
				    // weekly if period > 2 months
				    aTimeformat = "%d %b";
				    aTickSize = [5, "day"];
				    this.url += "&i=week";
				} else if ((this.endTime - this.startTime) / (1000 * 60 * 60 * 24) > 124) {
					// monthly if period > 4 months
				    aTimeformat = "%d %b";
				    aTickSize = [2, "month"];
				    this.url += "&i=month";
				}

				if (this.active == 'all') {
					this.url += '&active=all';
				} else if (this.active == 'false') {
					this.url += '&active=false';
				}
				if (this.mediaType) {
					this.url += '&m='+this.mediaType;
				}
				
				$.getJSON(this.url,
				    function(data) {
				        dailyGraphData = data;
				        if (!dailyGraphData[gCategoryId]) {
				            dailyGraphData[gCategoryId] = {};
				            dailyGraphData[gCategoryId]['data'] = [];
				        }	
				        plot = $.plot($("#graph"), [dailyGraphData[gCategoryId]],
				         	$.extend(true, {}, gGraphOptions, {
				            	xaxis: { min: gStartTime.getTime(), 
				                     max: gEndTime.getTime(),
				                     mode: "time", 
				                     timeformat: aTimeformat,
				        			     tickSize: aTickSize
				            	}
				        	})
				    	);
				    }
				);
			}
		};
	}  

	$.timeline = function(options) {
		timeline = new Timeline(options);
		return timeline;
	}

})(jQuery);
