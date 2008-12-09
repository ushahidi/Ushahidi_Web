/*
 * --------------------------------------------------------------------
 * jQuery-Plugin - accessibleUISlider - creates a UI slider component from a select element(s)
 * by Scott Jehl, scott@filamentgroup.com
 * http://www.filamentgroup.com
 * reference article: http://www.filamentgroup.com/lab/progressive_enhancement_convert_select_box_to_accessible_jquery_ui_slider
 * demo page: http://www.filamentgroup.com/examples/slider/demo.html
 * 
 * Copyright (c) 2008 Filament Group, Inc
 * Dual licensed under the MIT (filamentgroup.com/examples/mit-license.txt) and GPL (filamentgroup.com/examples/gpl-license.txt) licenses.
 *
 * Usage Notes: please refer to our article above for documentation
 *  
 * Version: 1.0, 08.12.08
 * Changelog: 
 * 	
 * --------------------------------------------------------------------
 */



jQuery.fn.accessibleUISlider = function(settings){
	var selects = jQuery(this);

	//id attrs - selects need ids for this feature to work - links handles to original select menus
	var elIds = (function(){
		var tempArr = [];
		selects.each(function(){
			tempArr.push(jQuery(this).attr('id'));
		});
		return tempArr;
	})();
	
	//array of all option elements in select element
	var options = (function(){
		var opts = [];
		selects.eq(0).find('option').each(function(i){
			opts.push({
				value: jQuery(this).attr('value'),
				text: jQuery(this).text()
			});
		});
		return opts;
	})();
	
	//presets by selected indexes
	var presets = (function(){
		var indexes = [];
		selects.each(function(){
			var thisIndex = jQuery(this).find('option:selected').get(0).index;
			var thisPercentage = Math.round((thisIndex / (options.length-1)) *100);
			indexes.push(thisPercentage);
		});
		return indexes;
	})();
	
	//set up slider options API
	var sliderAPI = jQuery.extend({
		labels: 3,
		inject: true,
		rangeOpacity: 0.7,
		width: selects.parent().width(),
		range: selects.length > 1,//boolean for whether it's a range or not
		steps: options.length-1,
		stepping: 0,
		handles: function(){//set starting locations to selected index, if applicable
				var tempArr = [];
				selects.each(function(i){
					tempArr[i] = {
						start: presets[i],
						min: 0,
						max: 100,
						id: 'handle_'+i
					};
				});
				return tempArr;
			}(),
		slide: function(e, ui) {//slide function
				var that = jQuery(this);
				var currValue = ui.value;
				var currIndex = Math.round(currValue / 100 * (options.length-1));
				var currValue = options[currIndex].value;
				var currText = options[currIndex].text;
				//handle feedback tooltip and aria attrs
				var feedback = ui.handle.find('.ui-slider-tooltip'); 
				feedback.html(currText).parent().attr('aria-valuetext', currValue).attr('aria-valuenow', currValue);
				//control original select menu
				var currSelect = jQuery('#'+ui.handle.attr('id').split('handle_')[1]);
				currSelect.find('option').eq(currIndex).attr('selected', 'selected');
			}
	}, settings);
		
	selects.change(function(){
		var thisIndex = jQuery(this).find('option:selected').get(0).index;
		var thisLeft = Math.ceil((thisIndex / (options.length-1))* 100);
		var thisIndex = jQuery('#handle_'+ jQuery(this).attr('id')).parent().prev('a').size();
		jQuery('#handle_'+ jQuery(this).attr('id')).parents('.ui-slider:eq(0)').slider("moveTo", thisLeft,thisIndex);
	});
	
	//opt groups if present
	var groups = (function(){
		if(selects.eq(0).find('optgroup').size()>0){
			var groupedData = [];
			selects.eq(0).find('optgroup').each(function(i){
				groupedData[i] = {};
				groupedData[i].label = jQuery(this).attr('label');
				groupedData[i].options = [];
				jQuery(this).find('option').each(function(){
					groupedData[i].options.push({text: jQuery(this).text(), value: jQuery(this).attr('value')});
				});
			});
			return groupedData;
		}
		else return false;
	})();	

	//create slider component div
	var sliderComponent = jQuery('<div class="sliderComponent"></div>');
	
	//CREATE HANDLES
	selects.each(function(i){
		sliderComponent.append('<div id="handle_'+elIds[i]+'" tabindex="'+ i+1 +'" class="ui-slider-handle ui-default-state" role="slider" aria-valuemin="'+ sliderAPI.minValue +'" aria-valuemax="'+  sliderAPI.maxValue +'"><span class="ui-slider-tooltip ui-component-content"></span></div>');
	});
	
	//CREATE SCALE AND TICS
	sliderComponent.width(sliderAPI.width);
	
	//write dl if there are optgroups
	if(groups) {
		var scale = sliderComponent.append('<dl class="ui-slider-scale"></dl>').find('.ui-slider-scale:eq(0)');
		jQuery(groups).each(function(){
			scale.append('<dt><span>'+this.label+'</span></dt>');//class name becomes camelCased label
			var groupOpts = this.options;
			jQuery(this.options).each(function(i){
				scale.append('<dd><span class="ui-slider-label">'+ groupOpts[i].text +'</span><span class="ui-slider-tic ui-component-content"></span></dd>');
			});
		});
	}
	//write ol
	else {
		var scale = sliderComponent.append('<ol class="ui-slider-scale"></ol>').find('.ui-slider-scale:eq(0)');
		jQuery(options).each(function(){
			scale.append('<li><span class="ui-slider-label">'+this.text+'</span><span class="ui-slider-tic ui-component-content"></span></li>');
		});
	}
	
	//style the li's or dd's
	sliderComponent.find('.ui-slider-scale li, .ui-slider-scale dd').each(function(i){
		jQuery(this).css({
			'left': ((100 /( options.length-1))*i).toFixed(2) + '%'
		});
	});
	
	//first and last classes, and right align the last li/dd
	sliderComponent.find('.ui-slider-scale li:first, .ui-slider-scale dd:first').addClass('first');
	sliderComponent.find('.ui-slider-scale li:last, .ui-slider-scale dd:last').addClass('last').each(function(){ 
		jQuery(this).css({'right': 0, 'left':'auto'});
	});
	
	//show and hide labels depending on showLabels pref
	//show the last one if there are more than 1 specified
	if(sliderAPI.labels > 1) sliderComponent.find('.ui-slider-scale li:last span.ui-slider-label, .ui-slider-scale dd:last span.ui-slider-label').css('text-indent', 0).addClass('ui-slider-label-show');
	//set increment
	var increm = Math.round(options.length / sliderAPI.labels);
	//show em based on inc
	for(var j=0; j<options.length; j+=increm){
		if((options.length - j) > increm){//don't show if it's too close to the end label
			sliderComponent.find('.ui-slider-scale li:eq('+ j +') span.ui-slider-label, .ui-slider-scale dd:eq('+ j +') span.ui-slider-label').addClass('ui-slider-label-show');
		}
	}

	//style the dt's
	sliderComponent.find('.ui-slider-scale dt').each(function(i){
		var aPixel = ((3 / sliderAPI.width) * 100).toFixed(2);
		jQuery(this).css({
			'left': ((100 /( groups.length))*i).toFixed(2) + '%',
			'width': (((sliderAPI.width / groups.length) / sliderAPI.width) * 100).toFixed(2)- aPixel +'%'
		});
	});
	
	sliderAPI.markup = sliderComponent;
	
	//port slider function to pass api in.
	sliderAPI.markup.slider = function(settings){
		var sliderAPI_port = jQuery.extend(sliderAPI, settings);		
		jQuery(this).slider(sliderAPI_port).find('.ui-slider-range').css('opacity', sliderAPI_port.rangeOpacity);
		return this;
	}
	
	//if inject is true, inject slider after last select element and init, otherwise return api including markup
	if(sliderAPI.inject){
		sliderAPI.markup.insertAfter(jQuery(this).eq(this.length-1)).slider(sliderAPI).find('.ui-slider-range').css('opacity', sliderAPI.rangeOpacity);
		return this;
	}
	else{
		return sliderAPI;
	}

}


