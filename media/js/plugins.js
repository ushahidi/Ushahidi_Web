/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */
(function(jQuery){jQuery.each(['backgroundColor','borderBottomColor','borderLeftColor','borderRightColor','borderTopColor','color','outlineColor'],function(i,attr){jQuery.fx.step[attr]=function(fx){if(fx.state==0){fx.start=getColor(fx.elem,attr);fx.end=getRGB(fx.end);}
fx.elem.style[attr]="rgb("+[Math.max(Math.min(parseInt((fx.pos*(fx.end[0]-fx.start[0]))+fx.start[0]),255),0),Math.max(Math.min(parseInt((fx.pos*(fx.end[1]-fx.start[1]))+fx.start[1]),255),0),Math.max(Math.min(parseInt((fx.pos*(fx.end[2]-fx.start[2]))+fx.start[2]),255),0)].join(",")+")";}});function getRGB(color){var result;if(color&&color.constructor==Array&&color.length==3)
return color;if(result=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
return[parseInt(result[1]),parseInt(result[2]),parseInt(result[3])];if(result=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
return[parseFloat(result[1])*2.55,parseFloat(result[2])*2.55,parseFloat(result[3])*2.55];if(result=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
return[parseInt(result[1],16),parseInt(result[2],16),parseInt(result[3],16)];if(result=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
return[parseInt(result[1]+result[1],16),parseInt(result[2]+result[2],16),parseInt(result[3]+result[3],16)];return colors[jQuery.trim(color).toLowerCase()];}
function getColor(elem,attr){var color;do{color=jQuery.curCSS(elem,attr);if(color!=''&&color!='transparent'||jQuery.nodeName(elem,"body"))
break;attr="backgroundColor";}while(elem=elem.parentNode);return getRGB(color);};var colors={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]};})(jQuery);

/*
 * jQuery ifixpng plugin
 * (previously known as pngfix)
 * Version 2.0  (04/11/2007)
 * @requires jQuery v1.1.3 or above
 *
 * Examples at: http://jquery.khurshid.com
 * Copyright (c) 2007 Kush M.
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function($){$.ifixpng=function(customPixel){$.ifixpng.pixel=customPixel;};$.ifixpng.getPixel=function(){return $.ifixpng.pixel||'images/pixel.gif';};var hack={ltie7:$.browser.msie&&$.browser.version<7,filter:function(src){return"progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='"+src+"')";}};$.fn.ifixpng=hack.ltie7?function(){return this.each(function(){var $$=$(this);var base=$('base').attr('href');if($$.is('img')||$$.is('input')){if($$.attr('src')){if($$.attr('src').match(/.*\.png([?].*)?$/i)){var source=(base&&$$.attr('src').substring(0,1)!='/')?base+$$.attr('src'):$$.attr('src');$$.css({filter:hack.filter(source),width:$$.width(),height:$$.height()}).attr({src:$.ifixpng.getPixel()}).positionFix();}}}else{var image=$$.css('backgroundImage');if(image.match(/^url\(["']?(.*\.png([?].*)?)["']?\)$/i)){image=RegExp.$1;$$.css({backgroundImage:'none',filter:hack.filter(image)}).children().children().positionFix();}}});}:function(){return this;};$.fn.iunfixpng=hack.ltie7?function(){return this.each(function(){var $$=$(this);var src=$$.css('filter');if(src.match(/src=["']?(.*\.png([?].*)?)["']?/i)){src=RegExp.$1;if($$.is('img')||$$.is('input')){$$.attr({src:src}).css({filter:''});}else{$$.css({filter:'',background:'url('+src+')'});}}});}:function(){return this;};$.fn.positionFix=function(){return this.each(function(){var $$=$(this);var position=$$.css('position');if(position!='absolute'&&position!='relative'){$$.css({position:'relative'});}});};})(jQuery);

/**
 * Flash (http://jquery.lukelutman.com/plugins/flash)
 * A jQuery plugin for embedding Flash movies.
 *
 * Version 1.0
 * November 9th, 2006
 *
 * Copyright (c) 2006 Luke Lutman (http://www.lukelutman.com)
 * Dual licensed under the MIT and GPL licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * Inspired by:
 * SWFObject (http://blog.deconcept.com/swfobject/)
 * UFO (http://www.bobbyvandersluis.com/ufo/)
 * sIFR (http://www.mikeindustries.com/sifr/)
 *
 * IMPORTANT:
 * The packed version of jQuery breaks ActiveX control
 * activation in Internet Explorer. Use JSMin to minifiy
 * jQuery (see: http://jquery.lukelutman.com/plugins/flash#activex).
 *
 **/
(function(){var $$;$$=jQuery.fn.flash=function(htmlOptions,pluginOptions,replace,update){var block=replace||$$.replace;pluginOptions=$$.copy($$.pluginOptions,pluginOptions);if(!$$.hasFlash(pluginOptions.version)){if(pluginOptions.expressInstall&&$$.hasFlash(6,0,65)){var expressInstallOptions={flashvars:{MMredirectURL:location,MMplayerType:'PlugIn',MMdoctitle:jQuery('title').text()}};}else if(pluginOptions.update){block=update||$$.update;}else{return this;}}
htmlOptions=$$.copy($$.htmlOptions,expressInstallOptions,htmlOptions);return this.each(function(){block.call(this,$$.copy(htmlOptions));});};$$.copy=function(){var options={},flashvars={};for(var i=0;i<arguments.length;i++){var arg=arguments[i];if(arg==undefined)continue;jQuery.extend(options,arg);if(arg.flashvars==undefined)continue;jQuery.extend(flashvars,arg.flashvars);}
options.flashvars=flashvars;return options;};$$.hasFlash=function(){if(/hasFlash\=true/.test(location))return true;if(/hasFlash\=false/.test(location))return false;var pv=$$.hasFlash.playerVersion().match(/\d+/g);var rv=String([arguments[0],arguments[1],arguments[2]]).match(/\d+/g)||String($$.pluginOptions.version).match(/\d+/g);for(var i=0;i<3;i++){pv[i]=parseInt(pv[i]||0);rv[i]=parseInt(rv[i]||0);if(pv[i]<rv[i])return false;if(pv[i]>rv[i])return true;}
return true;};$$.hasFlash.playerVersion=function(){try{try{var axo=new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6');try{axo.AllowScriptAccess='always';}
catch(e){return'6,0,0';}}catch(e){}
return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version').replace(/\D+/g,',').match(/^,?(.+),?$/)[1];}catch(e){try{if(navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin){return(navigator.plugins["Shockwave Flash 2.0"]||navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g,",").match(/^,?(.+),?$/)[1];}}catch(e){}}
return'0,0,0';};$$.htmlOptions={height:240,flashvars:{},pluginspage:'http://www.adobe.com/go/getflashplayer',src:'#',type:'application/x-shockwave-flash',width:320};$$.pluginOptions={expressInstall:false,update:true,version:'6.0.65'};$$.replace=function(htmlOptions){this.innerHTML='<div class="alt">'+this.innerHTML+'</div>';jQuery(this).addClass('flash-replaced').prepend($$.transform(htmlOptions));};$$.update=function(htmlOptions){var url=String(location).split('?');url.splice(1,0,'?hasFlash=true&');url=url.join('');var msg='<p>This content requires the Flash Player. <a href="http://www.adobe.com/go/getflashplayer">Download Flash Player</a>. Already have Flash Player? <a href="'+url+'">Click here.</a></p>';this.innerHTML='<span class="alt">'+this.innerHTML+'</span>';jQuery(this).addClass('flash-update').prepend(msg);};function toAttributeString(){var s='';for(var key in this)
if(typeof this[key]!='function')
s+=key+'="'+this[key]+'" ';return s;};function toFlashvarsString(){var s='';for(var key in this)
if(typeof this[key]!='function')
s+=key+'='+encodeURIComponent(this[key])+'&';return s.replace(/&$/,'');};$$.transform=function(htmlOptions){htmlOptions.toString=toAttributeString;if(htmlOptions.flashvars)htmlOptions.flashvars.toString=toFlashvarsString;return'<embed '+String(htmlOptions)+'/>';};if(window.attachEvent){window.attachEvent("onbeforeunload",function(){__flash_unloadHandler=function(){};__flash_savedUnloadHandler=function(){};});}})();