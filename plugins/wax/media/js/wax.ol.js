/* wax - 3.0.3 - 1.0.4-344-gf45ccac */


/*!
  * Reqwest! A x-browser general purpose XHR connection manager
  * copyright Dustin Diaz 2011
  * https://github.com/ded/reqwest
  * license MIT
  */
!function(window){function serial(a){var b=a.name;if(a.disabled||!b)return"";b=enc(b);switch(a.tagName.toLowerCase()){case"input":switch(a.type){case"reset":case"button":case"image":case"file":return"";case"checkbox":case"radio":return a.checked?b+"="+(a.value?enc(a.value):!0)+"&":"";default:return b+"="+(a.value?enc(a.value):"")+"&"}break;case"textarea":return b+"="+enc(a.value)+"&";case"select":return b+"="+enc(a.options[a.selectedIndex].value)+"&"}return""}function enc(a){return encodeURIComponent(a)}function reqwest(a,b){return new Reqwest(a,b)}function init(o,fn){function error(a){o.error&&o.error(a),complete(a)}function success(resp){o.timeout&&clearTimeout(self.timeout)&&(self.timeout=null);var r=resp.responseText,JSON;switch(type){case"json":resp=JSON?JSON.parse(r):eval("("+r+")");break;case"js":resp=eval(r);break;case"html":resp=r}fn(resp),o.success&&o.success(resp),complete(resp)}function complete(a){o.complete&&o.complete(a)}this.url=typeof o=="string"?o:o.url,this.timeout=null;var type=o.type||setType(this.url),self=this;fn=fn||function(){},o.timeout&&(this.timeout=setTimeout(function(){self.abort(),error()},o.timeout)),this.request=getRequest(o,success,error)}function setType(a){if(/\.json$/.test(a))return"json";if(/\.jsonp$/.test(a))return"jsonp";if(/\.js$/.test(a))return"js";if(/\.html?$/.test(a))return"html";if(/\.xml$/.test(a))return"xml";return"js"}function Reqwest(a,b){this.o=a,this.fn=b,init.apply(this,arguments)}function getRequest(a,b,c){if(a.type!="jsonp"){var f=xhr();f.open(a.method||"GET",typeof a=="string"?a:a.url,!0),setHeaders(f,a),f.onreadystatechange=readyState(f,b,c),a.before&&a.before(f),f.send(a.data||null);return f}var d=doc.createElement("script");window[getCallbackName(a)]=generalCallback,d.type="text/javascript",d.src=a.url,d.async=!0;var e=function(){a.success&&a.success(lastValue),lastValue=undefined,head.removeChild(d)};d.onload=e,d.onreadystatechange=function(){/^loaded|complete$/.test(d.readyState)&&e()},head.appendChild(d)}function generalCallback(a){lastValue=a}function getCallbackName(a){var b=a.jsonpCallback||"callback";if(a.url.slice(-(b.length+2))==b+"=?"){var c="reqwest_"+uniqid++;a.url=a.url.substr(0,a.url.length-1)+c;return c}var d=new RegExp(b+"=([\\w]+)");return a.url.match(d)[1]}function setHeaders(a,b){var c=b.headers||{};c.Accept=c.Accept||"text/javascript, text/html, application/xml, text/xml, */*",b.crossOrigin||(c["X-Requested-With"]=c["X-Requested-With"]||"XMLHttpRequest");if(b.data){c["Content-type"]=c["Content-type"]||"application/x-www-form-urlencoded";for(var d in c)c.hasOwnProperty(d)&&a.setRequestHeader(d,c[d],!1)}}function readyState(a,b,c){return function(){a&&a.readyState==4&&(twoHundo.test(a.status)?b(a):c(a))}}var twoHundo=/^20\d$/,doc=document,byTag="getElementsByTagName",head=doc[byTag]("head")[0],xhr="XMLHttpRequest"in window?function(){return new XMLHttpRequest}:function(){return new ActiveXObject("Microsoft.XMLHTTP")},uniqid=0,lastValue;Reqwest.prototype={abort:function(){this.request.abort()},retry:function(){init.call(this,this.o,this.fn)}},reqwest.serialize=function(a){var b=a[byTag]("input"),c=a[byTag]("select"),d=a[byTag]("textarea");return(v(b).chain().toArray().map(serial).value().join("")+v(c).chain().toArray().map(serial).value().join("")+v(d).chain().toArray().map(serial).value().join("")).replace(/&$/,"")},reqwest.serializeArray=function(a){for(var b=this.serialize(a).split("&"),c=0,d=b.length,e=[],f;c<d;c++)b[c]&&(f=b[c].split("="))&&e.push({name:f[0],value:f[1]});return e};var old=window.reqwest;reqwest.noConflict=function(){window.reqwest=old;return this},window.reqwest=reqwest}(this)
// Instantiate objects based on a JSON "record". The record must be a statement
// array in the following form:
//
//     [ "{verb} {subject}", arg0, arg1, arg2, ... argn ]
//
// Each record is processed from a passed `context` which starts from the
// global (ie. `window`) context if unspecified.
//
// - `@literal` Evaluate `subject` and return its value as a scalar. Useful for
//   referencing API constants, object properties or other values.
// - `@new` Call `subject` as a constructor with args `arg0 - argn`. The
//   newly created object will be the new context.
// - `@call` Call `subject` as a function with args `arg0 - argn` in the
//   global namespace. The return value will be the new context.
// - `@chain` Call `subject` as a method of the current context with args `arg0
//   - argn`. The return value will be the new context.
// - `@inject` Call `subject` as a method of the current context with args
//   `arg0 - argn`. The return value will *not* affect the context.
// - `@group` Treat `arg0 - argn` as a series of statement arrays that share a
//   context. Each statement will be called in serial and affect the context
//   for the next statement.
//
// Usage:
//
//     var gmap = ['@new google.maps.Map',
//         ['@call document.getElementById', 'gmap'],
//         {
//             center: [ '@new google.maps.LatLng', 0, 0 ],
//             zoom: 2,
//             mapTypeId: [ '@literal google.maps.MapTypeId.ROADMAP' ]
//         }
//     ];
//     wax.Record(gmap);
var wax = wax || {};


