
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
		this.elementId = 'graph';
		this.categoryId = '0';
		this.startTime = null; //new Date(new Date().getFullYear() + '/01/01');
		this.endTime = null; //new Date(this.startTime.getFullYear() + '/12/31');
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
		this.playCount = 0;
		this.addMarkers = null;
		if (typeof(gAddMarkers) != 'undefined') {
			this.addMarkers = gAddMarkers;
		}
	    
		if (options) {
			if (options.categoryId == '0') {
				options.categoryId = '0';
			}
			$.extend(this, options);
			if (!isNaN(this.categoryId)) {
				this.categoryId = gCategoryId;
			}
		}
	    
		this.plot = function() {
			gStartTime    = this.startTime;
			gEndTime      = this.endTime;
			gCategoryId   = this.categoryId;
			gGraphOptions = this.graphOptions;
			gTimelineId   = this.elementId;
			
			$.extend(this.graphOptions.xaxis, this.getXaxisOptions());
	    	
			if (!this.url) { 
				gStartTime = gStartTime || new Date(this.graphData[0][0]);
				timeCount  = this.graphData.length-1;
				gEndTime   = gEndTime || new Date(this.graphData[timeCount][0]);
				var customOptions = {};
				if (gStartTime && gEndTime) {
					customOptions = {xaxis: { min: gStartTime.getTime(), 
				                              max: gEndTime.getTime() 
				                            }};
				}
				plot = $.plot($("#"+this.elementId), [this.graphData],
				        $.extend(true, {}, this.graphOptions, customOptions));
	        } else {   
				var startDate = '';
				var endDate = ''; 
				
				if (this.startTime) {
					startDate = this.startTime.getFullYear() + '-' + 
				                (this.startTime.getMonth()+1) + '-'+ this.startTime.getDate();
				}
				if (this.endTime) {
					endDate = this.endTime.getFullYear() + '-' + 
				                (this.endTime.getMonth()+1) + '-'+ this.endTime.getDate();
				}
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
				        gTimelineData = data;
				        //plotPeriod = $.timelinePeriod(gTimelineData[0].data);
				        gStartTime = gStartTime // || new Date(plotPeriod[0]);
				        gEndTime   = gEndTime // || new Date(plotPeriod[1]);
				        if (!gTimelineData[gCategoryId]) {
				            gTimelineData[gCategoryId] = {};
				            gTimelineData[gCategoryId]['data'] = [];
				        }
				        plot = $.plot($("#"+gTimelineId), 
				            [gTimelineData[gCategoryId]],
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
		
		this.resetPlay = function() {
			this.playCount = 0;
			return this;
		};
		
		this.pause = function() {
			window.clearTimeout(gTimelinePlayHandle);
			gTimelinePlayHandle = null;
			$('#playTimeline').html('PLAY');
			$('#playTimeline').parent().attr('class', 'play');
			return this;
		};
		
		this.resume = function() {
			this.play();
		};
		
		this.playOrPause = function() {
			if (this.playCount == 0 || this.playCount == this.filteredData().length) {
				this.resetPlay().play();
			} else if (typeof(gTimelinePlayHandle) != 'undefined' && gTimelinePlayHandle) {
				this.pause();
			} else {
				this.resume();
			}
		};
		
		this.filteredData = function() {
			// Uncomment to play at default intervals
			//return $.grep(this.graphData.data, function(n,i) {
			return $.grep(dailyGraphData[0][this.categoryId].data, function(n,i) {
				return (n[0] >= gStartTime.getTime() && n[0] <= gEndTime.getTime());
			});
		};
		
		this.play = function() {
			this.graphData = this.graphData || gTimelineData;
			var plotData = this.graphData;
			var data = this.filteredData();
			
			if (this.playCount >= data.length) {
				return this;
			}
			
			playTimeline = $.timeline({graphData: {color: plotData.color, 
			                                       data: data.slice(0,this.playCount+1)}, 
			            categoryId: this.categoryId,                           
			            startTime: gStartTime, //new Date(plotData.data[0][0]),
			            endTime: gEndTime //new Date(plotData.data[plotData.data.length-1][0])
			           });
			playTimeline.plot();
			//gStartTime = new Date(plotData.data[0][0]);
			gPlayEndDate = playTimeline.graphData.data[playTimeline.graphData.data.length-1][0] / 1000;
			playTimeline.plotMarkers(style, markers, gPlayEndDate);
			this.playCount++;
			if (this.playCount == data.length) {
				$('#playTimeline').html('PLAY');
				$('#playTimeline').parent().attr('class', 'play');
				this.graphData = allGraphData[0][gCategoryId];
			} else {
				$('#playTimeline').html('PAUSE');
				$('#playTimeline').parent().attr('class', 'play pause');
				gTimeline = this;
				gTimelinePlayHandle = window.setTimeout("gTimeline.play()",1000);
			}
			
			return this;
		};
		
		this.plotMarkers = function(style, markers, endDate) {
			var startDate = this.startTime.getTime() / 1000;
			endDate = endDate || this.endTime.getTime() / 1000;

			// Uncomment to play at monthly intervals
			//endDate = $.monthEndTime(endDate * 1000) / 1000;
			endDate = $.dayEndDateTime(endDate * 1000) / 1000;

			// plot markers using a custom addMarkers method if available
			if (this.addMarkers) {	
				this.addMarkers(gCategoryId, '', endDate, gMap.getZoom(), gMap.getCenter(), gMediaType);
				return this;
			}
			
			var sliderfilter = new OpenLayers.Rule({
				filter: new OpenLayers.Filter.Comparison(
				{
					type: OpenLayers.Filter.Comparison.BETWEEN,
					property: "timestamp",
					lowerBoundary: startDate,
					upperBoundary: endDate
				})
			});
			style.rules = [];
			style.addRules(sliderfilter);					
			markers.styleMap.styles["default"] = style; 
			markers.redraw();
			return this;
		};
		
		/*
		 * Returns options to plot incidents hourly, daily, weekly or monthly
		 * according to the period
		 */
		this.getXaxisOptions = function() {
			var startTime = this.startTime; //new Date(startDate * 1000);
			var endTime = this.endTime; //new Date(endDate * 1000);
			// daily
			var aTimeformat = "%d %b";
			var aTickSize = [5, "day"];

			// plot hourly incidents when period is within 2 days
			if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 2) {
			    aTimeformat = "%H:%M";
			    aTickSize = [5, "hour"];
			} else if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 124) { 
			    // weekly if period > 2 months
			    aTimeformat = "%d %b";
			    aTickSize = [5, "day"];
			} else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124) {
				// monthly if period > 4 months
			    aTimeformat = "%b %y";
			    aTickSize = [2, "month"];
			}
			
			return {
				mode: "time", 
				timeformat: aTimeformat,
				tickSize: aTickSize, 
				autoscaleMargin: 3
			};
		};
	}  

	$.timeline = function(options) {
		timeline = new Timeline(options);
		return timeline;
	}
	
	$.timelinePeriod = function(plotData) {
		var days = $.timelineDays(plotData);
		if (days < 365) {
			startTime = plotData[0][0];
			endTime = plotData[plotData.length-1][0];
		} else {
			heatLevel = 0;
			hottestMoment = null;
			for (var i=0; i<plotData.length; i++) {
				if (plotData[i][1] > heatLevel) {
					hottestMoment = plotData[i][0];
					heatLevel = plotData[i][1];
				}
			}
			startTime = hottestMoment - (6 * 30 * 24 * 60 * 60 * 1000);
			endTime   = hottestMoment + (6 * 30 * 24 * 60 * 60 * 1000);
		}
		return [startTime, endTime];
	};

	/*
	 * Returns number of days in the given plot data
	 */
	$.timelineDays = function(plotData) {
		var days = 0;
		if (plotData) {
			var incidentCount = plotData.length;
			var startDate = new Date(plotData[0][0]);
			var endDate = new Date(plotData[incidentCount-1][0]);
			days = (endDate - startDate)/(1000 * 60 * 60 * 24);
		}
		return days;
	};
	
	/*
	 * Returns number of days in given month. Jan is given as 0, Dec as 11
	 */
	$.monthDays = function(year, month) {
		days = [31,28,31,30,31,30,31,31,30,31,30,31];
		daysInMonth = days[month];
		if ((year % 4) == 0 && month == 1) daysInMonth++;
		return daysInMonth;
	};
	
	/*
	 * Returns timestamp of the first day of Month of the given timestamp
	 */
	$.monthStartTime = function(timestamp) {
		var startDate = new Date(timestamp);
		startDate.setDate(1);
		startDate.setHours(0);
		startDate.setMinutes(0);
		startDate.setSeconds(0);
		return startDate.getTime();
	};
	
	/*
	 * Returns timestamp of the last day of month of the given timestamp
	 */
	$.monthEndDateTime = function(timestamp) {
		endDate = new Date(timestamp);
		endDate.setDate($.monthDays(endDate.getYear(), endDate.getMonth()));
		endDate.setHours(0);
		endDate.setMinutes(0);
		endDate.setSeconds(0);
		return endDate.getTime();	
	};
	
	/*
	 * Returns timestamp of the last hour of day of the given timestamp
	 */
	$.dayEndDateTime = function(timestamp) {
		endDate = new Date(timestamp);
		//endDate.setDate($.monthDays(endDate.getYear(), endDate.getMonth()));
		endDate.setHours(23);
		endDate.setMinutes(59);
		endDate.setSeconds(59);
		return endDate.getTime();	
	};
	
	/*
	 * Returns timestamp of the last second in the month of given timestamp
	 */
	$.monthEndTime = function(timestamp) {
		return $.monthEndDateTime(timestamp) + (59*1000)+(59*60*1000)+(23*60*60*1000);
	};
		
})(jQuery);
