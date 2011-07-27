
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
			xaxis: { 
				mode: "time",
				timeformat: "%b %y",
				autoscaleMargin: 3
			},
			yaxis: { 
				tickDecimals: 0
			},
			points: { 
				show: true
			},
			lines: { 
				show: true
			},
			legend: { 
				show: false
			},
			grid: {
				color: "#999999"
			}
		};

		this.markerOptions = null;

		this.graphData = [];
		this.playCount = 0;
		this.addMarkers = null;
	    
		if (options)
		{
			if (options.categoryId == '0')
			{
				options.categoryId = '0';
			}
			var defaultGraphOptions = this.graphOptions;
			$.extend(this, options);
			$.extend(true, this.graphOptions, defaultGraphOptions);
			if (!isNaN(this.categoryId))
			{
				this.categoryId = gCategoryId;
			}
		}
		
		this.plot = function()
		{
			gStartTime    = this.startTime;
			gEndTime      = this.endTime;
			gCategoryId   = this.categoryId;
			gGraphOptions = this.graphOptions;
			gTimelineId   = this.elementId;
			
			$.extend(this.graphOptions.xaxis, this.getXaxisOptions());
	    	
			if (!this.url)
			{
				gStartTime = gStartTime || new Date(this.graphData[0][0]);
				timeCount  = this.graphData.length-1;
				gEndTime   = gEndTime || new Date(this.graphData[timeCount][0]);
				var customOptions = {};
				if (gStartTime && gEndTime)
				{
					customOptions = {
						xaxis: {
							min: gStartTime.getTime(),
							max: gEndTime.getTime()
						}
					};
				}
				plot = $.plot($("#"+this.elementId), [this.graphData],
					$.extend(true, {}, this.graphOptions, customOptions));
			}
			else
			{
				var startDate = '';
				var endDate = ''; 
				
				if (this.startTime)
				{
					startDate = this.startTime.getFullYear() + '-' + 
					(this.startTime.getMonth()+1) + '-'+ this.startTime.getDate();
				}
				if (this.endTime)
				{
					endDate = this.endTime.getFullYear() + '-' + 
					(this.endTime.getMonth()+1) + '-'+ this.endTime.getDate();
				}
				this.url += "?s=" + startDate + "&e=" + endDate;

				// daily
				var aTimeformat = "%d %b";
				var aTickSize = [5, "day"];

				// plot hourly incidents when period is within 2 days
				if ((this.endTime - this.startTime) / (1000 * 60 * 60 * 24) <= 2)
				{
					aTimeformat = "%H:%M";
					aTickSize = [5, "hour"];
					this.url += "&i=hour";
				}
				else if ((this.endTime - this.startTime) / (1000 * 60 * 60 * 24) <= 124)
				{
					// weekly if period > 2 months
					aTimeformat = "%d %b";
					aTickSize = [5, "day"];
					this.url += "&i=week";
				} 
				else if ((this.endTime - this.startTime) / (1000 * 60 * 60 * 24) > 124)
				{
					// monthly if period > 4 months
					aTimeformat = "%d %b";
					aTickSize = [2, "month"];
					this.url += "&i=month";
				}

				if (this.active == 'all')
				{
					this.url += '&active=all';
				} 
				else if (this.active == 'false')
				{
					this.url += '&active=false';
				}
				if (this.mediaType)
				{
					this.url += '&m='+this.mediaType;
				}
				
				$.getJSON(this.url,
					function(data)
					{
						gTimelineData = data;
						//plotPeriod = $.timelinePeriod(gTimelineData[0].data);
						gStartTime = gStartTime; // || new Date(plotPeriod[0]);
						gEndTime   = gEndTime; // || new Date(plotPeriod[1]);
						if (!gTimelineData[gCategoryId])
						{
							gTimelineData[gCategoryId] = {};
							gTimelineData[gCategoryId]['data'] = [];
						}
						plot = $.plot($("#"+gTimelineId),
							[gTimelineData[gCategoryId]],
							$.extend(true, {}, gGraphOptions, {
								xaxis: {
									min: gStartTime.getTime(),
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
		
		this.resetPlay = function()
		{
			this.playCount = 0;
			return this;
		};
		
		this.pause = function()
		{
			window.clearTimeout(gTimelinePlayHandle);
			gTimelinePlayHandle = null;
			$('#playTimeline').html('PLAY');
			$('#playTimeline').parent().attr('class', 'play');
			return this;
		};
		
		this.resume = function(visualType)
		{
			this.play(visualType);
		};
		
		this.playOrPause = function(visualType)
		{
			if (typeof(visualType) == 'undefined') 
			{	
				visualType = 'default';
			}
			
			if (this.playCount == 0 || gPlayEndDate >= this.endTime.getTime()/1000)
			{
				this.resetPlay().play(visualType);
			}
			else if (typeof(gTimelinePlayHandle) != 'undefined' && gTimelinePlayHandle)
			{
				this.pause();
			} 
			else
			{
				this.resume(visualType);
			}
		};
		
		this.filteredData = function(endTime)
		{
			// Uncomment to play at default intervals
			//return $.grep(this.graphData.data, function(n,i) {
			if (typeof(endTime) == 'undefined')
			{
				endTime = gEndTime;
			}
			return $.grep(dailyGraphData.data, function(n,i) {
				return (n[0] >= gStartTime.getTime() && n[0] <= endTime.getTime());
			});
		};
		
		this.play = function(visualType)
		{
			if (typeof(visualType) == 'undefined')
			{
				visualType = 'default';
			}
			else if (visualType == 'raindrops')
			{
				return this.playRainDrops();
			}
			
			this.graphData = this.graphData || gTimelineData;
			var plotData = this.graphData;
			var data = this.filteredData();
			
			if (this.playCount >= data.length)
			{
				return this;
			}
			
			playTimeline = $.timeline({
				graphData: {
					color: plotData.color,
					data: data.slice(0,this.playCount+1)
				},
				markerOptions: this.markerOptions,
				categoryId: this.categoryId,
				startTime: gStartTime, //new Date(plotData.data[0][0]),
				endTime: gEndTime //new Date(plotData.data[plotData.data.length-1][0])
			});
			playTimeline.plot();
			gPlayEndDate = playTimeline.graphData.data[playTimeline.graphData.data.length-1][0] / 1000;
			playTimeline.plotMarkers(style, markers, gPlayEndDate);
			this.playCount++;
			if (this.playCount == data.length)
			{
				$('#playTimeline').html('PLAY');
				$('#playTimeline').parent().attr('class', 'play');
				this.graphData = allGraphData;
			} 
			else
			{
				$('#playTimeline').html('PAUSE');
				$('#playTimeline').parent().attr('class', 'play pause');
				gTimeline = this;
				gTimelinePlayHandle = window.setTimeout("gTimeline.play('"+visualType+"')",500);
			}
			
			return this;
		};

		this.playRainDrops = function()
		{
			this.graphData = this.graphData || gTimelineData;

			var plotData = this.graphData;
			gPlayEndDate = gStartTime.getTime()/1000 + (this.playCount * 60*60*24);
			gPlayStartDate = gPlayEndDate - (60*60*24);
			var playEndDateTime = new Date(gPlayEndDate * 1000);
			var data = this.filteredData(new Date(gPlayEndDate * 1000));

			var playOptions = {
				graphData: {
					color: plotData.color,
					data: data
				},
				graphOptions: {
					grid: {
						markings: [{
							xaxis: {
								from: playEndDateTime.getTime(),
								to: playEndDateTime.getTime()
							},
							color: "#222222"
						}]
					}
				},
				markerOptions: this.markerOptions,
				categoryId: this.categoryId,
				startTime: gStartTime,
				endTime: gEndTime
			};

			playTimeline = $.timeline(playOptions);
			var style = playTimeline.markerStyle();
			var markers = gTimelineMarkers;
			playTimeline.plot();
			playTimeline.plotMarkers(style, markers, gPlayStartDate, gPlayEndDate);
			this.playCount++;
			if (gPlayEndDate >= gEndTime.getTime()/1000)
			{
				$('#playTimeline').html('PLAY');
				$('#playTimeline').parent().attr('class', 'play');
				this.graphData = allGraphData;
			}
			else
			{
				$('#playTimeline').html('PAUSE');
				$('#playTimeline').parent().attr('class', 'play pause');
				gTimeline = this;
				gTimelinePlayHandle = window.setTimeout("gTimeline.playRainDrops()",800);
			}

			return this;
		};
		
		this.plotMarkers = function(style, markers, startDate, endDate)
		{
			//var startDate = this.startTime.getTime() / 1000;
			endDate = endDate || this.endTime.getTime() / 1000;

			// Uncomment to play at monthly intervals
			//endDate = $.monthEndTime(endDate * 1000) / 1000;
			endDate = $.dayEndDateTime(endDate * 1000) / 1000;

			/*
			// plot markers using a custom addMarkers method if available
			if (this.addMarkers && !this.playCount > 0) {
				this.addMarkers(gCategoryId, '', endDate, gMap.getZoom(), gMap.getCenter(), gMediaType);
				return this;
			}
			*/

			var sliderfilter = new OpenLayers.Rule({
				filter: new OpenLayers.Filter.Comparison(
				{
					type: OpenLayers.Filter.Comparison.BETWEEN,
					property: "timestamp",
					lowerBoundary: startDate,
					upperBoundary: endDate
				}),
				symbolizer: {
					fillOpacity: 1,
					strokeColor: "black"
				}
			});
			
			var sliderfilter2 = new OpenLayers.Rule({
				filter: new OpenLayers.Filter.Comparison(
				{
					type: OpenLayers.Filter.Comparison.BETWEEN,
					property: "timestamp",
					lowerBoundary: 0,
					upperBoundary: endDate
				}),
				symbolizer: {
					fillOpacity: 0.3,
					strokeColor: "white",
					strokeOpacity: 1
				}
			});
			
			style.rules = [];
			style.addRules([sliderfilter2, sliderfilter]);
			markers.styleMap.styles["default"] = style;
			markers.redraw();
			return this;
		};

		// Get url params for creating markers layer
		this.markerUrlParams = function(startDate, endDate)
		{
			// Add parameters
			params = [];
			if (typeof(this.categoryId) != 'undefined' && this.categoryId.length > 0)
			{
				params.push('c=' + this.categoryId);
			}
			if (typeof(startDate) != 'undefined')
			{
				params.push('s=' + startDate);
			}
			if (typeof(endDate) != 'undefined')
			{
				params.push('e=' + endDate);
			}
			if (typeof(this.mediaType) != 'undefined')
			{
				params.push('m=' + this.mediaType);
			}
			return params;
		};


		/*
		 * Style
		 */
		this.markerStyle = function()
		{
			// Set Feature Styles
			style = new OpenLayers.Style({
				'externalGraphic': "${icon}",
				'graphicTitle': "${cluster_count}",
				pointRadius: "${radius}",
				fillColor: "${color}",
				fillOpacity: "${opacity}",
				strokeColor: "${strokeColor}",
				strokeWidth: "${strokeWidth}",
				strokeOpacity: "0.3",
				label:"${clusterCount}",
				//labelAlign: "${labelalign}", // IE doesn't like this for some reason
				fontWeight: "${fontweight}",
				fontColor: "#ffffff",
				fontSize: "${fontsize}"
			},
			{
				context:
				{
					count: function(feature)
					{
						if (feature.attributes.count < 2)
						{
							return 2 * markerRadius;
						} 
						else if (feature.attributes.count == 2)
						{
							return (Math.min(feature.attributes.count, 7) + 1) *
							(markerRadius * 0.8);
						}
						else
						{
							return (Math.min(feature.attributes.count, 7) + 1) *
							(markerRadius * 0.6);
						}
					},
					fontsize: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!=="")
						{
							return "9px";
						}
						else
						{
							feature_count = feature.attributes.count;
							if (feature_count > 1000)
							{
								return "20px";
							}
							else if (feature_count > 500)
							{
								return "18px";
							}
							else if (feature_count > 100)
							{
								return "14px";
							}
							else if (feature_count > 10)
							{
								return "12px";
							}
							else if (feature_count >= 2)
							{
								return "10px";
							}
							else
							{
								return "";
							}
						}
					},
					fontweight: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!=="")
						{
							return "normal";
						}
						else
						{
							return "bold";
						}
					},
					radius: function(feature)
					{
						feature_count = feature.attributes.count;
						if (feature_count > 10000)
						{
							return markerRadius * 17;
						}
						else if (feature_count > 5000)
						{
							return markerRadius * 10;
						}
						else if (feature_count > 1000)
						{
							return markerRadius * 8;
						}
						else if (feature_count > 500)
						{
							return markerRadius * 7;
						}
						else if (feature_count > 100)
						{
							return markerRadius * 6;
						}
						else if (feature_count > 10)
						{
							return markerRadius * 5;
						}
						else if (feature_count >= 2)
						{
							return markerRadius * 3;
						}
						else
						{
							return markerRadius * 2;
						}
					},
					strokeWidth: function(feature)
					{
						if ( typeof(feature.attributes.strokewidth) != 'undefined' && 
							feature.attributes.strokewidth != '')
						{
							return feature.attributes.strokewidth;
						}
						else
						{
							feature_count = feature.attributes.count;
							if (feature_count > 10000)
							{
								return 45;
							}
							else if (feature_count > 5000)
							{
								return 30;
							}
							else if (feature_count > 1000)
							{
								return 22;
							}
							else if (feature_count > 100)
							{
								return 15;
							}
							else if (feature_count > 10)
							{
								return 10;
							}
							else if (feature_count >= 2)
							{
								return 5;
							}
							else
							{
								return 1;
							}
						}
					},
					color: function(feature)
					{
						return "#" + feature.attributes.color;
					},
					strokeColor: function(feature)
					{
						if ( typeof(feature.attributes.strokecolor) != 'undefined' && 
							feature.attributes.strokecolor != '')
						{
							return "#"+feature.attributes.strokecolor;
						}
						else
						{
							return "#"+feature.attributes.color;
						}
					},
					icon: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!=="")
						{
							return baseUrl + feature_icon;
						} 
						else
						{
							return "";
						}
					},
					clusterCount: function(feature)
					{
						if (feature.attributes.count > 1)
						{
							if($.browser.msie && $.browser.version=="6.0")
							{ // IE6 Bug with Labels
								return "";
							}
							
							feature_icon = feature.attributes.icon;
							if (feature_icon!=="")
							{
								return "> " + feature.attributes.count;
							} 
							else
							{
								return feature.attributes.count;
							}
						}
						else
						{
							return "";
						}
					},
					opacity: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!=="")
						{
							return "1";
						}
						else
						{
							return markerOpacity;
						}
					},
					labelalign: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!=="")
						{
							return "c";
						}
						else
						{
							return "c";
						}
					}
				}
			});
			return style;
		};

		/*
		Create the Markers Layer
		*/
		this.addMarkers = function(startDate,endDate, currZoom, currCenter,
			thisLayerID, thisLayerType, thisLayerUrl, thisLayerColor, json_url)
		{

			var	protocolUrl = baseUrl + json_url + "/"; // Default Json
			var thisLayer = "Reports"; // Default Layer Name
			var protocolFormat = new OpenLayers.Format.GeoJSON();
			newlayer = false;

			if (thisLayer && thisLayerType == 'shares')
			{
				protocolUrl = baseUrl + "json/share/"+thisLayerID+"/";
				thisLayer = "Share_"+thisLayerID;
				newlayer = true;
			} 
			else if (thisLayer && thisLayerType == 'layers')
			{
				protocolUrl = baseUrl + "json/layer/"+thisLayerID+"/";
				thisLayer = "Layer_"+thisLayerID;
				protocolFormat = new OpenLayers.Format.KML({
					extractStyles: true,
					extractAttributes: true,
					maxDepth: 5
				});
				newlayer = true;
			}

			var myPoint;
			if (currZoom && currCenter && typeof(currZoom) != 'undefined' && typeof(currCenter) != 'undefined')
			{
				myPoint = currCenter;
				myZoom = currZoom;
			}
			else
			{
				// Create a lat/lon object
				myPoint = new OpenLayers.LonLat(longitude, latitude);
				myPoint.transform(proj_4326, map.getProjectionObject());

				// Display the map centered on a latitude and longitude (Google zoom levels)
				myZoom = defaultZoom;
			}

			if (mapLoad == 0)
			{
				map.setCenter(myPoint, myZoom, false, false);
			}
			mapLoad = mapLoad+1;

			// Get Viewport Boundaries
			extent = map.getExtent().transform(map.getProjectionObject(),
				new OpenLayers.Projection("EPSG:4326"));
			southwest = extent.bottom+','+extent.left;
			northeast = extent.top+','+extent.right;

			var style = this.markerStyle();

			// Transform feature point coordinate to Spherical Mercator
			preFeatureInsert = function(feature)
			{
				var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
				OpenLayers.Projection.transform(point, proj_4326, proj_900913);
			};

			// Does 'markers' already exist? If so, destroy it before creating new layer
			markers = map.getLayersByName(thisLayer);
			if (markers && markers.length > 0)
			{
				for (var i = 0; i < markers.length; i++)
				{
					map.removeLayer(markers[i]);
				}
			}
			
			// Build the URL for fetching the data
			fetchUrl = (thisLayer && thisLayerType == 'layers')
				? protocolUrl
				: protocolUrl + '?z=' + myZoom + '&' + this.markerUrlParams(startDate, endDate).join('&');
			
			// Create the reports layer
			markers = new OpenLayers.Layer.Vector(thisLayer, {
				preFeatureInsert:preFeatureInsert,
				projection: proj_4326,
				formatOptions: {
					extractStyles: true,
					extractAttributes: true
				},
				styleMap: new OpenLayers.StyleMap({
					"default":style,
					"select": style
				}),
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: fetchUrl,
					format: protocolFormat
				})
				
			});
			
			// Add the layer to the map
			map.addLayer(markers);
			
			/*
			 - Added by E.Kala <emmanuel(at)ushahidi.com>
			 - Part of the fix to issue #2168
			*/
			
			// Check if the the new layer is a KML layer
			if (thisLayer && thisLayerType == 'layers')
			{
				// Add layer object to the kmlOvelays array
				kmlOverlays.push(markers);
			}
			
			selectControl = new OpenLayers.Control.SelectFeature(markers);
			map.addControl(selectControl);
			selectControl.activate();
			markers.events.on({
				"featureselected": onFeatureSelect,
				"featureunselected": onFeatureUnselect
			});
			
			return markers;
		};
		
		/*
		 * Returns options to plot incidents hourly, daily, weekly or monthly
		 * according to the period
		 */
		this.getXaxisOptions = function()
		{
			var startTime = this.startTime; //new Date(startDate * 1000);
			var endTime = this.endTime; //new Date(endDate * 1000);
			// daily
			var aTimeformat = "%d %b";
			var aTickSize = [5, "day"];

			// plot hourly incidents when period is within 2 days
			if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 2)
			{
				aTimeformat = "%H:%M";
				aTickSize = [5, "hour"];
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 124)
			{
				// weekly if period > 2 months
				aTimeformat = "%d %b";
				aTickSize = [5, "day"];
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124)
			{
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

	$.timeline = function(options)
	{
		timeline = new Timeline(options);
		return timeline;
	};
	
	$.timelinePeriod = function(plotData)
	{
		var days = $.timelineDays(plotData);
		if (days < 365)
		{
			startTime = plotData[0][0];
			endTime = plotData[plotData.length-1][0];
		} 
		else
		{
			heatLevel = 0;
			hottestMoment = null;
			for (var i=0; i<plotData.length; i++)
			{
				if (plotData[i][1] > heatLevel)
				{
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
	$.timelineDays = function(plotData)
	{
		var days = 0;
		if (plotData)
		{
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
	$.monthDays = function(year, month)
	{
		days = [31,28,31,30,31,30,31,31,30,31,30,31];
		daysInMonth = days[month];
		if ((year % 4) == 0 && month == 1)
		{
			daysInMonth++;
		}
		return daysInMonth;
	};
	
	/*
	 * Returns timestamp of the first day of Month of the given timestamp
	 */
	$.monthStartTime = function(timestamp)
	{
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
	$.monthEndDateTime = function(timestamp)
	{
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
	$.dayEndDateTime = function(timestamp)
	{
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
	$.monthEndTime = function(timestamp)
	{
		return $.monthEndDateTime(timestamp) + (59*1000)+(59*60*1000)+(23*60*60*1000);
	};
		
})(jQuery);