// TODO: replace with non-global-modifier
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/Reduce
if (!Array.prototype.reduce) {
  Array.prototype.reduce = function(fun /*, initialValue */) {
    "use strict";

    if (this === void 0 || this === null)
      throw new TypeError();

    var t = Object(this);
    var len = t.length >>> 0;
    if (typeof fun !== "function")
      throw new TypeError();

    // no value to return if no initial value and an empty array
    if (len == 0 && arguments.length == 1)
      throw new TypeError();

    var k = 0;
    var accumulator;
    if (arguments.length >= 2) {
      accumulator = arguments[1];
    } else {
      do {
        if (k in t) {
          accumulator = t[k++];
          break;
        }

        // if array contains no values, no initial value to return
        if (++k >= len)
          throw new TypeError();
      }
      while (true);
    }

    while (k < len) {
      if (k in t)
        accumulator = fun.call(undefined, accumulator, t[k], k, t);
      k++;
    }

    return accumulator;
  };
}


wax.Record = function(obj, context) {
    var getFunction = function(head, cur) {
        // TODO: strip out reduce
        var ret = head.split('.').reduce(function(part, segment) {
            return [part[1] || part[0], part[1] ? part[1][segment] : part[0][segment]];
        }, [cur || window, null]);
        if (ret[0] && ret[1]) {
            return ret;
        } else {
            throw head + ' not found.';
        }
    };
    var makeObject = function(fn_name, args) {
        var fn_obj = getFunction(fn_name),
            obj;
        args = args.length ? wax.Record(args) : [];

        // real browsers
        if (Object.create) {
            obj = Object.create(fn_obj[1].prototype);
            fn_obj[1].apply(obj, args);
        // lord have mercy on your soul.
        } else {
            switch (args.length) {
                case 0: obj = new fn_obj[1](); break;
                case 1: obj = new fn_obj[1](args[0]); break;
                case 2: obj = new fn_obj[1](args[0], args[1]); break;
                case 3: obj = new fn_obj[1](args[0], args[1], args[2]); break;
                case 4: obj = new fn_obj[1](args[0], args[1], args[2], args[3]); break;
                case 5: obj = new fn_obj[1](args[0], args[1], args[2], args[3], args[4]); break;
                default: break;
            }
        }
        return obj;
    };
    var runFunction = function(fn_name, args, cur) {
        var fn_obj = getFunction(fn_name, cur);
        var fn_args = args.length ? wax.Record(args) : [];
        // @TODO: This is currently a stopgap measure that calls methods like
        // `foo.bar()` in the context of `foo`. It will probably be necessary
        // in the future to be able to call `foo.bar()` from other contexts.
        if (cur && fn_name.indexOf('.') === -1) {
            return fn_obj[1].apply(cur, fn_args);
        } else {
            return fn_obj[1].apply(fn_obj[0], fn_args);
        }
    };
    var isKeyword = function(string) {
        return wax.util.isString(string) && (wax.util.indexOf([
            '@new',
            '@call',
            '@literal',
            '@chain',
            '@inject',
            '@group'
        ], string.split(' ')[0]) !== -1);
    };
    var altersContext = function(string) {
        return wax.util.isString(string) && (wax.util.indexOf([
            '@new',
            '@call',
            '@chain'
        ], string.split(' ')[0]) !== -1);
    };
    var getStatement = function(obj) {
        if (wax.util.isArray(obj) && obj[0] && isKeyword(obj[0])) {
            return {
                verb: obj[0].split(' ')[0],
                subject: obj[0].split(' ')[1],
                object: obj.slice(1)
            };
        }
        return false;
    };

    var i,
        fn = false,
        ret = null,
        child = null,
        statement = getStatement(obj);
    if (statement) {
        switch (statement.verb) {
        case '@group':
            for (i = 0; i < statement.object.length; i++) {
                ret = wax.Record(statement.object[i], context);
                child = getStatement(statement.object[i]);
                if (child && altersContext(child.verb)) {
                    context = ret;
                }
            }
            return context;
        case '@new':
            return makeObject(statement.subject, statement.object);
        case '@literal':
            fn = getFunction(statement.subject);
            return fn ? fn[1] : null;
        case '@inject':
            return runFunction(statement.subject, statement.object, context);
        case '@chain':
            return runFunction(statement.subject, statement.object, context);
        case '@call':
            return runFunction(statement.subject, statement.object, null);
        }
    } else if (obj !== null && typeof obj === 'object') {
        var keys = wax.util.keys(obj);
        for (i = 0; i < keys.length; i++) {
            var key = keys[i];
            obj[key] = wax.Record(obj[key], context);
        }
        return obj;
    } else {
        return obj;
    }
};
wax = wax || {};

// Attribution
// -----------
wax.attribution = function() {
    var container,
        a = {};

    a.set = function(content) {
        if (typeof content === 'undefined') return;
        container.innerHTML = content;
        return this;
    };

    a.element = function() {
        return container;
    };

    a.init = function() {
        container = document.createElement('div');
        container.className = 'wax-attribution';
        return this;
    };

    return a.init();
};
// Formatter
// ---------
wax.formatter = function(x) {
    var formatter = {},
        f;

    // Prevent against just any input being used.
    if (x && typeof x === 'string') {
        try {
            // Ugly, dangerous use of eval.
            eval('f = ' + x);
        } catch (e) {
            if (console) console.log(e);
        }
    } else if (x && typeof x === 'function') {
        f = x;
    } else {
        f = function() {};
    }

    // Wrap the given formatter function in order to
    // catch exceptions that it may throw.
    formatter.format = function(options, data) {
        try {
            return f(options, data);
        } catch (e) {
            if (console) console.log(e);
        }
    };

    return formatter;
};
// GridInstance
// ------------
// GridInstances are queryable, fully-formed
// objects for acquiring features from events.
wax.GridInstance = function(grid_tile, formatter, options) {
    options = options || {};
    // resolution is the grid-elements-per-pixel ratio of gridded data.
    // The size of a tile element. For now we expect tiles to be squares.
    var instance = {},
        resolution = options.resolution || 4;
        tileSize = options.tileSize || 256;

    // Resolve the UTF-8 encoding stored in grids to simple
    // number values.
    // See the [utfgrid section of the mbtiles spec](https://github.com/mapbox/mbtiles-spec/blob/master/1.1/utfgrid.md)
    // for details.
    function resolveCode(key) {
        if (key >= 93) key--;
        if (key >= 35) key--;
        key -= 32;
        return key;
    }

    // Get a feature:
    //
    // * `x` and `y`: the screen coordinates of an event
    // * `tile_element`: a DOM element of a tile, from which we can get an offset.
    // * `options` options to give to the formatter: minimally having a `format`
    //   member, being `full`, `teaser`, or something else.
    instance.getFeature = function(x, y, tile_element, options) {
        if (!(grid_tile && grid_tile.grid)) return;

        // IE problem here - though recoverable, for whatever reason
        var offset = wax.util.offset(tile_element),
            tileX = offset.left,
            tileY = offset.top,
            res = (offset.width / tileSize) * resolution;

        // This tile's resolution. larger tiles will have lower, aka coarser, resolutions
        if ((y - tileY < 0) || (x - tileX < 0)) return;
        if ((Math.floor(y - tileY) > tileSize) ||
            (Math.floor(x - tileX) > tileSize)) return;
        // Find the key in the grid. The above calls should ensure that
        // the grid's array is large enough to make this work.
        var key = grid_tile.grid[
           Math.floor((y - tileY) / res)
        ].charCodeAt(
           Math.floor((x - tileX) / res)
        );

        key = resolveCode(key);

        // If this layers formatter hasn't been loaded yet,
        // download and load it now.
        if (grid_tile.keys[key] && grid_tile.data[grid_tile.keys[key]]) {
            return formatter.format(options, grid_tile.data[grid_tile.keys[key]]);
        }
    };

    return instance;
};
// GridManager
// -----------
// Generally one GridManager will be used per map.
//
// It takes one options object, which current accepts a single option:
// `resolution` determines the number of pixels per grid element in the grid.
// The default is 4.
wax.GridManager = function(options) {
    options = options || {};

    var resolution = options.resolution || 4,
        grid_tiles = {},
        manager = {},
        formatter;

    var formatterUrl = function(url) {
        return url.replace(/\d+\/\d+\/\d+\.\w+/, 'layer.json');
    };

    var gridUrl = function(url) {
        return url.replace(/(\.png|\.jpg|\.jpeg)(\d*)/, '.grid.json');
    };

    function getFormatter(url, callback) {
        if (typeof formatter !== 'undefined') {
            return callback(null, formatter);
        } else {
            wax.request.get(formatterUrl(url), function(err, data) {
                if (data && data.formatter) {
                    formatter = wax.formatter(data.formatter);
                } else {
                    formatter = false;
                }
                return callback(err, formatter);
            });
        }
    }

    function templatedGridUrl(template) {
        if (typeof template === 'string') template = [template];
        return function templatedGridFinder(url) {
            if (!url) return;
            var xyz = /(\d+)\/(\d+)\/(\d+)\.[\w\._]+/g.exec(url);
            if (!xyz) return;
            return template[parseInt(xyz[2], 10) % template.length]
                .replace('{z}', xyz[1])
                .replace('{x}', xyz[2])
                .replace('{y}', xyz[3]);
        };
    }

    manager.formatter = function(x) {
        if (!arguments.length) return formatter;
        formatter =  wax.formatter(x);
        return manager;
    };

    manager.formatterUrl = function(x) {
        if (!arguments.length) return formatterUrl;
        formatterUrl = typeof x === 'string' ?
            function() { return x; } : x;
        return manager;
    };

    manager.gridUrl = function(x) {
        if (!arguments.length) return gridUrl;
        gridUrl = typeof x === 'function' ?
            x : templatedGridUrl(x);
        return manager;
    };

     manager.getGrid = function(url, callback) {
        getFormatter(url, function(err, f) {
            var gurl = gridUrl(url);
            if (err || !f || !gurl) return callback(err, null);

            wax.request.get(gurl, function(err, t) {
                if (err) return callback(err, null);
                callback(null, wax.GridInstance(t, f, {
                    resolution: resolution || 4
                }));
            });
        });
        return manager;
    };

    if (options.formatter) manager.formatter(options.formatter);
    if (options.grids) manager.gridUrl(options.grids);

    return manager;
};
// Wax Legend
// ----------

// Wax header
var wax = wax || {};

wax.legend = function() {
    var element,
        legend = {},
        container;

    legend.element = function() {
        return container;
    };

    legend.content = function(content) {
        if (!arguments.length) return element.innerHTML;
        if (content) {
            element.innerHTML = content;
            element.style.display = 'block';
        } else {
            element.innerHTML = '';
            element.style.display = 'none';
        }
        return this;
    };

    legend.add = function() {
        container = document.createElement('div');
        container.className = 'wax-legends';

        element = document.createElement('div');
        element.className = 'wax-legend';
        element.style.display = 'none';

        container.appendChild(element);
        return this;
    };

    return legend.add();
};
// Like underscore's bind, except it runs a function
// with no arguments off of an object.
//
//     var map = ...;
//     w(map).melt(myFunction);
//
// is equivalent to
//
//     var map = ...;
//     myFunction(map);
//
var w = function(self) {
    self.melt = function(func, obj) {
        return func.apply(obj, [self, obj]);
    };
    return self;
};
// Wax GridUtil
// ------------

// Wax header
var wax = wax || {};

// Request
// -------
// Request data cache. `callback(data)` where `data` is the response data.
wax.request = {
    cache: {},
    locks: {},
    promises: {},
    get: function(url, callback) {
        // Cache hit.
        if (this.cache[url]) {
            return callback(this.cache[url][0], this.cache[url][1]);
        // Cache miss.
        } else {
            this.promises[url] = this.promises[url] || [];
            this.promises[url].push(callback);
            // Lock hit.
            if (this.locks[url]) return;
            // Request.
            var that = this;
            this.locks[url] = true;
            reqwest({
                url: url + '?callback=grid',
                type: 'jsonp',
                jsonpCallback: 'callback',
                success: function(data) {
                    that.locks[url] = false;
                    that.cache[url] = [null, data];
                    for (var i = 0; i < that.promises[url].length; i++) {
                        that.promises[url][i](that.cache[url][0], that.cache[url][1]);
                    }
                },
                error: function(err) {
                    that.locks[url] = false;
                    that.cache[url] = [err, null];
                    for (var i = 0; i < that.promises[url].length; i++) {
                        that.promises[url][i](that.cache[url][0], that.cache[url][1]);
                    }
                }
            });
        }
    }
};
if (!wax) var wax = {};

// A wrapper for reqwest jsonp to easily load TileJSON from a URL.
wax.tilejson = function(url, callback) {
    reqwest({
        url: url + '?callback=grid',
        type: 'jsonp',
        jsonpCallback: 'callback',
        success: callback,
        error: callback
    });
};
var wax = wax || {};
wax.tooltip = {};

wax.tooltip = function(options) {
    this._currentTooltip = undefined;
    options = options || {};
    if (options.animationOut) this.animationOut = options.animationOut;
    if (options.animationIn) this.animationIn = options.animationIn;
};

// Helper function to determine whether a given element is a wax popup.
wax.tooltip.prototype.isPopup = function(el) {
    return el && el.className.indexOf('wax-popup') !== -1;
};

// Get the active tooltip for a layer or create a new one if no tooltip exists.
// Hide any tooltips on layers underneath this one.
wax.tooltip.prototype.getTooltip = function(feature, context, index, evt) {
    tooltip = document.createElement('div');
    tooltip.className = 'wax-tooltip wax-tooltip-' + index;
    tooltip.innerHTML = feature;
    context.appendChild(tooltip);
    return tooltip;
};

// Hide a given tooltip.
wax.tooltip.prototype.hideTooltip = function(el) {
    if (!el) return;
    var event;
    var remove = function() {
        if (this.parentNode) this.parentNode.removeChild(this);
    };
    if (el.style['-webkit-transition'] !== undefined && this.animationOut) {
        event = 'webkitTransitionEnd';
    } else if (el.style.MozTransition !== undefined && this.animationOut) {
        event = 'transitionend';
    }
    if (event) {
        el.addEventListener(event, remove, false);
        el.addEventListener('transitionend', remove, false);
        el.className += ' ' + this.animationOut;
    } else {
        if (el.parentNode) el.parentNode.removeChild(el);
    }
};

// Expand a tooltip to be a "popup". Suspends all other tooltips from being
// shown until this popup is closed or another popup is opened.
wax.tooltip.prototype.click = function(feature, context, index) {
    // Hide any current tooltips.
    this.unselect(context);

    var tooltip = this.getTooltip(feature, context, index);
    var close = document.createElement('a');
    close.href = '#close';
    close.className = 'close';
    close.innerHTML = 'Close';

    var closeClick = wax.util.bind(function(ev) {
        this.hideTooltip(tooltip);
        this._currentTooltip = undefined;
        ev.returnValue = false; // Prevents hash change.
        if (ev.stopPropagation) ev.stopPropagation();
        if (ev.preventDefault) ev.preventDefault();
        return false;
    }, this);
    // IE compatibility.
    if (close.addEventListener) {
        close.addEventListener('click', closeClick, false);
    } else if (close.attachEvent) {
        close.attachEvent('onclick', closeClick);
    }

    tooltip.className += ' wax-popup';
    tooltip.innerHTML = feature;
    tooltip.appendChild(close);
    this._currentTooltip = tooltip;
};

// Show a tooltip.
wax.tooltip.prototype.select = function(feature, context, layer_id, evt) {
    if (!feature) return;
    if (this.isPopup(this._currentTooltip)) return;

    this._currentTooltip = this.getTooltip(feature, context, layer_id, evt);
    context.style.cursor = 'pointer';
};


// Hide all tooltips on this layer and show the first hidden tooltip on the
// highest layer underneath if found.
wax.tooltip.prototype.unselect = function(context) {
    if (this.isPopup(this._currentTooltip)) return;

    context.style.cursor = 'default';
    if (this._currentTooltip) {
        this.hideTooltip(this._currentTooltip);
        this._currentTooltip = undefined;
    }
};

wax.tooltip.prototype.out = wax.tooltip.prototype.unselect;
wax.tooltip.prototype.over = wax.tooltip.prototype.select;
wax.tooltip.prototype.click = wax.tooltip.prototype.click;
var wax = wax || {};
wax.util = wax.util || {};

// Utils are extracted from other libraries or
// written from scratch to plug holes in browser compatibility.
wax.util = {
    // From Bonzo
    offset: function(el) {
        // TODO: window margins
        //
        // Okay, so fall back to styles if offsetWidth and height are botched
        // by Firefox.
        var width = el.offsetWidth || parseInt(el.style.width, 10),
            height = el.offsetHeight || parseInt(el.style.height, 10),
            top = 0,
            left = 0;

        var calculateOffset = function(el) {
            if (el === document.body || el === document.documentElement) return;
            top += el.offsetTop;
            left += el.offsetLeft;

            // Add additional CSS3 transform handling.
            // These features are used by Google Maps API V3.
            var style = el.style.transform ||
                el.style['-webkit-transform'] ||
                el.style.MozTransform;
            if (style) {
                if (match = style.match(/translate\((.+)px, (.+)px\)/)) {
                    top += parseInt(match[2], 10);
                    left += parseInt(match[1], 10);
                } else if (match = style.match(/translate3d\((.+)px, (.+)px, (.+)px\)/)) {
                    top += parseInt(match[2], 10);
                    left += parseInt(match[1], 10);
                } else if (match = style.match(/matrix3d\(([\-\d,\s]+)\)/)) {
                    var pts = match[1].split(',');
                    top += parseInt(pts[13], 10);
                    left += parseInt(pts[12], 10);
                }
            }
        };

        calculateOffset(el);

        try {
            while (el = el.offsetParent) calculateOffset(el);
        } catch(e) {
            // Hello, internet explorer.
        }

        // Offsets from the body
        top += document.body.offsetTop;
        left += document.body.offsetLeft;
        // Offsets from the HTML element
        top += document.body.parentNode.offsetTop;
        left += document.body.parentNode.offsetLeft;

        // Firefox and other weirdos. Similar technique to jQuery's
        // `doesNotIncludeMarginInBodyOffset`.
        var htmlComputed = document.defaultView ?
            window.getComputedStyle(document.body.parentNode, null) :
            document.body.parentNode.currentStyle;
        if (document.body.parentNode.offsetTop !==
            parseInt(htmlComputed.marginTop, 10) &&
            !isNaN(parseInt(htmlComputed.marginTop, 10))) {
            top += parseInt(htmlComputed.marginTop, 10);
        left += parseInt(htmlComputed.marginLeft, 10);
        }

        return {
            top: top,
            left: left,
            height: height,
            width: width
        };
    },

    '$': function(x) {
        return (typeof x === 'string') ?
            document.getElementById(x) :
            x;
    },

    // From underscore, minus funcbind for now.
    // Returns a version of a function that always has the second parameter,
    // `obj`, as `this`.
    bind: function(func, obj) {
      var args = Array.prototype.slice.call(arguments, 2);
      return function() {
        return func.apply(obj, args.concat(Array.prototype.slice.call(arguments)));
      };
    },
    // From underscore
    isString: function(obj) {
      return !!(obj === '' || (obj && obj.charCodeAt && obj.substr));
    },
    // IE doesn't have indexOf
    indexOf: function(array, item) {
        var nativeIndexOf = Array.prototype.indexOf;
        if (array === null) return -1;
        var i, l;
        if (nativeIndexOf && array.indexOf === nativeIndexOf) return array.indexOf(item);
        for (i = 0, l = array.length; i < l; i++) if (array[i] === item) return i;
        return -1;
    },
    // is this object an array?
    isArray: Array.isArray || function(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    },
    // From underscore: reimplement the ECMA5 `Object.keys()` method
    keys: Object.keys || function(obj) {
        var hasOwnProperty = Object.prototype.hasOwnProperty;
        if (obj !== Object(obj)) throw new TypeError('Invalid object');
        var keys = [];
        for (var key in obj) if (hasOwnProperty.call(obj, key)) keys[keys.length] = key;
        return keys;
    },
    // From quirksmode: normalize the offset of an event from the top-left
    // of the page.
    eventoffset: function(e) {
        var posx = 0;
        var posy = 0;
        if (!e) var e = window.event;
        if (e.pageX || e.pageY) {
            // Good browsers
            return {
                x: e.pageX,
                y: e.pageY
            };
        } else if (e.clientX || e.clientY) {
            // Internet Explorer
            var doc = document.documentElement, body = document.body;
            var htmlComputed = document.body.parentNode.currentStyle;
            var topMargin = parseInt(htmlComputed.marginTop, 10) || 0;
            var leftMargin = parseInt(htmlComputed.marginLeft, 10) || 0;
            return {
                x: e.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) -
                  (doc && doc.clientLeft || body && body.clientLeft || 0) + leftMargin,
                y: e.clientY + (doc && doc.scrollTop  || body && body.scrollTop  || 0) -
                  (doc && doc.clientTop  || body && body.clientTop  || 0) + topMargin
            };
        } else if (e.touches && e.touches.length === 1) {
            // Touch browsers
            return {
                x: e.touches[0].pageX,
                y: e.touches[0].pageY
            };
        }
    }
};
// Wax header
var wax = wax || {};
wax.ol = wax.ol || {};

// An interaction toolkit for tiles that implement the
// [MBTiles UTFGrid spec](https://github.com/mapbox/mbtiles-spec)
wax.ol.Embedder =
    OpenLayers.Class(OpenLayers.Control, {
    initialize: function(options) {
      options = options || {};
      OpenLayers.Control.prototype.initialize.apply(this, [options || {}]);
    },

    // Add handlers to the map
    setMap: function(map) {
      if ($('#' + this.el + '-script').length) {
        OpenLayers.Control.prototype.setMap.apply(this, arguments);
        $(map.div).prepend($('<input type="text" class="embed-src" />')
          .css({
              'z-index': '9999999999',
              'position': 'relative'
          })
          .val("<div id='" + this.el + "-script'>" + $('#' + this.el + '-script').html() + '</div>'));
      }
      this.activate();
    },

    CLASS_NAME: 'wax.ol.Embedder'
});
// Wax header
var wax = wax || {};
wax.ol = wax.ol || {};

var addEv = function(element, name, observer) {
    if (element.addEventListener) {
        element.addEventListener(name, observer, false);
    } else if (element.attachEvent) {
        element.attachEvent('on' + name, observer);
    }
};

// An interaction toolkit for tiles that implement the
// [MBTiles UTFGrid spec](https://github.com/mapbox/mbtiles-spec)
wax.ol.Interaction =
    OpenLayers.Class(OpenLayers.Control, {
    feature: {},
    handlerOptions: null,
    handlers: null,

    gm: new wax.GridManager(),

    initialize: function(tilejson, options) {
        this.options = options || {};
        this.clickAction = this.options.clickAction || 'full';
        this.gm = new wax.GridManager(tilejson);

        OpenLayers.Control.prototype.initialize.apply(this, [this.options || {}]);

        this.callbacks = this.options.callbacks || new wax.tooltip();
    },

    setMap: function(map) {
        addEv(map.viewPortDiv, 'mousemove', wax.util.bind(this.getInfoForHover, this));
        addEv(map.viewPortDiv, 'mouseout', wax.util.bind(this.resetLayers, this));
        this.clickHandler = new OpenLayers.Handler.Click(
            this, {
                click: this.getInfoForClick
            }
        );

        this.clickHandler.setMap(map);
        this.clickHandler.activate();

        map.events.on({
            addlayer: this.resetLayers,
            changelayer: this.resetLayers,
            removelayer: this.resetLayers,
            changebaselayer: this.resetLayers,
            scope: this
        });

        OpenLayers.Control.prototype.setMap.apply(this, arguments);
    },

    // Get an Array of the stack of tiles under the mouse.
    // This operates with pixels only, since there's no way
    // to bubble through an element which is sitting on the map
    // (like an SVG overlay).
    //
    // If no tiles are under the mouse, returns an empty array.
    getTileStack: function(layers, pos) {
        // If we don't have both an event and some tiles, it's nothing.
        if (!layers || !pos) return [];
        var tiles = [];
        layerfound: for (var j = 0; j < layers.length; j++) {
            for (var x = 0; x < layers[j].grid.length; x++) {
                for (var y = 0; y < layers[j].grid[x].length; y++) {
                    if (layers[j].grid[x][y].imgDiv) {
                        var divpos = wax.util.offset(layers[j].grid[x][y].imgDiv);
                    } else {
                        var divpos = wax.util.offset(layers[j].grid[x][y].frame);
                    }
                    if (divpos &&
                        ((divpos.top < pos.y) &&
                         ((divpos.top + 256) > pos.y) &&
                         (divpos.left < pos.x) &&
                         ((divpos.left + 256) > pos.x))) {
                        tiles.push(layers[j].grid[x][y]);
                        continue layerfound;
                    }
                }
            }
        }
        return tiles;
    },

    // Get all interactable layers
    viableLayers: function() {
        if (this._viableLayers) return this._viableLayers;
        this._viableLayers = [];
        for (var i in this.map.layers) {
            // TODO: make better indication of whether
            // this is an interactive layer
            if ((this.map.layers[i].visibility === true) &&
                (this.map.layers[i].CLASS_NAME === 'OpenLayers.Layer.TMS')) {
              this._viableLayers.push(this.map.layers[i]);
            }
        }
        return this._viableLayers;
    },

    resetLayers: function(evt) {
        this._viableLayers = null;
        // Fix a condition in which mouseout is called, but the user is really mousing
        // over to a different tile.
        var newTarget = evt.relatedTarget || evt.toElement;
        if (newTarget && newTarget.className !== 'olTileImage') {
            this.callbacks.out(this.map.viewPortDiv);
        }
    },

    // React to a click mouse event
    // This is the `pause` handler attached to the map.
    getInfoForClick: function(evt) {
        // If there's no event, this handler should not continue.
        if (!evt) return;
        var layers = this.viableLayers();
        var pos = wax.util.eventoffset(evt);
        var tiles = this.getTileStack(this.viableLayers(), pos);
        var feature = null,
        g = null;
        var that = this;

        for (var t = 0; t < tiles.length; t++) {
            if (!tiles[t].url) continue;
            this.gm.getGrid(tiles[t].url, function(err, g) {
                if (!g) return;
                var feature = g.getFeature(pos.x, pos.y, tiles[t].frame, {
                    format: that.clickAction
                });
                if (feature) {
                    switch (that.clickAction) {
                        case 'full':
                            that.callbacks.click(feature, tiles[t].layer.map.viewPortDiv, t);
                        break;
                        case 'location':
                            window.location = feature;
                        break;
                    }
                }
            });
        }
    },

    // React to a hover mouse event, by finding all tiles,
    // finding features, and calling `this.callbacks[]`
    // This is the `click` handler attached to the map.
    getInfoForHover: function(evt) {
        // If there's no event, this handler should not proceed.
        if (!evt) return;
        var options = { format: 'teaser' };
        var layers = this.viableLayers();
        var pos = wax.util.eventoffset(evt);
        var tiles = this.getTileStack(this.viableLayers(), pos);
        var feature = null,
        g = null;
        var that = this;

        for (var t = 0; t < tiles.length; t++) {
            if (!tiles[t].url) continue;
            // This features has already been loaded, or
            // is currently being requested.
            this.gm.getGrid(tiles[t].url, function(err, g) {
                if (g && tiles[t]) {
                    var feature = g.getFeature(pos.x, pos.y, tiles[t].frame, options);

                    if (feature) {
                        if (!tiles[t]) return;
                        if (feature && that.feature[t] !== feature) {
                            that.feature[t] = feature;
                            that.callbacks.out(tiles[t].layer.map.div);
                            that.callbacks.over(feature, tiles[t].layer.map.div, t, evt);
                        } else if (!feature) {
                            that.feature[t] = null;
                            that.callbacks.out(tiles[t].layer.map.div);
                        } else {
                        }
                    } else {
                        // Request this feature
                        // TODO(tmcw) re-add layer
                        // Only nix this tooltip if the current tooltip is
                        // owned by this layer
                        if (that.feature[t]) {
                            that.callbacks.out(tiles[t].layer.map.div);
                        }
                        that.feature[t] = null;
                    }
                }
            });
        }
    },
    CLASS_NAME: 'wax.ol.Interaction'
});
// Wax: Legend Control
// -------------------

// Wax header
var wax = wax || {};
wax.ol = wax.ol || {};

wax.ol.Legend = OpenLayers.Class(OpenLayers.Control, {
    CLASS_NAME: 'wax.ol.Legend',
    legend: null,
    options: null,

    initialize: function(options) {
        this.options = options || {};
        OpenLayers.Control.prototype.initialize.apply(this, [options || {}]);
    },

    activate: function() {
        this.legend = new wax.Legend(this.map.viewPortDiv, this.options.container);
        return OpenLayers.Control.prototype.activate.apply(this, arguments);
    },

    setMap: function(map) {
        OpenLayers.Control.prototype.setMap.apply(this, arguments);
        this.activate();
        this.map.events.on({
            'addlayer': this.setLegend,
            'changelayer': this.setLegend,
            'removelayer': this.setLegend,
            'changebaselayer': this.setLegend,
            scope: this
        });
    },

    setLegend: function() {
        var urls = [];
        for (var i = 0; i < this.map.layers.length; i++) {
            var layer = this.map.layers[i];
            if (layer && layer.getURL && layer.visibility) {
                urls.push(layer.getURL(new OpenLayers.Bounds()));
            }
        }
        this.legend.render(urls);
    }
});

// Wax: Legend Control
// -------------------
// This is a simple layer switcher for OpenLayers, based loosely
// off of the strategy of the openlayers_plus blockswitcher.
// See the last lines for the `layeradded` event, which is the
// way to style layer switcher elements.

// Wax header
var wax = wax || {};
wax.ol = wax.ol || {};

wax.ol.Switcher = OpenLayers.Class(OpenLayers.Control, {
    CLASS_NAME: 'wax.ol.Switcher',

    // Called on `new`. In the tradition of BackBone.js, this control takes
    // an option `e` in its settings object which is a reference to a DOM
    // element it will own.
    initialize: function(options) {
        this.$element = $(options.e);
        this.options = options || {};
        OpenLayers.Control.prototype.initialize.apply(this, [options || {}]);
    },

    // Called from OpenLayers. Attach event handlers to call `this.redraw`
    // when the map state has changed.
    setMap: function(map) {
        OpenLayers.Control.prototype.setMap.apply(this, arguments);
        this.map.events.on({
            addlayer: this.redraw,
            changelayer: this.redraw,
            removelayer: this.redraw,
            changebaselayer: this.redraw,
            scope: this
        });
        this.redraw();
    },

    // The callback of a click on a layer switcher layer element (usually a
    // link element).
    layerClick: function(evt) {
      var element = evt.currentTarget;
      var layer = $(element).data('layer');
      $('a.active', this.$element).removeClass('active');
      $.each(this.map.getLayersBy('isBaseLayer', false),
        function() {
          // Only make visible, non-RootContainer layers invisible.
          // RootContainer layers are behind-the-scenes OpenLayers-created
          // layers that help manage interaction with multiple Vector layers.
          if (this.CLASS_NAME !== 'OpenLayers.Layer.Vector.RootContainer' &&
             this.displayInLayerSwitcher) {
            this.setVisibility(false);
          }
        }
      );
      layer.setVisibility(true);
      $(element).addClass('active');
    },

    // Evaluate whether the map state has changed enough to justify a
    // redraw of this element
    needsRedraw: function() {
        if (!this.layerStates || this.layerStates.length ||
           (this.map.layers.length != this.layerStates.length)) {
            return true;
        }
        for (var i = 0, len = this.layerStates.length; i < len; i++) {
            var layerState = this.layerStates[i];
            var layer = this.map.layers[i];
            if ((layerState.name != layer.name) ||
                (layerState.inRange != layer.inRange) ||
                (layerState.id != layer.id) ||
                (layerState.visibility != layer.visibility)) {
              return true;
            }
        }
        return false;
    },

    // Rebuild this layer switcher by clearing out its `$element` (aka `e`)
    // and rebuilding its DOM structure.
    redraw: function() {
      if (this.needsRedraw()) {
        // Clear out previous layers
        this.$element.html('');

        // Save state -- for checking layer if the map state changed.
        // We save this before redrawing, because in the process of redrawing
        // we will trigger more visibility changes, and we want to not redraw
        // and enter an infinite loop.
        var len = this.map.layers.length;
        this.layerStates = [];
        for (var i = 0; i < len; i++) {
          var layerState = this.map.layers[i];
          this.layerStates[i] = {
              name: layerState.name,
              visibility: layerState.visibility,
              inRange: layerState.inRange,
              id: layerState.id
          };
        }

        var layers = this.map.layers.slice();
        for (i = 0, len = layers.length; i < len; i++) {
          var layer = layers[i];
          if (layer.displayInLayerSwitcher) {
            // Only check a baselayer if it is *the* baselayer, check data layers if they are visible
            var checked = layer.isBaseLayer ? (layer === this.map.baseLayer) : layer.getVisibility();
            var clickLayer = $.proxy(function(e) { this.layerClick(e); return false; }, this);
            var $layer_element = $('<a></a>');
            // Add states and click handler
            $layer_element
                .click(clickLayer)
                .attr('href', '#')
                .text(layer.name)
                .addClass('layer-toggle')
                .data('layer', layer)
                .attr('disabled', !layer.inRange);
                if (checked) {
                  $layer_element.addClass('active');
                }
            }
            this.$element.append($layer_element);
            // Trigger a `layeradded` event on the element we own. This is
            // the way to style layer switcher elements: attach a listener
            // to this event, and then modify on addition.
            this.$element.trigger('layeradded', $layer_element);
          }
        }
    }
});
