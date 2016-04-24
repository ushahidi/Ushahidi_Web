/*! 
 * Timemap.js Copyright 2008 Nick Rabinowitz.
 * Licensed under the MIT License (see LICENSE.txt)
 */

/**
 * @overview
 *
 * <p>Timemap.js is intended to sync a SIMILE Timeline with a web-based map. 
 * Thanks to Jorn Clausen (http://www.oe-files.de) for initial concept and code.
 * Timemap.js is licensed under the MIT License (see <a href="../LICENSE.txt">LICENSE.txt</a>).</p>
 * <p><strong>Depends on:</strong> 
 *         <a href="http://jquery.com">jQuery</a>, 
 *         <a href="https://github.com/nrabinowitz/mxn"> a customized version of Mapstraction 2.x<a>, 
 *          a map provider of your choice, <a href="code.google.com/p/simile-widgets">SIMILE Timeline v1.2 - 2.3.1.</a>
 * </p>
 * <p><strong>Tested browsers:</strong> Firefox 3.x, Google Chrome, IE7, IE8</p>
 * <p><strong>Tested map providers:</strong> 
 *          <a href="http://code.google.com/apis/maps/documentation/javascript/v2/reference.html">Google v2</a>, 
 *          <a href="http://code.google.com/apis/maps/documentation/javascript/reference.html">Google v3</a>, 
 *          <a href="http://openlayers.org">OpenLayers</a>, 
 *          <a href="http://msdn.microsoft.com/en-us/library/bb429565.aspx">Bing Maps</a>
 * </p>
 * <ul>
 *     <li><a href="http://code.google.com/p/timemap/">Project Homepage</a></li>
 *     <li><a href="http://groups.google.com/group/timemap-development">Discussion Group</a></li>
 *     <li><a href="../examples/index.html">Working Examples</a></li>
 * </ul>
 *
 * @name timemap.js
 * @author Nick Rabinowitz (www.nickrabinowitz.com)
 * @version 2.0.1
 */

// for jslint

(function(){
// borrowing some space-saving devices from jquery
var 
    // Will speed up references to window, and allows munging its name.
    window = this,
    // Will speed up references to undefined, and allows munging its name.
    undefined,
    // aliases for Timeline objects
    Timeline = window.Timeline, DateTime = Timeline.DateTime, 
    // alias libraries
    $ = window.jQuery,
    mxn = window.mxn,
    // alias Mapstraction classes
    Mapstraction = mxn.Mapstraction,
    LatLonPoint = mxn.LatLonPoint,
    BoundingBox = mxn.BoundingBox,
    Marker = mxn.Marker,
    Polyline = mxn.Polyline,
    // events
    E_ITEMS_LOADED = 'itemsloaded',
    // Google icon path
    GIP = "http://www.google.com/intl/en_us/mapfiles/ms/icons/",
    // aliases for class names, allowing munging
    TimeMap, TimeMapFilterChain, TimeMapDataset, TimeMapTheme, TimeMapItem;

/*----------------------------------------------------------------------------
 * TimeMap Class
 *---------------------------------------------------------------------------*/
 
/**
 * @class
 * The TimeMap object holds references to timeline, map, and datasets.
 *
 * @constructor
 * This will create the visible map, but not the timeline, which must be initialized separately.
 *
 * @param {DOM Element} tElement     The timeline element.
 * @param {DOM Element} mElement     The map element.
 * @param {Object} [options]       A container for optional arguments
 * @param {TimeMapTheme|String} [options.theme=red] Color theme for the timemap
 * @param {Boolean} [options.syncBands=true]    Whether to synchronize all bands in timeline
 * @param {LatLonPoint} [options.mapCenter=0,0] Point for map center
 * @param {Number} [options.mapZoom=0]          Initial map zoom level
 * @param {String} [options.mapType=physical]   The maptype for the map (see {@link TimeMap.mapTypes} for options)
 * @param {Function|String} [options.mapFilter={@link TimeMap.filters.hidePastFuture}] 
 *                                              How to hide/show map items depending on timeline state;
 *                                              options: keys in {@link TimeMap.filters} or function. Set to 
 *                                              null or false for no filter.
 * @param {Boolean} [options.showMapTypeCtrl=true]  Whether to display the map type control
 * @param {Boolean} [options.showMapCtrl=true]      Whether to show map navigation control
 * @param {Boolean} [options.centerOnItems=true] Whether to center and zoom the map based on loaded item 
 * @param {String} [options.eventIconPath]      Path for directory holding event icons; if set at the TimeMap
 *                                              level, will override dataset and item defaults
 * @param {Boolean} [options.checkResize=true]  Whether to update the timemap display when the window is 
 *                                              resized. Necessary for fluid layouts, but might be better set to
 *                                              false for absolutely-sized timemaps to avoid extra processing
 * @param {Boolean} [options.multipleInfoWindows=false] Whether to allow multiple simultaneous info windows for 
 *                                              map providers that allow this (Google v3, OpenLayers)
 * @param {mixed} [options[...]]                Any of the options for {@link TimeMapDataset}, 
 *                                              {@link TimeMapItem}, or {@link TimeMapTheme} may be set here,
 *                                              to cascade to the entire TimeMap, though they can be overridden
 *                                              at lower levels
 * </pre>
 */
TimeMap = function(tElement, mElement, options) {
    var tm = this,
        // set defaults for options
        defaults = {
            mapCenter:          new LatLonPoint(0,0),
            mapZoom:            0,
            mapType:            'physical',
            showMapTypeCtrl:    true,
            showMapCtrl:        true,
            syncBands:          true,
            mapFilter:          'hidePastFuture',
            centerOnItems:      true,
            theme:              'red',
            dateParser:         'hybrid',
            checkResize:        true,
            multipleInfoWindows:false
        }, 
        mapCenter;
    
    // save DOM elements
    /**
     * Map element
     * @name TimeMap#mElement
     * @type DOM Element
     */
    tm.mElement = mElement;
    /**
     * Timeline element
     * @name TimeMap#tElement
     * @type DOM Element
     */
    tm.tElement = tElement;
    
    /** 
     * Map of datasets 
     * @name TimeMap#datasets
     * @type Object 
     */
    tm.datasets = {};
    /**
     * Filter chains for this timemap 
     * @name TimeMap#chains
     * @type Object
     */
    tm.chains = {};
    
    /** 
     * Container for optional settings passed in the "options" parameter
     * @name TimeMap#opts
     * @type Object
     */
    tm.opts = options = $.extend(defaults, options);
    
    // allow map center to be specified as a point object
    mapCenter = options.mapCenter;
    if (mapCenter.constructor != LatLonPoint && mapCenter.lat) {
        options.mapCenter = new LatLonPoint(mapCenter.lat, mapCenter.lon);
    }
    // allow map types to be specified by key
    options.mapType = util.lookup(options.mapType, TimeMap.mapTypes);
    // allow map filters to be specified by key
    options.mapFilter = util.lookup(options.mapFilter, TimeMap.filters);
    // allow theme options to be specified in options
    options.theme = TimeMapTheme.create(options.theme, options);
    
    // initialize map
    tm.initMap();
};

// STATIC FIELDS

/**
 * Current library version.
 * @constant
 * @type String
 */
TimeMap.version = "2.0.1";

/**
 * @name TimeMap.util
 * @namespace
 * Namespace for TimeMap utility functions.
 */
var util = TimeMap.util = {};

// STATIC METHODS

/**
 * Intializes a TimeMap.
 *
 * <p>The idea here is to throw all of the standard intialization settings into
 * a large object and then pass it to the TimeMap.init() function. The full
 * data format is outlined below, but if you leave elements out the script 
 * will use default settings instead.</p>
 *
 * <p>See the examples and the 
 * <a href="http://code.google.com/p/timemap/wiki/UsingTimeMapInit">UsingTimeMapInit wiki page</a>
 * for usage.</p>
 *
 * @param {Object} config                           Full set of configuration options.
 * @param {String} [config.mapId]                   DOM id of the element to contain the map.
 *                                                  Either this or mapSelector is required.
 * @param {String} [config.timelineId]              DOM id of the element to contain the timeline.
 *                                                  Either this or timelineSelector is required.
 * @param {String} [config.mapSelector]             jQuery selector for the element to contain the map.
 *                                                  Either this or mapId is required.
 * @param {String} [config.timelineSelector]        jQuery selector for the element to contain the timeline.
 *                                                  Either this or timelineId is required.
 * @param {Object} [config.options]                 Options for the TimeMap object (see the {@link TimeMap} constructor)
 * @param {Object[]} config.datasets                Array of datasets to load
 * @param {Object} config.datasets[x]               Configuration options for a particular dataset
 * @param {String|Class} config.datasets[x].type    Loader type for this dataset (generally a sub-class 
 *                                                  of {@link TimeMap.loaders.base})
 * @param {Object} config.datasets[x].options       Options for the loader. See the {@link TimeMap.loaders.base}
 *                                                  constructor and the constructors for the various loaders for 
 *                                                  more details.
 * @param {String} [config.datasets[x].id]          Optional id for the dataset in the {@link TimeMap#datasets}
 *                                                  object, for future reference; otherwise "ds"+x is used
 * @param {String} [config.datasets[x][...]]        Other options for the {@link TimeMapDataset} object
 * @param {String|Array} [config.bandIntervals=wk]  Intervals for the two default timeline bands. Can either be an 
 *                                                  array of interval constants or a key in {@link TimeMap.intervals}
 * @param {Object[]} [config.bandInfo]              Array of configuration objects for Timeline bands, to be passed to
 *                                                  Timeline.createBandInfo (see the <a href="http://code.google.com/p/simile-widgets/wiki/Timeline_GettingStarted">Timeline Getting Started tutorial</a>).
 *                                                  This will override config.bandIntervals, if provided.
 * @param {Timeline.Band[]} [config.bands]          Array of instantiated Timeline Band objects. This will override
 *                                                  config.bandIntervals and config.bandInfo, if provided.
 * @param {Function} [config.dataLoadedFunction]    Function to be run as soon as all datasets are loaded, but
 *                                                  before they've been displayed on the map and timeline
 *                                                  (this will override dataDisplayedFunction and scrollTo)
 * @param {Function} [config.dataDisplayedFunction] Function to be run as soon as all datasets are loaded and 
 *                                                  displayed on the map and timeline
 * @param {String|Date} [config.scrollTo=earliest]  Date to scroll to once data is loaded - see 
 *                                                  {@link TimeMap.parseDate} for options
 * @return {TimeMap}                                The initialized TimeMap object
 */
TimeMap.init = function(config) {
    var err = "TimeMap.init: Either %Id or %Selector is required",    
        // set defaults
        defaults = {
            options:        {},
            datasets:       [],
            bands:          false,
            bandInfo:       false,
            bandIntervals:  "wk",
            scrollTo:       "earliest"
        },
        state = TimeMap.state,
        intervals, tm,
        datasets = [], x, dsOptions, topOptions, dsId,
        bands = [], eventSource;
    
    // get DOM element selectors
    config.mapSelector = config.mapSelector || '#' + config.mapId;
    config.timelineSelector = config.timelineSelector || '#' + config.timelineId;
    
    // get state from url hash if state functions are available
    if (state) {
        state.setConfigFromUrl(config);
    }
    // merge options and defaults
    config = $.extend(defaults, config);

    if (!config.bandInfo && !config.bands) {
        // allow intervals to be specified by key
        intervals = util.lookup(config.bandIntervals, TimeMap.intervals);
        // make default band info
        config.bandInfo = [
            {
                width:          "80%", 
                intervalUnit:   intervals[0], 
                intervalPixels: 70
            },
            {
                width:          "20%", 
                intervalUnit:   intervals[1], 
                intervalPixels: 100,
                showEventText:  false,
                overview:       true,
                trackHeight:    0.4,
                trackGap:       0.2
            }
        ];
    }
    
    // create the TimeMap object
    tm = new TimeMap(
        $(config.timelineSelector).get(0), 
        $(config.mapSelector).get(0),
        config.options
    );
    
    // create the dataset objects
    config.datasets.forEach(function(ds, x) {
        // put top-level data into options
        dsOptions = $.extend({
            title: ds.title,
            theme: ds.theme,
            dateParser: ds.dateParser
        }, ds.options);
        dsId = ds.id || "ds" + x;
        datasets[x] = tm.createDataset(dsId, dsOptions);
        if (x > 0) {
            // set all to the same eventSource
            datasets[x].eventSource = datasets[0].eventSource;
        }
    });
    // add a pointer to the eventSource in the TimeMap
    tm.eventSource = datasets[0].eventSource;
    
    // set up timeline bands
    // ensure there's at least an empty eventSource
    eventSource = (datasets[0] && datasets[0].eventSource) || new Timeline.DefaultEventSource();
    // check for pre-initialized bands (manually created with Timeline.createBandInfo())
    if (config.bands) {
        config.bands.forEach(function(band) {
            // substitute dataset event source
            // assume that these have been set up like "normal" Timeline bands:
            // with an empty event source if events are desired, and null otherwise
            if (band.eventSource !== null) {
                band.eventSource = eventSource;
            }
        });
        bands = config.bands;
    }
    // otherwise, make bands from band info
    else {
        config.bandInfo.forEach(function(bandInfo, x) {
            // if eventSource is explicitly set to null or false, ignore
            if (!(('eventSource' in bandInfo) && !bandInfo.eventSource)) {
                bandInfo.eventSource = eventSource;
            }
            else {
                bandInfo.eventSource = null;
            }
            bands[x] = Timeline.createBandInfo(bandInfo);
            if (x > 0 && util.TimelineVersion() == "1.2") {
                // set all to the same layout
                bands[x].eventPainter.setLayout(bands[0].eventPainter.getLayout()); 
            }
        });
    }
    // initialize timeline
    tm.initTimeline(bands);
    
    // initialize load manager
    var loadManager = TimeMap.loadManager,
        callback = function() { 
            loadManager.increment(); 
        };
    loadManager.init(tm, config.datasets.length, config);
    
    // load data!
    config.datasets.forEach(function(data, x) {
        var dataset = datasets[x], 
            options = data.data || data.options || {}, 
            type = data.type || options.type,
            // get loader class
            loaderClass = (typeof type == 'string') ? TimeMap.loaders[type] : type,
            // load with appropriate loader
            loader = new loaderClass(options);
        loader.load(dataset, callback);
    });
    // return timemap object for later manipulation
    return tm;
};


// METHODS

TimeMap.prototype = {

    /**
     *
     * Initialize the map.
     */
    initMap: function() {
        var tm = this,
            options = tm.opts, 
            map, i;
        
        /**
         * The Mapstraction object
         * @name TimeMap#map
         * @type Mapstraction
         */
        tm.map = map = new Mapstraction(tm.mElement, options.mapProvider);

        // display the map centered on a latitude and longitude
        map.setCenterAndZoom(options.mapCenter, options.mapZoom);
        
        // set default controls and map type
        map.addControls({
            pan: options.showMapCtrl, 
            zoom: options.showMapCtrl ? 'large' : false,
            map_type: options.showMapTypeCtrl
        });
        map.setMapType(options.mapType);
        
        // allow multiple windows if desired
        if (options.multipleInfoWindows) {
            map.setOption('enableMultipleBubbles', true);
        }
        
        /**
         * Return the native map object (specific to the map provider)
         * @name TimeMap#getNativeMap
         * @function
         * @return {Object}     The native map object (e.g. GMap2)
         */
        tm.getNativeMap = function() { return map.getMap(); };

    },

    /**
     * Initialize the timeline - this must happen separately to allow full control of 
     * timeline properties.
     *
     * @param {BandInfo Array} bands    Array of band information objects for timeline
     */
    initTimeline: function(bands) {
        var tm = this, timeline,
            opts = tm.opts,
            // filter: hide when item is hidden
            itemVisible = function(item) {
                return item.visible;
            },
            // filter: hide when dataset is hidden
            datasetVisible = function(item) {
                return item.dataset.visible;
            },
            // handler to open item window
            eventClickHandler = function(x, y, evt) {
                evt.item.openInfoWindow();
            },
            resizeTimerID, x, painter;
        
        // synchronize & highlight timeline bands
        for (x=1; x < bands.length; x++) {
            if (opts.syncBands) {
                bands[x].syncWith = 0;
            }
            bands[x].highlight = true;
        }
        
        /** 
         * The associated timeline object 
         * @name TimeMap#timeline
         * @type Timeline 
         */
        tm.timeline = timeline = Timeline.create(tm.tElement, bands);

        // hijack timeline popup window to open info window
        for (x=0; x < timeline.getBandCount(); x++) {
            painter = timeline.getBand(x).getEventPainter().constructor;
            painter.prototype._showBubble = eventClickHandler;
        }
        
        // filter chain for map placemarks
        tm.addFilterChain("map",
            // on
            function(item) {
                item.showPlacemark();
            },
            // off
            function(item) {
                item.hidePlacemark();
            },
            // pre/post
            null, null,
            // initial chain
            [itemVisible, datasetVisible]
        );
        
        // filter: hide map items depending on timeline state
        if (opts.mapFilter) {
            tm.addFilter("map", opts.mapFilter);
            // update map on timeline scroll
            timeline.getBand(0).addOnScrollListener(function() {
                tm.filter("map");
            });
        }
        
        // filter chain for timeline events
        tm.addFilterChain("timeline", 
            // on
            function(item) {
                item.showEvent();
            },
            // off
            function(item) {
                item.hideEvent();
            },
            // pre
            null,
            // post
            function() {
                // XXX: needed if we go to Timeline filtering?
                tm.eventSource._events._index();
                timeline.layout();
            },
            // initial chain
            [itemVisible, datasetVisible]
        );
        
        // filter: hide timeline items depending on map state
        if (opts.timelineFilter) {
            tm.addFilter("map", opts.timelineFilter);
        }
        
        // add callback for window resize, if necessary
        if (opts.checkResize) {
            window.onresize = function() {
                if (!resizeTimerID) {
                    resizeTimerID = window.setTimeout(function() {
                        resizeTimerID = null;
                        timeline.layout();
                    }, 500);
                }
            };
        }
    },

    /**
     * Parse a date in the context of the timeline. Uses the standard parser
     * ({@link TimeMap.dateParsers.hybrid}) but accepts "now", "earliest", 
     * "latest", "first", and "last" (referring to loaded events)
     *
     * @param {String|Date} s   String (or date) to parse
     * @return {Date}           Parsed date
     */
    parseDate: function(s) {
        var d = new Date(),
            eventSource = this.eventSource,
            parser = TimeMap.dateParsers.hybrid,
            // make sure there are events to scroll to
            hasEvents = eventSource.getCount() > 0 ? true : false;
        switch (s) {
            case "now":
                break;
            case "earliest":
            case "first":
                if (hasEvents) {
                    d = eventSource.getEarliestDate();
                }
                break;
            case "latest":
            case "last":
                if (hasEvents) {
                    d = eventSource.getLatestDate();
                }
                break;
            default:
                // assume it's a date, try to parse
                d = parser(s);
        }
        return d;
    },

    /**
     * Scroll the timeline to a given date. If lazyLayout is specified, this function
     * will also call timeline.layout(), but only if it won't be called by the 
     * onScroll listener. This involves a certain amount of reverse engineering,
     * and may not be future-proof.
     *
     * @param {String|Date} d           Date to scroll to (either a date object, a 
     *                                  date string, or one of the strings accepted 
     *                                  by TimeMap#parseDate)
     * @param {Boolean} [lazyLayout]    Whether to call timeline.layout() if not
     *                                  required by the scroll.
     * @param {Boolean} [animated]      Whether to do an animated scroll, rather than a jump.
     */
    scrollToDate: function(d, lazyLayout, animated) {
        var timeline = this.timeline,
            topband = timeline.getBand(0),
            x, time, layouts = [],
            band, minTime, maxTime;
        d = this.parseDate(d);
        if (d) {
            time = d.getTime();
            // check which bands will need layout after scroll
            for (x=0; x < timeline.getBandCount(); x++) {
                band = timeline.getBand(x);
                minTime = band.getMinDate().getTime();
                maxTime = band.getMaxDate().getTime();
                layouts[x] = (lazyLayout && time > minTime && time < maxTime);
            }
            // do scroll
            if (animated) {
                // create animation
                var provider = util.TimelineVersion() == '1.2' ? Timeline : SimileAjax,
                    a = provider.Graphics.createAnimation(function(abs, diff) {
                        topband.setCenterVisibleDate(new Date(abs));
                    }, topband.getCenterVisibleDate().getTime(), time, 1000);
                a.run();
            }
            else {
                topband.setCenterVisibleDate(d);
            }
            // layout as necessary
            for (x=0; x < layouts.length; x++) {
                if (layouts[x]) {
                    timeline.getBand(x).layout();
                }
            }
        } 
        // layout if requested even if no date is found
        else if (lazyLayout) {
            timeline.layout();
        }
    },

    /**
     * Create an empty dataset object and add it to the timemap
     *
     * @param {String} id           The id of the dataset
     * @param {Object} options      A container for optional arguments for dataset constructor -
     *                              see the options passed to {@link TimeMapDataset}
     * @return {TimeMapDataset}     The new dataset object    
     */
    createDataset: function(id, options) {
        var tm = this,
            dataset = new TimeMapDataset(tm, options);
        // save id reference
        dataset.id = id;
        tm.datasets[id] = dataset;
        // add event listener
        if (tm.opts.centerOnItems) {
            var map = tm.map;
            $(dataset).bind(E_ITEMS_LOADED, function() {
                // determine the center and zoom level from the bounds
                map.autoCenterAndZoom();
            });
        }
        return dataset;
    },

    /**
     * Run a function on each dataset in the timemap. This is the preferred
     * iteration method, as it allows for future iterator options.
     *
     * @param {Function} f    The function to run, taking one dataset as an argument
     */
    each: function(f) {
        var tm = this, 
            id;
        for (id in tm.datasets) {
            if (tm.datasets.hasOwnProperty(id)) {
                f(tm.datasets[id]);
            }
        }
    },

    /**
     * Run a function on each item in each dataset in the timemap.
     * @param {Function} f    The function to run, taking one item as an argument
     */
    eachItem: function(f) {
        this.each(function(ds) {
            ds.each(function(item) {
                f(item);
            });
        });
    },

    /**
     * Get all items from all datasets.
     * @return {TimeMapItem[]}  Array of all items
     */
    getItems: function() {
        var items = [];
        this.each(function(ds) {
            items = items.concat(ds.items);
        });
        return items;
    },
    
    /**
     * Save the currently selected item
     * @param {TimeMapItem|String} item     Item to select, or undefined
     *                                      to clear selection
     */
    setSelected: function(item) {
        this.opts.selected = item;
    },
    
    /**
     * Get the currently selected item
     * @return {TimeMapItem} Selected item
     */
    getSelected: function() {
        return this.opts.selected;
    },
    
    // Helper functions for dealing with filters
    
    /**
     * Update items, hiding or showing according to filters
     * @param {String} chainId  Filter chain to update on
     */
    filter: function(chainId) {
        var fc = this.chains[chainId];
        if (fc) {
            fc.run();
        }  
    },

    /**
     * Add a new filter chain
     *
     * @param {String} chainId      Id of the filter chain
     * @param {Function} fon        Function to run on an item if filter is true
     * @param {Function} foff       Function to run on an item if filter is false
     * @param {Function} [pre]      Function to run before the filter runs
     * @param {Function} [post]     Function to run after the filter runs
     * @param {Function[]} [chain]  Optional initial filter chain
     */
    addFilterChain: function(chainId, fon, foff, pre, post, chain) {
        this.chains[chainId] = new TimeMapFilterChain(this, fon, foff, pre, post, chain);
    },

    /**
     * Remove a filter chain
     *
     * @param {String} chainId  Id of the filter chain
     */
    removeFilterChain: function(chainId) {
        delete this.chains[chainId];
    },

    /**
     * Add a function to a filter chain
     *
     * @param {String} chainId  Id of the filter chain
     * @param {Function} f      Function to add
     */
    addFilter: function(chainId, f) {
        var fc = this.chains[chainId];
        if (fc) {
            fc.add(f);
        }
    },

    /**
     * Remove a function from a filter chain
     *
     * @param {String} chainId  Id of the filter chain
     * @param {Function} [f]    The function to remove
     */
    removeFilter: function(chainId, f) {
        var fc = this.chains[chainId];
        if (fc) {
            fc.remove(f);
        }
    }

};

/*----------------------------------------------------------------------------
 * Load manager
 *---------------------------------------------------------------------------*/

/**
 * @class Static singleton for managing multiple asynchronous loads
 */
TimeMap.loadManager = new function() {
    var mgr = this;
    
    /**
     * Initialize (or reset) the load manager
     * @name TimeMap.loadManager#init
     * @function
     *
     * @param {TimeMap} tm          TimeMap instance
     * @param {Number} target       Number of datasets we're loading
     * @param {Object} [options]    Container for optional settings
     * @param {Function} [options.dataLoadedFunction]
     *                                      Custom function replacing default completion function;
     *                                      should take one parameter, the TimeMap object
     * @param {String|Date} [options.scrollTo]
     *                                      Where to scroll the timeline when load is complete
     *                                      Options: "earliest", "latest", "now", date string, Date
     * @param {Function} [options.dataDisplayedFunction]   
     *                                      Custom function to fire once data is loaded and displayed;
     *                                      should take one parameter, the TimeMap object
     */
    mgr.init = function(tm, target, config) {
        mgr.count = 0;
        mgr.tm = tm;
        mgr.target = target;
        mgr.opts = config || {};
    };
    
    /**
     * Increment the count of loaded datasets
     * @name TimeMap.loadManager#increment
     * @function
     */
    mgr.increment = function() {
        mgr.count++;
        if (mgr.count >= mgr.target) {
            mgr.complete();
        }
    };
    
    /**
     * Function to fire when all loads are complete. 
     * Default behavior is to scroll to a given date (if provided) and
     * layout the timeline.
     * @name TimeMap.loadManager#complete
     * @function
     */
    mgr.complete = function() {
        var tm = mgr.tm,
            opts = mgr.opts,
            // custom function including timeline scrolling and layout
            func = opts.dataLoadedFunction;
        if (func) {
            func(tm);
        } 
        else {
            tm.scrollToDate(opts.scrollTo, true);
            // check for state support
            if (tm.initState) {
                tm.initState();
            }
            // custom function to be called when data is loaded
            func = opts.dataDisplayedFunction;
            if (func) {
                func(tm);
            }
        }
    };
}();

/*----------------------------------------------------------------------------
 * Loader namespace and base classes
 *---------------------------------------------------------------------------*/
 
/**
 * @namespace
 * Namespace for different data loader functions.
 * New loaders can add their factories or constructors to this object; loader
 * functions are passed an object with parameters in TimeMap.init().
 *
 * @example
    TimeMap.init({
        datasets: [
            {
                // name of class in TimeMap.loaders
                type: "json_string",
                options: {
                    url: "mydata.json"
                },
                // etc...
            }
        ],
        // etc...
    });
 */
TimeMap.loaders = {

    /**
     * @namespace
     * Namespace for storing callback functions
     * @private
     */
    cb: {},
    
    /**
     * Cancel a specific load request. In practice, this is really only
     * applicable to remote asynchronous loads. Note that this doesn't cancel 
     * the download of data, just the callback that loads it.
     * @param {String} callbackName     Name of the callback function to cancel
     */
    cancel: function(callbackName) {
        var namespace = TimeMap.loaders.cb;
        // replace with self-cancellation function
        namespace[callbackName] = function() {
            delete namespace[callbackName];
        };
    },
    
    /**
     * Cancel all current load requests.
     */
    cancelAll: function() {
        var loaderNS = TimeMap.loaders,
            namespace = loaderNS.cb,
            callbackName;
        for (callbackName in namespace) {
            if (namespace.hasOwnProperty(callbackName)) {
                loaderNS.cancel(callbackName);
            }
        }
    },
    
    /**
     * Static counter for naming callback functions
     * @private
     * @type int
     */
    counter: 0,

    /**
     * @class
     * Abstract loader class. All loaders should inherit from this class.
     *
     * @constructor
     * @param {Object} options          All options for the loader
     * @param {Function} [options.parserFunction=Do nothing]   
     *                                      Parser function to turn a string into a JavaScript array
     * @param {Function} [options.preloadFunction=Do nothing]      
     *                                      Function to call on data before loading
     * @param {Function} [options.transformFunction=Do nothing]    
     *                                      Function to call on individual items before loading
     * @param {String|Date} [options.scrollTo=earliest] Date to scroll the timeline to in the default callback 
     *                                                  (see {@link TimeMap#parseDate} for accepted syntax)
     */
    base: function(options) {
        var dummy = function(data) { return data; },
            loader = this;
         
        /**
         * Parser function to turn a string into a JavaScript array
         * @name TimeMap.loaders.base#parse
         * @function
         * @parameter {String} s        String to parse
         * @return {Object[]}           Array of item data
         */
        loader.parse = options.parserFunction || dummy;
        
        /**
         * Function to call on data object before loading
         * @name TimeMap.loaders.base#preload
         * @function
         * @parameter {Object} data     Data to preload
         * @return {Object[]}           Array of item data
         */
        loader.preload = options.preloadFunction || dummy;
        
        /**
         * Function to call on a single item data object before loading
         * @name TimeMap.loaders.base#transform
         * @function
         * @parameter {Object} data     Data to transform
         * @return {Object}             Transformed data for one item
         */
        loader.transform = options.transformFunction || dummy;
        
        /**
         * Date to scroll the timeline to on load
         * @name TimeMap.loaders.base#scrollTo
         * @default "earliest"
         * @type String|Date
         */
        loader.scrollTo = options.scrollTo || "earliest";
        
        /**
         * Get the name of a callback function that can be cancelled. This callback applies the parser,
         * preload, and transform functions, loads the data, then calls the user callback
         * @name TimeMap.loaders.base#getCallbackName
         * @function
         *
         * @param {TimeMapDataset} dataset  Dataset to load data into
         * @param {Function} callback       User-supplied callback function. If no function 
         *                                  is supplied, the default callback will be used
         * @return {String}                 The name of the callback function in TimeMap.loaders.cb
         */
        loader.getCallbackName = function(dataset, callback) {
            var callbacks = TimeMap.loaders.cb,
                // Define a unique function name
                callbackName = "_" + TimeMap.loaders.counter++;
            // Define default callback
            callback = callback || function() {
                dataset.timemap.scrollToDate(loader.scrollTo, true);
            };
            
            // create callback
            callbacks[callbackName] = function(result) {
                // parse
                var items = loader.parse(result);
                // preload
                items = loader.preload(items);
                // load
                dataset.loadItems(items, loader.transform);
                // callback
                callback(); 
                // delete the callback function
                delete callbacks[callbackName];
            };
            
            return callbackName;
        };
        
        /**
         * Get a callback function that can be cancelled. This is a convenience function
         * to be used if the callback name itself is not needed.
         * @name TimeMap.loaders.base#getCallback 
         * @function
         * @see TimeMap.loaders.base#getCallbackName
         *
         * @param {TimeMapDataset} dataset  Dataset to load data into
         * @param {Function} callback       User-supplied callback function
         * @return {Function}               The configured callback function
         */
        loader.getCallback = function(dataset, callback) {
            // get loader callback name
            var callbackName = loader.getCallbackName(dataset, callback);
            // return the function
            return TimeMap.loaders.cb[callbackName];
        };
        
        /**
         * Cancel the callback function for this loader.
         * @name TimeMap.loaders.base#cancel
         * @function
         */
        loader.cancel = function() {
            TimeMap.loaders.cancel(loader.callbackName);
        };
        
    }, 

    /**
     * @class
     * Basic loader class, for pre-loaded data. 
     * Other types of loaders should take the same parameter.
     *
     * @augments TimeMap.loaders.base
     * @example
TimeMap.init({
    datasets: [
        {
            type: "basic",
            options: {
                data: [
                    // object literals for each item
                    {
                        title: "My Item",
                        start: "2009-10-06",
                        point: {
                            lat: 37.824,
                            lon: -122.256
                        }
                    },
                    // etc...
                ]
            }
        }
    ],
    // etc...
});
     * @see <a href="../../examples/basic.html">Basic Example</a>
     *
     * @constructor
     * @param {Object} options          All options for the loader
     * @param {Array} options.data          Array of items to load
     * @param {mixed} [options[...]]        Other options (see {@link TimeMap.loaders.base})
     */
    basic: function(options) {
        var loader = new TimeMap.loaders.base(options);
        
        /**
         * Array of item data to load.
         * @name TimeMap.loaders.basic#data
         * @default []
         * @type Object[]
         */
        loader.data = options.items || 
            // allow "value" for backwards compatibility
            options.value || [];

        /**
         * Load javascript literal data.
         * New loaders should implement a load function with the same signature.
         * @name TimeMap.loaders.basic#load
         * @function
         *
         * @param {TimeMapDataset} dataset  Dataset to load data into
         * @param {Function} callback       Function to call once data is loaded
         */
        loader.load = function(dataset, callback) {
            // get callback function and call immediately on data
            (this.getCallback(dataset, callback))(this.data);
        };
        
        return loader;
    },

    /**
     * @class
     * Generic class for loading remote data with a custom parser function
     *
     * @augments TimeMap.loaders.base
     *
     * @constructor
     * @param {Object} options      All options for the loader
     * @param {String} options.url      URL of file to load (NB: must be local address)
     * @param {mixed} [options[...]]    Other options. In addition to options for 
     *                                  {@link TimeMap.loaders.base}), any option for 
     *                                  <a href="http://api.jquery.com/jQuery.ajax/">jQuery.ajax</a>
     *                                  may be set here
     */
    remote: function(options) {
        var loader = new TimeMap.loaders.base(options);
        
        /**
         * Object to hold optional settings. Any setting for 
         * <a href="http://api.jquery.com/jQuery.ajax/">jQuery.ajax</a> should be set on this
         * object before load() is called.
         * @name TimeMap.loaders.remote#opts
         * @type String
         */
        loader.opts = $.extend({}, options, {
            type: 'GET',
            dataType: 'text'
        });
        
        /**
         * Load function for remote files.
         * @name TimeMap.loaders.remote#load
         * @function
         *
         * @param {TimeMapDataset} dataset  Dataset to load data into
         * @param {Function} callback       Function to call once data is loaded
         */
        loader.load = function(dataset, callback) {
            // get loader callback name (allows cancellation)
            loader.callbackName = loader.getCallbackName(dataset, callback);
            // set the callback function
            loader.opts.success = TimeMap.loaders.cb[loader.callbackName];
            // download remote data
            $.ajax(loader.opts);
        };
        
        return loader;
    }
    
};

/*----------------------------------------------------------------------------
 * TimeMapFilterChain Class
 *---------------------------------------------------------------------------*/
 
/**
 * @class
 * TimeMapFilterChain holds a set of filters to apply to the map or timeline.
 *
 * @constructor
 * @param {TimeMap} timemap     Reference to the timemap object
 * @param {Function} fon        Function to run on an item if filter is true
 * @param {Function} foff       Function to run on an item if filter is false
 * @param {Function} [pre]      Function to run before the filter runs
 * @param {Function} [post]     Function to run after the filter runs
 * @param {Function[]} [chain]  Optional initial filter chain
 */
TimeMapFilterChain = function(timemap, fon, foff, pre, post, chain) {
    var fc = this,
        dummy = $.noop;
    /** 
     * Reference to parent TimeMap
     * @name TimeMapFilterChain#timemap
     * @type TimeMap
     */
    fc.timemap = timemap;
    
    /** 
     * Chain of filter functions, each taking an item and returning a boolean
     * @name TimeMapFilterChain#chain
     * @type Function[]
     */
    fc.chain = chain || [];
    
    /** 
     * Function to run on an item if filter is true
     * @name TimeMapFilterChain#on
     * @function
     */
    fc.on = fon || dummy;
    
    /** 
     * Function to run on an item if filter is false
     * @name TimeMapFilterChain#off
     * @function
     */
    fc.off = foff || dummy;
    
    /** 
     * Function to run before the filter runs
     * @name TimeMapFilterChain#pre
     * @function
     */
    fc.pre = pre || dummy;
    
    /** 
     * Function to run after the filter runs
     * @name TimeMapFilterChain#post
     * @function
     */
    fc.post = post || dummy;
};

// METHODS

TimeMapFilterChain.prototype = {

    /**
     * Add a filter to the filter chain.
     * @param {Function} f      Function to add
     */
    add: function(f) {
        return this.chain.push(f);
    },

    /**
     * Remove a filter from the filter chain
     * @param {Function} [f]    Function to remove; if not supplied, the last filter 
     *                          added is removed
     */
    remove: function(f) {
        var chain = this.chain, 
            i = f ? chain.indexOf(f) : chain.length - 1;
        // remove specific filter or last if none specified
        return chain.splice(i, 1);
    },

    /**
     * Run filters on all items
     */
    run: function() {
        var fc = this,
            chain = fc.chain;
        // early exit
        if (!chain.length) {
            return;
        }
        // pre-filter function
        fc.pre();
        // run items through filter
        fc.timemap.eachItem(function(item) {
            var done, 
                i = chain.length;
            L: while (!done) { 
                while (i--) {
                    if (!chain[i](item)) {
                        // false condition
                        fc.off(item);
                        break L;
                    }
                }
                // true condition
                fc.on(item);
                done = true;
            }
        });
        // post-filter function
        fc.post();
    }
    
};

/**
 * @namespace
 * Namespace for different filter functions. Adding new filters to this
 * namespace allows them to be specified by string name.
 * @example
    TimeMap.init({
        options: {
            mapFilter: "hideFuture"
        },
        // etc...
    });
 */
TimeMap.filters = {

    /**
     * Static filter function: Hide items not in the visible area of the timeline.
     *
     * @param {TimeMapItem} item    Item to test for filter
     * @return {Boolean}            Whether to show the item
     */
    hidePastFuture: function(item) {
        return item.onVisibleTimeline();
    },

    /**
     * Static filter function: Hide items later than the visible area of the timeline.
     *
     * @param {TimeMapItem} item    Item to test for filter
     * @return {Boolean}            Whether to show the item
     */
    hideFuture: function(item) {
        var maxVisibleDate = item.timeline.getBand(0).getMaxVisibleDate().getTime(),
            itemStart = item.getStartTime();
        if (itemStart !== undefined) {
            // hide items in the future
            return itemStart < maxVisibleDate;
        }
        return true;
    },

    /**
     * Static filter function: Hide items not present at the exact
     * center date of the timeline (will only work for duration events).
     *
     * @param {TimeMapItem} item    Item to test for filter
     * @return {Boolean}            Whether to show the item
     */
    showMomentOnly: function(item) {
        var topband = item.timeline.getBand(0),
            momentDate = topband.getCenterVisibleDate().getTime(),
            itemStart = item.getStartTime(),
            itemEnd = item.getEndTime();
        if (itemStart !== undefined) {
            // hide items in the future
            return itemStart < momentDate &&
                // hide items in the past
                (itemEnd > momentDate || itemStart > momentDate);
        }
        return true;
    }

};


/*----------------------------------------------------------------------------
 * TimeMapDataset Class
 *---------------------------------------------------------------------------*/

/**
 * @class 
 * The TimeMapDataset object holds an array of items and dataset-level
 * options and settings, including visual themes.
 *
 * @constructor
 * @param {TimeMap} timemap         Reference to the timemap object
 * @param {Object} [options]        Object holding optional arguments
 * @param {String} [options.id]                     Key for this dataset in the datasets map
 * @param {String} [options.title]                  Title of the dataset (for the legend)
 * @param {String|TimeMapTheme} [options.theme]     Theme settings.
 * @param {String|Function} [options.dateParser]    Function to replace default date parser.
 * @param {Boolean} [options.noEventLoad=false]     Whether to skip loading events on the timeline
 * @param {Boolean} [options.noPlacemarkLoad=false] Whether to skip loading placemarks on the map
 * @param {String} [options.infoTemplate]       HTML for the info window content, with variable expressions
 *                                              (as "{{varname}}" by default) to be replaced by option data
 * @param {String} [options.templatePattern]    Regex pattern defining variable syntax in the infoTemplate
 * @param {mixed} [options[...]]                Any of the options for {@link TimeMapItem} or 
 *                                              {@link TimeMapTheme} may be set here, to cascade to 
 *                                              the dataset's objects, though they can be 
 *                                              overridden at the TimeMapItem level
 */
TimeMapDataset = function(timemap, options) {
    var ds = this;

    /** 
     * Reference to parent TimeMap
     * @name TimeMapDataset#timemap
     * @type TimeMap
     */
    ds.timemap = timemap;
    
    /** 
     * EventSource for timeline events
     * @name TimeMapDataset#eventSource
     * @type Timeline.EventSource
     */
    ds.eventSource = new Timeline.DefaultEventSource();
    
    /** 
     * Array of child TimeMapItems
     * @name TimeMapDataset#items
     * @type Array
     */
    ds.items = [];
    
    /** 
     * Whether the dataset is visible
     * @name TimeMapDataset#visible
     * @type Boolean
     */
    ds.visible = true;
        
    /** 
     * Container for optional settings passed in the "options" parameter
     * @name TimeMapDataset#opts
     * @type Object
     */
    ds.opts = options = $.extend({}, timemap.opts, options);
    
    // allow date parser to be specified by key
    options.dateParser = util.lookup(options.dateParser, TimeMap.dateParsers);
    // allow theme options to be specified in options
    options.theme = TimeMapTheme.create(options.theme, options);
};

TimeMapDataset.prototype = {
    
    /**
     * Return an array of this dataset's items
     * @param {Number} [index]     Index of single item to return
     * @return {TimeMapItem[]}  Single item, or array of all items if no index was supplied
     */
    getItems: function(index) {
        var items = this.items;
        return index === undefined ? items : 
            index in items ? items[index] : null;
    },
    
    /**
     * Return the title of the dataset
     * @return {String}     Dataset title
     */
    getTitle: function() { 
        return this.opts.title; 
    },

    /**
     * Run a function on each item in the dataset. This is the preferred
     * iteration method, as it allows for future iterator options.
     *
     * @param {Function} f    The function to run
     */
    each: function(f) {
        this.items.forEach(f);
    },

    /**
     * Add an array of items to the map and timeline.
     *
     * @param {Object[]} data           Array of data to be loaded
     * @param {Function} [transform]    Function to transform data before loading
     * @see TimeMapDataset#loadItem
     */
    loadItems: function(data, transform) {
		if (data) {
			var ds = this;
			data.forEach(function(item) {
				ds.loadItem(item, transform);
			});
			$(ds).trigger(E_ITEMS_LOADED);
		}
    },

    /**
     * Add one item to map and timeline. 
     * Each item has both a timeline event and a map placemark.
     *
     * @param {Object} data         Data to be loaded - see the {@link TimeMapItem} constructor for details
     * @param {Function} [transform]        If data is not in the above format, transformation function to make it so
     * @return {TimeMapItem}                The created item (for convenience, as it's already been added)
     * @see TimeMapItem
     */
    loadItem: function(data, transform) {
        // apply transformation, if any
        if (transform) {
            data = transform(data);
        }
        // transform functions can return a false value to skip a datum in the set
        if (data) {
            // create new item, cascading options
            var ds = this, item;
            data.options = $.extend({}, ds.opts, {title:null}, data.options);
            item = new TimeMapItem(data, ds);
            // add the item to the dataset
            ds.items.push(item);
            // return the item object
            return item;
        }
    }

};

/*----------------------------------------------------------------------------
 * TimeMapTheme Class
 *---------------------------------------------------------------------------*/

/**
 * @class 
 * Predefined visual themes for datasets, defining colors and images for
 * map markers and timeline events. Note that theme is only used at creation
 * time - updating the theme of an existing object won't do anything.
 *
 * @constructor
 * @param {Object} [options]        A container for optional arguments
 * @param {String} [options.icon=http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png]
 *                                                      Icon image for marker placemarks
 * @param {Number[]} [options.iconSize=[32,32]]         Array of two integers indicating marker icon size as
 *                                                      [width, height] in pixels
 * @param {String} [options.iconShadow=http://www.google.com/intl/en_us/mapfiles/ms/icons/msmarker.shadow.png]
 *                                                      Icon image for marker placemarks
 * @param {Number[]} [options.iconShadowSize=[59,32]]   Array of two integers indicating marker icon shadow
 *                                                      size as [width, height] in pixels
 * @param {Number[]} [options.iconAnchor=[16,33]]       Array of two integers indicating marker icon anchor
 *                                                      point as [xoffset, yoffset] in pixels
 * @param {String} [options.color=#FE766A]              Default color in hex for events, polylines, polygons.
 * @param {String} [options.lineColor=color]            Color for polylines/polygons.
 * @param {Number} [options.lineOpacity=1]              Opacity for polylines/polygons.
 * @param {Number} [options.lineWeight=2]               Line weight in pixels for polylines/polygons.
 * @param {String} [options.fillColor=color]            Color for polygon fill.
 * @param {String} [options.fillOpacity=0.25]           Opacity for polygon fill.
 * @param {String} [options.eventColor=color]           Background color for duration events.
 * @param {String} [options.eventTextColor=null]        Text color for events (null=Timeline default).
 * @param {String} [options.eventIconPath=timemap/images/]  Path to instant event icon directory.
 * @param {String} [options.eventIconImage=red-circle.gif]  Filename of instant event icon image.
 * @param {URL} [options.eventIcon=eventIconPath+eventIconImage] URL for instant event icons.
 * @param {Boolean} [options.classicTape=false]         Whether to use the "classic" style timeline event tape
 *                                                      (needs additional css to work - see examples/artists.html).
 */
TimeMapTheme = function(options) {

    // work out various defaults - the default theme is Google's reddish color
    var defaults = {
        /** Default color in hex
         * @name TimeMapTheme#color 
         * @type String */
        color:          "#FE766A",
        /** Opacity for polylines/polygons
         * @name TimeMapTheme#lineOpacity 
         * @type Number */
        lineOpacity:    1,
        /** Line weight in pixels for polylines/polygons
         * @name TimeMapTheme#lineWeight 
         * @type Number */
        lineWeight:     2,
        /** Opacity for polygon fill 
         * @name TimeMapTheme#fillOpacity 
         * @type Number */
        fillOpacity:    0.4,
        /** Text color for duration events 
         * @name TimeMapTheme#eventTextColor 
         * @type String */
        eventTextColor: null,
        /** Path to instant event icon directory 
         * @name TimeMapTheme#eventIconPath 
         * @type String */
        eventIconPath:  "timemap/images/",
        /** Filename of instant event icon image
         * @name TimeMapTheme#eventIconImage 
         * @type String */
        eventIconImage: "red-circle.png",
        /** Whether to use the "classic" style timeline event tape
         * @name TimeMapTheme#classicTape 
         * @type Boolean */
        classicTape:    false,
        /** Icon image for marker placemarks 
         * @name TimeMapTheme#icon 
         * @type String */
        icon:      GIP + "red-dot.png",
        /** Icon size for marker placemarks 
         * @name TimeMapTheme#iconSize 
         * @type Number[] */
        iconSize: [32, 32],
        /** Icon shadow image for marker placemarks 
         * @name TimeMapTheme#iconShadow
         * @type String */
        iconShadow: GIP + "msmarker.shadow.png",
        /** Icon shadow size for marker placemarks 
         * @name TimeMapTheme#iconShadowSize 
         * @type Number[] */
        iconShadowSize: [59, 32],
        /** Icon anchor for marker placemarks 
         * @name TimeMapTheme#iconAnchor 
         * @type Number[] */
        iconAnchor: [16, 33]
    };
    
    // merge defaults with options
    var settings = $.extend(defaults, options);
    
    // cascade some settings as defaults
    defaults = {
        /** Line color for polylines/polygons
         * @name TimeMapTheme#lineColor 
         * @type String */
        lineColor:          settings.color,
        /** Fill color for polygons
         * @name TimeMapTheme#fillColor 
         * @type String */
        fillColor:          settings.color,
        /** Background color for duration events
         * @name TimeMapTheme#eventColor 
         * @type String */
        eventColor:         settings.color,
        /** Full URL for instant event icons
         * @name TimeMapTheme#eventIcon 
         * @type String */
        eventIcon:          settings.eventIcon || settings.eventIconPath + settings.eventIconImage
    };
    
    // return configured options as theme
    return $.extend(defaults, settings);
};

/**
 * Create a theme, based on an optional new or pre-set theme
 *
 * @param {TimeMapTheme|String} [theme] Existing theme to clone, or string key in {@link TimeMap.themes}
 * @param {Object} [options]            Optional settings to overwrite - see {@link TimeMapTheme}
 * @return {TimeMapTheme}               Configured theme
 */
TimeMapTheme.create = function(theme, options) {
    // test for string matches and missing themes
    theme = util.lookup(theme, TimeMap.themes);
    if (!theme) {
        return new TimeMapTheme(options);
    }
    if (options) {
        // see if we need to clone - guessing fewer keys in options
        var clone = false, key;
        for (key in options) {
            if (theme.hasOwnProperty(key)) {
                clone = {};
                break;
            }
        }
        // clone if necessary
        if (clone) {
            for (key in theme) {
                if (theme.hasOwnProperty(key)) {
                    clone[key] = options[key] || theme[key];
                }
            }
            // fix event icon path, allowing full image path in options
            clone.eventIcon = options.eventIcon || 
                clone.eventIconPath + clone.eventIconImage;
            return clone;
        }
    }
    return theme;
};


/*----------------------------------------------------------------------------
 * TimeMapItem Class
 *---------------------------------------------------------------------------*/

/**
 * @class
 * The TimeMapItem object holds references to one or more map placemarks and 
 * an associated timeline event.
 *
 * @constructor
 * @param {String} data             Object containing all item data
 * @param {String} [data.title=Untitled] Title of the item (visible on timeline)
 * @param {String|Date} [data.start]    Start time of the event on the timeline
 * @param {String|Date} [data.end]      End time of the event on the timeline (duration events only)
 * @param {Object} [data.point]         Data for a single-point placemark: 
 * @param {Float} [data.point.lat]          Latitude of map marker
 * @param {Float} [data.point.lon]          Longitude of map marker
 * @param {Object[]} [data.polyline]    Data for a polyline placemark, as an array in "point" format
 * @param {Object[]} [data.polygon]     Data for a polygon placemark, as an array "point" format
 * @param {Object} [data.overlay]       Data for a ground overlay:
 * @param {String} [data.overlay.image]     URL of image to overlay
 * @param {Float} [data.overlay.north]      Northern latitude of the overlay
 * @param {Float} [data.overlay.south]      Southern latitude of the overlay
 * @param {Float} [data.overlay.east]       Eastern longitude of the overlay
 * @param {Float} [data.overlay.west]       Western longitude of the overlay
 * @param {Object[]} [data.placemarks]  Array of placemarks, e.g. [{point:{...}}, {polyline:[...]}]
 * @param {Object} [data.options]       A container for optional arguments
 * @param {String} [data.options.description]       Plain-text description of the item
 * @param {LatLonPoint} [data.options.infoPoint]    Point indicating the center of this item
 * @param {String} [data.options.infoHtml]          Full HTML for the info window
 * @param {String} [data.options.infoUrl]           URL from which to retrieve full HTML for the info window
 * @param {String} [data.options.infoTemplate]      HTML for the info window content, with variable expressions
 *                                                  (as "{{varname}}" by default) to be replaced by option data
 * @param {String} [data.options.templatePattern=/{{([^}]+)}}/g]
 *                                                  Regex pattern defining variable syntax in the infoTemplate
 * @param {Function} [data.options.openInfoWindow={@link TimeMapItem.openInfoWindowBasic}]   
 *                                                  Function redefining how info window opens
 * @param {Function} [data.options.closeInfoWindow={@link TimeMapItem.closeInfoWindowBasic}]  
 *                                                  Function redefining how info window closes
 * @param {String|TimeMapTheme} [data.options.theme]    Theme applying to this item, overriding dataset theme
 * @param {mixed} [data.options[...]]               Any of the options for {@link TimeMapTheme} may be set here
 * @param {TimeMapDataset} dataset  Reference to the parent dataset object
 */
TimeMapItem = function(data, dataset) {
    // improve compression
    var item = this,
        // set defaults for options
        options = $.extend({
                type: 'none',
                description: '',
                infoPoint: null,
                infoHtml: '',
                infoUrl: '',
                openInfoWindow: data.options.infoUrl ? 
                    TimeMapItem.openInfoWindowAjax :
                    TimeMapItem.openInfoWindowBasic,
                infoTemplate: '<div class="infotitle">{{title}}</div>' + 
                              '<div class="infodescription">{{description}}</div>',
                templatePattern: /\{\{([^}]+)\}\}/g,
                closeInfoWindow: TimeMapItem.closeInfoWindowBasic
            }, data.options),
        tm = dataset.timemap,
        // allow theme options to be specified in options
        theme = options.theme = TimeMapTheme.create(options.theme, options),
        parser = options.dateParser, 
        eventClass = Timeline.DefaultEventSource.Event,
        // settings for timeline event
        start = data.start, 
        end = data.end, 
        eventIcon = theme.eventIcon,
        textColor = theme.eventTextColor,
        title = options.title = data.title || 'Untitled',
        event = null,
        instant,
        // empty containers
        placemarks = [], 
        pdataArr = [], 
        pdata = null, 
        type = "", 
        point = null, 
        i;
    
    // set core fields
    
    /**
     * This item's parent dataset
     * @name TimeMapItem#dataset
     * @type TimeMapDataset
     */
    item.dataset = dataset;
    
    /**
     * The timemap's map object
     * @name TimeMapItem#map
     * @type Mapstraction
     */
    item.map = tm.map;
    
    /**
     * The timemap's timeline object
     * @name TimeMapItem#timeline
     * @type Timeline
     */
    item.timeline = tm.timeline;
    
    /**
     * Container for optional settings passed in through the "options" parameter
     * @name TimeMapItem#opts
     * @type Object
     */
    item.opts = options;
    
    // Create timeline event
    
    start = start ? parser(start) : null;
    end = end ? parser(end) : null;
    instant = !end;
    if (start !== null) { 
        if (util.TimelineVersion() == "1.2") {
            // attributes by parameter
            event = new eventClass(start, end, null, null,
                instant, title, null, null, null, eventIcon, theme.eventColor, 
                theme.eventTextColor);
        } else {
            if (!textColor) {
                // tweak to show old-style events
                textColor = (theme.classicTape && !instant) ? '#FFFFFF' : '#000000';
            }
            // attributes in object
            event = new eventClass({
                start: start,
                end: end,
                instant: instant,
                text: title,
                icon: eventIcon,
                color: theme.eventColor,
                textColor: textColor
            });
        }
        // create cross-reference and add to timeline
        event.item = item;
        // allow for custom event loading
        if (!options.noEventLoad) {
            // add event to timeline
            dataset.eventSource.add(event);
        }
    }

    /**
     * This item's timeline event
     * @name TimeMapItem#event
     * @type Timeline.Event
     */
    item.event = event;
    
    // internal function: create map placemark
    // takes a data object (could be full data, could be just placemark)
    // returns an object with {placemark, type, point}
    function createPlacemark(pdata) {
        var placemark = null, 
            type = "", 
            point = null,
            pBounds;
        // point placemark
        if (pdata.point) {
            var lat = pdata.point.lat, 
                lon = pdata.point.lon;
            if (lat === undefined || lon === undefined) {
                // give up
                return placemark;
            }
            point = new LatLonPoint(
                parseFloat(lat), 
                parseFloat(lon)
            );
            // create marker
            placemark = new Marker(point);
            placemark.setLabel(pdata.title);
            placemark.addData(theme);
            type = "marker";
        }
        // polyline and polygon placemarks
        else if (pdata.polyline || pdata.polygon) {
            var points = [],
                isPolygon = "polygon" in pdata,
                line = pdata.polyline || pdata.polygon,
                x;
            pBounds = new BoundingBox();
            if (line && line.length) {
                for (x=0; x<line.length; x++) {
                    point = new LatLonPoint(
                        parseFloat(line[x].lat), 
                        parseFloat(line[x].lon)
                    );
                    points.push(point);
                    // add point to visible map bounds
                    pBounds.extend(point);
                }
                // make polyline or polygon
                placemark = new Polyline(points);
                placemark.addData({
                    color: theme.lineColor, 
                    width: theme.lineWeight, 
                    opacity: theme.lineOpacity, 
                    closed: isPolygon, 
                    fillColor: theme.fillColor,
                    fillOpacity: theme.fillOpacity
                });
                // set type and point
                type = isPolygon ? "polygon" : "polyline";
                point = isPolygon ?
                    pBounds.getCenter() :
                    points[Math.floor(points.length/2)];
            }
        } 
        // ground overlay placemark
        else if ("overlay" in pdata) {
            var sw = new LatLonPoint(
                    parseFloat(pdata.overlay.south), 
                    parseFloat(pdata.overlay.west)
                ),
                ne = new LatLonPoint(
                    parseFloat(pdata.overlay.north), 
                    parseFloat(pdata.overlay.east)
                );
            pBounds = new BoundingBox(sw.lat, sw.lon, ne.lat, ne.lon);
            // mapstraction can only add it - there's no placemark type :(
            // XXX: look into extending Mapstraction here
            tm.map.addImageOverlay("img" + (new Date()).getTime(), pdata.overlay.image, theme.lineOpacity,
                sw.lon, sw.lat, ne.lon, ne.lat);
            type = "overlay";
            point = pBounds.getCenter();
        }
        return {
            "placemark": placemark,
            "type": type,
            "point": point
        };
    }
    
    // Create placemark or placemarks
    
    // Create array of placemark data
    if ("placemarks" in data) {
        pdataArr = data.placemarks;
    } else {
        // we have one or more single placemarks
        ["point", "polyline", "polygon", "overlay"].forEach(function(type) {
            if (type in data) {
                // push placemarks into array
                pdata = {};
                pdata[type] = data[type];
                pdataArr.push(pdata);
            }
        });
    }
    // Create placemark objects
    pdataArr.forEach(function(pdata) {
        // put in title if necessary
        pdata.title = pdata.title || title;
        // create the placemark
        var p = createPlacemark(pdata);
        // check that the placemark was valid
        if (p) {
            // take the first point and type as a default
            point = point || p.point;
            type = type || p.type;
            if (p.placemark) {
                placemarks.push(p.placemark);
            }
        }
    });
    // set type, overriding for arrays
    options.type = placemarks.length > 1 ? "array" : type;
    
    // set infoPoint, checking for custom option
    options.infoPoint = options.infoPoint ?
        // check for custom infoPoint and convert to point
        new LatLonPoint(
            parseFloat(options.infoPoint.lat), 
            parseFloat(options.infoPoint.lon)
        ) : 
        point;
    
    // create cross-reference(s) and add placemark(s) if any exist
    placemarks.forEach(function(placemark) {
        placemark.item = item;
        // add listener to make placemark open when event is clicked
        placemark.click.addHandler(function() {
            item.openInfoWindow();
        });
        // allow for custom placemark loading
        if (!options.noPlacemarkLoad) {
            if (util.getPlacemarkType(placemark) == 'marker') {
                // add placemark to map
                tm.map.addMarker(placemark);
            } else {
                // add polyline to map
                tm.map.addPolyline(placemark);
            }
            // hide placemarks until the next refresh
            placemark.hide();
        }
    });
    
    /**
     * This item's placemark(s)
     * @name TimeMapItem#placemark
     * @type Marker|Polyline|Array
     */
    item.placemark = placemarks.length == 1 ? placemarks[0] :
        placemarks.length ? placemarks : 
        null;
    
    // getter functions
    
    /**
     * Return this item's native placemark object (specific to map provider;
     * undefined if this item has more than one placemark)
     * @name TimeMapItem#getNativePlacemark
     * @function
     * @return {Object}     The native placemark object (e.g. GMarker)
     */
    item.getNativePlacemark = function() {
        var placemark = item.placemark;
        return placemark && (placemark.proprietary_marker || placemark.proprietary_polyline);
    };
    
    /**
     * Return the placemark type for this item
     * @name TimeMapItem#getType
     * @function
     * 
     * @return {String}     Placemark type
     */
    item.getType = function() { return options.type; };
    
    /**
     * Return the title for this item
     * @name TimeMapItem#getTitle
     * @function
     * 
     * @return {String}     Item title
     */
    item.getTitle = function() { return options.title; };
    
    /**
     * Return the item's "info point" (the anchor for the map info window)
     * @name TimeMapItem#getInfoPoint
     * @function
     * 
     * @return {GLatLng}    Info point
     */
    item.getInfoPoint = function() { 
        // default to map center if placemark not set
        return options.infoPoint || item.map.getCenter();
    };
    
    /**
     * Return the start date of the item's event, if any
     * @name TimeMapItem#getStart
     * @function
     * 
     * @return {Date}   Item start date or undefined
     */
    item.getStart = function() {
        return item.event ? item.event.getStart() : null;
    };
    
    /**
     * Return the end date of the item's event, if any
     * @name TimeMapItem#getEnd
     * @function
     * 
     * @return {Date}   Item end dateor undefined
     */
    item.getEnd = function() {
        return item.event ? item.event.getEnd() : null;
    };
    
    /**
     * Return the timestamp of the start date of the item's event, if any
     * @name TimeMapItem#getStartTime
     * @function
     * 
     * @return {Number}    Item start date timestamp or undefined
     */
    item.getStartTime = function() {
        var start = item.getStart();
        if (start) {
            return start.getTime();
        }
    };
    
    /**
     * Return the timestamp of the end date of the item's event, if any
     * @name TimeMapItem#getEndTime
     * @function
     * 
     * @return {Number}    Item end date timestamp or undefined
     */
    item.getEndTime = function() {
        var end = item.getEnd();
        if (end) {
            return end.getTime();
        }
    };
    
    /**
     * Whether the item is currently selected 
     * (i.e., the item's info window is open)
     * @name TimeMapItem#isSelected
     * @function
     * @return Boolean
     */
    item.isSelected = function() {
        return tm.getSelected() === item;
    };
    
    /**
     * Whether the item is visible
     * @name TimeMapItem#visible
     * @type Boolean
     */
    item.visible = true;
    
    /**
     * Whether the item's placemark is visible
     * @name TimeMapItem#placemarkVisible
     * @type Boolean
     */
    item.placemarkVisible = false;
    
    /**
     * Whether the item's event is visible
     * @name TimeMapItem#eventVisible
     * @type Boolean
     */
    item.eventVisible = true;
    
    /**
     * Open the info window for this item.
     * By default this is the map infoWindow, but you can set custom functions
     * for whatever behavior you want when the event or placemark is clicked
     * @name TimeMapItem#openInfoWindow
     * @function
     */
    item.openInfoWindow = function() {
        options.openInfoWindow.call(item);
        tm.setSelected(item);
    };
    
    /**
     * Close the info window for this item.
     * By default this is the map infoWindow, but you can set custom functions
     * for whatever behavior you want.
     * @name TimeMapItem#closeInfoWindow
     * @function
     */
    item.closeInfoWindow = function() {
        options.closeInfoWindow.call(item);
        tm.setSelected(undefined);
    };
};

TimeMapItem.prototype = {
    /** 
     * Show the map placemark(s)
     */
    showPlacemark: function() {
        // XXX: Special case for overlay image (support for some providers)?
        var item = this,
            f = function(placemark) {
                if (placemark.api) {
                    placemark.show();
                }
            };
        if (item.placemark && !item.placemarkVisible) {
            if (item.getType() == "array") {
                item.placemark.forEach(f);
            } else {
                f(item.placemark);
            }
            item.placemarkVisible = true;
        }
    },

    /** 
     * Hide the map placemark(s)
     */
    hidePlacemark: function() {
        // XXX: Special case for overlay image (support for some providers)?
        var item = this,
            f = function(placemark) {
                if (placemark.api) {
                    placemark.hide();
                }
            };
        if (item.placemark && item.placemarkVisible) {
            if (item.getType() == "array") {
                item.placemark.forEach(f);
            } else {
                f(item.placemark);
            }
            item.placemarkVisible = false;
        }
        item.closeInfoWindow();
    },

    /** 
     * Show the timeline event.
     * NB: Will likely require calling timeline.layout()
     */
    showEvent: function() {
        var item = this,
            event = item.event;
        if (event && !item.eventVisible) {
            // XXX: Use timeline filtering API
            item.timeline.getBand(0)
                .getEventSource()._events._events.add(event);
            item.eventVisible = true;
        }
    },

    /** 
     * Hide the timeline event.
     * NB: Will likely require calling timeline.layout(),
     * AND calling eventSource._events._index()  (ugh)
     */
    hideEvent: function() {
        var item = this,
            event = item.event;
        if (event && item.eventVisible){
            // XXX: Use timeline filtering API
            item.timeline.getBand(0)
                .getEventSource()._events._events.remove(event);
            item.eventVisible = false;
        }
    },

    /** 
     * Scroll the timeline to the start of this item's event
     * @param {Boolean} [animated]      Whether to do an animated scroll, rather than a jump.
     */
    scrollToStart: function(animated) {
        var item = this;
        if (item.event) {
            item.dataset.timemap.scrollToDate(item.getStart(), false, animated);
        }
    },

    /**
     * Get the HTML for the info window, filling in the template if necessary
     * @return {String}     Info window HTML
     */
    getInfoHtml: function() {
        var opts = this.opts,
            html = opts.infoHtml,
            patt = opts.templatePattern,
            match;
        // create content for info window if none is provided
        if (!html) {
            // fill in template
            html = opts.infoTemplate;
            match = patt.exec(html);
            while (match) {
                html = html.replace(match[0], opts[match[1]]||'');
                match = patt.exec(html);
            }
            // cache for future use
            opts.infoHtml = html;
        }
        return html;
    },
    
    /**
     * Determine if this item's event is in the current visible area
     * of the top band of the timeline. Note that this only considers the
     * dates, not whether the event is otherwise hidden.
     * @return {Boolean}    Whether the item's event is visible
     */
    onVisibleTimeline: function() {
        var item = this,
            topband = item.timeline.getBand(0),
            maxVisibleDate = topband.getMaxVisibleDate().getTime(),
            minVisibleDate = topband.getMinVisibleDate().getTime(),
            itemStart = item.getStartTime(),
            itemEnd = item.getEndTime();
        return itemStart !== undefined ? 
            // item is in the future
            itemStart < maxVisibleDate &&
            // item is in the past
            (itemEnd > minVisibleDate || itemStart > minVisibleDate) :
            // item has no start date
            true;
    }

};


/**
 * Standard open info window function, using static text in map window
 */
TimeMapItem.openInfoWindowBasic = function() {
    var item = this,
        html = item.getInfoHtml(),
        ds = item.dataset,
        placemark = item.placemark;
    // scroll timeline if necessary
    if (!item.onVisibleTimeline()) {
        ds.timemap.scrollToDate(item.getStart());
    }
    // open window
    if (item.getType() == "marker" && placemark.api) {
        placemark.setInfoBubble(html);
        placemark.openBubble();
        // deselect when window is closed
        item.closeHandler = placemark.closeInfoBubble.addHandler(function() { 
            // deselect
            ds.timemap.setSelected(undefined);
            // kill self
            placemark.closeInfoBubble.removeHandler(item.closeHandler);
        });
    } else {
        item.map.openBubble(item.getInfoPoint(), html);
        item.map.tmBubbleItem = item;
    }
};

/**
 * Open info window function using ajax-loaded text in map window
 */
TimeMapItem.openInfoWindowAjax = function() {
    var item = this;
    if (!item.opts.infoHtml && item.opts.infoUrl) { // load content via AJAX
        $.get(item.opts.infoUrl, function(result) {
                item.opts.infoHtml = result;
                item.openInfoWindow();
        });
        return;
    }
    // fall back on basic function if content is loaded or URL is missing
    item.openInfoWindow = function() {
        TimeMapItem.openInfoWindowBasic.call(item);
        item.dataset.timemap.setSelected(item);
    };
    item.openInfoWindow();
};

/**
 * Standard close window function, using the map window
 */
TimeMapItem.closeInfoWindowBasic = function() {
    var item = this;
    if (item.getType() == "marker") {
        item.placemark.closeBubble();
    } else {
        if (item == item.map.tmBubbleItem) {
            item.map.closeBubble();
            item.map.tmBubbleItem = null;
        }
    }
};

/*----------------------------------------------------------------------------
 * Utility functions
 *---------------------------------------------------------------------------*/

/**
 * Get XML tag value as a string
 *
 * @param {jQuery} n        jQuery object with context
 * @param {String} tag      Name of tag to look for
 * @param {String} [ns]     XML namespace to look in
 * @return {String}         Tag value as string
 */
TimeMap.util.getTagValue = function(n, tag, ns) {
    return util.getNodeList(n, tag, ns).first().text();
};

/**
 * Empty container for mapping XML namespaces to URLs
 * @example
 TimeMap.util.nsMap['georss'] = 'http://www.georss.org/georss';
 // find georss:point
 TimeMap.util.getNodeList(node, 'point', 'georss')
 */
TimeMap.util.nsMap = {};

/**
 * Cross-browser implementation of getElementsByTagNameNS.
 * Note: Expects any applicable namespaces to be mapped in
 * {@link TimeMap.util.nsMap}.
 *
 * @param {jQuery|XML Node} n   jQuery object with context, or XML node
 * @param {String} tag          Name of tag to look for
 * @param {String} [ns]         XML namespace to look in
 * @return {jQuery}             jQuery object with the list of nodes found
 */
TimeMap.util.getNodeList = function(n, tag, ns) {
    var nsMap = TimeMap.util.nsMap;
    // get context node
    n = n.jquery ? n[0] : n;
    // no context
    return !n ? $() :
        // no namespace
        !ns ? $(tag, n) :
        // native function exists
        (n.getElementsByTagNameNS && nsMap[ns]) ? $(n.getElementsByTagNameNS(nsMap[ns], tag)) :
        // no function, try the colon tag name
        $(n.getElementsByTagName(ns + ':' + tag));
};

/**
 * Make TimeMap.init()-style points from a GLatLng, LatLonPoint, array, or string
 *
 * @param {Object} coords       GLatLng, LatLonPoint, array, or string to convert
 * @param {Boolean} [reversed]  Whether the points are KML-style lon/lat, rather than lat/lon
 * @return {Object}             TimeMap.init()-style point object
 */
TimeMap.util.makePoint = function(coords, reversed) {
    var latlon = null;
    // GLatLng or LatLonPoint
    if (coords.lat && coords.lng) {
        latlon = [coords.lat(), coords.lng()];
    }
    // array of coordinates
    if ($.type(coords)=='array') {
        latlon = coords;
    }
    // string
    if (!latlon) {
        // trim extra whitespace
        coords = $.trim(coords);
        if (coords.indexOf(',') > -1) {
            // split on commas
            latlon = coords.split(",");
        } else {
            // split on whitespace
            latlon = coords.split(/[\r\n\f ]+/);
        }
    }
    // deal with extra coordinates (i.e. KML altitude)
    if (latlon.length > 2) {
        latlon = latlon.slice(0, 2);
    }
    // deal with backwards (i.e. KML-style) coordinates
    if (reversed) {
        latlon.reverse();
    }
    return {
        "lat": $.trim(latlon[0]),
        "lon": $.trim(latlon[1])
    };
};

/**
 * Make TimeMap.init()-style polyline/polygons from a whitespace-delimited
 * string of coordinates (such as those in GeoRSS and KML).
 *
 * @param {Object} coords       String to convert
 * @param {Boolean} [reversed]  Whether the points are KML-style lon/lat, rather than lat/lon
 * @return {Object}             Formated coordinate array
 */
TimeMap.util.makePoly = function(coords, reversed) {
    var poly = [], 
        latlon, x,
        coordArr = $.trim(coords).split(/[\r\n\f ]+/);
    // loop through coordinates
    for (x=0; x<coordArr.length; x++) {
        latlon = (coordArr[x].indexOf(',') > 0) ?
            // comma-separated coordinates (KML-style lon/lat)
            coordArr[x].split(",") :
            // space-separated coordinates - increment to step by 2s
            [coordArr[x], coordArr[++x]];
        // deal with extra coordinates (i.e. KML altitude)
        if (latlon.length > 2) {
            latlon = latlon.slice(0, 2);
        }
        // deal with backwards (i.e. KML-style) coordinates
        if (reversed) {
            latlon.reverse();
        }
        poly.push({
            "lat": latlon[0],
            "lon": latlon[1]
        });
    }
    return poly;
};

/**
 * Format a date as an ISO 8601 string
 *
 * @param {Date} d          Date to format
 * @param {Number} [precision] Precision indicator:<pre>
 *      3 (default): Show full date and time
 *      2: Show full date and time, omitting seconds
 *      1: Show date only
 *</pre>
 * @return {String}         Formatted string
 */
TimeMap.util.formatDate = function(d, precision) {
    // default to high precision
    precision = precision || 3;
    var str = "";
    if (d) {
        var yyyy = d.getUTCFullYear(),
            mo = d.getUTCMonth(),
            dd = d.getUTCDate();
        // deal with early dates
        if (yyyy < 1000) {
            return (yyyy < 1 ? (yyyy * -1 + "BC") : yyyy + "");
        }
        // check for date.js support
        if (d.toISOString && precision == 3) {
            return d.toISOString();
        }
        // otherwise, build ISO 8601 string
        var pad = function(num) {
            return ((num < 10) ? "0" : "") + num;
        };
        str += yyyy + '-' + pad(mo + 1 ) + '-' + pad(dd);
        // show time if top interval less than a week
        if (precision > 1) {
            var hh = d.getUTCHours(),
                mm = d.getUTCMinutes(),
                ss = d.getUTCSeconds();
            str += 'T' + pad(hh) + ':' + pad(mm);
            // show seconds if the interval is less than a day
            if (precision > 2) {
                str += pad(ss);
            }
            str += 'Z';
        }
    }
    return str;
};

/**
 * Determine the SIMILE Timeline version.
 *
 * @return {String}     At the moment, only "1.2", "2.2.0", or what Timeline provides
 */
TimeMap.util.TimelineVersion = function() {
    // Timeline.version support added in 2.3.0
    return Timeline.version ||
        // otherwise check manually
        (Timeline.DurationEventPainter ? "1.2" : "2.2.0");
};

/** 
 * Identify the placemark type of a Mapstraction placemark
 *
 * @param {Object} pm       Placemark to identify
 * @return {String}         Type of placemark, or false if none found
 */
TimeMap.util.getPlacemarkType = function(pm) {
    return pm.constructor == Marker ? 'marker' :
        pm.constructor == Polyline ?
            (pm.closed ? 'polygon' : 'polyline') :
        false;
};

/**
 * Attempt look up a key in an object, returning either the value,
 * undefined if the key is a string but not found, or the key if not a string 
 *
 * @param {String|Object} key   Key to look up
 * @param {Object} map          Object in which to look
 * @return {Object}             Value, undefined, or key
 */
TimeMap.util.lookup = function(key, map) {
    return typeof key == 'string' ? map[key] : key;
};


// add indexOf support for older browsers (simple version, no "from" support)
if (!([].indexOf)) {
    Array.prototype.indexOf = function(el) {
        var a = this,
            i = a.length;
        while (--i >= 0) {
            if (a[i] === el) {
                break;
            }
        }
        return i;
    };
}

// add forEach support for older browsers (simple version, no "this" support)
if (!([].forEach)) {
    Array.prototype.forEach = function(f) {
        var a = this,
            i;
        for (i=0; i < a.length; i++) {
            if (i in a) {
                f(a[i], i, a);
            }
        }
    };
}

/*----------------------------------------------------------------------------
 * Lookup maps
 * (need to be at end because some call util functions on initialization)
 *---------------------------------------------------------------------------*/

/**
 * @namespace
 * Lookup map of common timeline intervals.  
 * Add custom intervals here if you want to refer to them by key rather 
 * than as a function name.
 * @example
    TimeMap.init({
        bandIntervals: "hr",
        // etc...
    });
 *
 */
TimeMap.intervals = {
    /** second / minute */
    sec: [DateTime.SECOND, DateTime.MINUTE],
    /** minute / hour */
    min: [DateTime.MINUTE, DateTime.HOUR],
    /** hour / day */
    hr: [DateTime.HOUR, DateTime.DAY],
    /** day / week */
    day: [DateTime.DAY, DateTime.WEEK],
    /** week / month */
    wk: [DateTime.WEEK, DateTime.MONTH],
    /** month / year */
    mon: [DateTime.MONTH, DateTime.YEAR],
    /** year / decade */
    yr: [DateTime.YEAR, DateTime.DECADE],
    /** decade / century */
    dec: [DateTime.DECADE, DateTime.CENTURY]
};

/**
 * @namespace
 * Lookup map of map types.
 * @example
    TimeMap.init({
        options: {
            mapType: "satellite"
        },
        // etc...
    });
 */
TimeMap.mapTypes = {
    /** Normal map */
    normal: Mapstraction.ROAD, 
    /** Satellite map */
    satellite: Mapstraction.SATELLITE, 
    /** Hybrid map */
    hybrid: Mapstraction.HYBRID,
    /** Physical (terrain) map */
    physical: Mapstraction.PHYSICAL
};

/**
 * @namespace
 * Lookup map of supported date parser functions. 
 * Add custom date parsers here if you want to refer to them by key rather 
 * than as a function name.
 * @example
    TimeMap.init({
        datasets: [
            {
                options: {
                    dateParser: "gregorian"
                },
                // etc...
            }
        ],
        // etc...
    });
 */
TimeMap.dateParsers = {
    
    /**
     * Better Timeline Gregorian parser... shouldn't be necessary :(.
     * Gregorian dates are years with "BC" or "AD"
     *
     * @param {String} s    String to parse into a Date object
     * @return {Date}       Parsed date or null
     */
    gregorian: function(s) {
        if (!s || typeof s != "string") {
            return null;
        }
        // look for BC
        var bc = Boolean(s.match(/b\.?c\.?/i)),
            // parse - parseInt will stop at non-number characters
            year = parseInt(s, 10),
            d;
        // look for success
        if (!isNaN(year)) {
            // deal with BC
            if (bc) {
                year = 1 - year;
            }
            // make Date and return
            d = new Date(0);
            d.setUTCFullYear(year);
            return d;
        }
        else {
            return null;
        }
    },

    /**
     * Parse date strings with a series of date parser functions, until one works. 
     * In order:
     * <ol>
     *  <li>Date.parse() (so Date.js should work here, if it works with Timeline...)</li>
     *  <li>Gregorian parser</li>
     *  <li>The Timeline ISO 8601 parser</li>
     * </ol>
     *
     * @param {String} s    String to parse into a Date object
     * @return {Date}       Parsed date or null
     */
    hybrid: function(s) {
        // in case we don't know if this is a string or a date
        if (s instanceof Date) {
            return s;
        }
        var parsers = TimeMap.dateParsers,
            // try native date parse and timestamp
            d = new Date(typeof s == "number" ? s : Date.parse(parsers.fixChromeBug(s)));
        if (isNaN(d)) {
            if (typeof s == "string") {
                // look for Gregorian dates
                if (s.match(/^-?\d{1,6} ?(a\.?d\.?|b\.?c\.?e?\.?|c\.?e\.?)?$/i)) {
                    d = parsers.gregorian(s);
                } 
                // try ISO 8601 parse
                else {
                    try {
                        d = parsers.iso8601(s);
                    } catch(e) {
                        d = null;
                    }
                }
                // look for timestamps
                if (!d && s.match(/^\d{7,}$/)) {
                    d = new Date(parseInt(s, 10));
                }
            } else {
                return null;
            }
        }
        // d should be a date or null
        return d;
    },
    
    /** 
     * ISO8601 parser: parse ISO8601 datetime strings 
     * @function
     */
    iso8601: DateTime.parseIso8601DateTime,
    
    /** 
     * Clunky fix for Chrome bug: http://code.google.com/p/chromium/issues/detail?id=46703
     * @private
     */
    fixChromeBug: function(s) {
        return Date.parse("-200") == Date.parse("200") ? 
            (typeof s == "string" && s.substr(0,1) == "-" ? null : s) :
            s;
    }
};
 
/**
 * @namespace
 * Pre-set event/placemark themes in a variety of colors. 
 * Add custom themes here if you want to refer to them by key rather 
 * than as a function name.
 * @example
    TimeMap.init({
        options: {
            theme: "orange"
        },
        datasets: [
            {
                options: {
                    theme: "yellow"
                },
                // etc...
            }
        ],
        // etc...
    });
 */
TimeMap.themes = {

    /** 
     * Red theme: <span style="background:#FE766A">#FE766A</span>
     * This is the default.
     *
     * @type TimeMapTheme
     */
    red: new TimeMapTheme(),
    
    /** 
     * Blue theme: <span style="background:#5A7ACF">#5A7ACF</span>
     *
     * @type TimeMapTheme
     */
    blue: new TimeMapTheme({
        icon: GIP + "blue-dot.png",
        color: "#5A7ACF",
        eventIconImage: "blue-circle.png"
    }),

    /** 
     * Green theme: <span style="background:#19CF54">#19CF54</span>
     *
     * @type TimeMapTheme
     */
    green: new TimeMapTheme({
        icon: GIP + "green-dot.png",
        color: "#19CF54",
        eventIconImage: "green-circle.png"
    }),

    /** 
     * Light blue theme: <span style="background:#5ACFCF">#5ACFCF</span>
     *
     * @type TimeMapTheme
     */
    ltblue: new TimeMapTheme({
        icon: GIP + "ltblue-dot.png",
        color: "#5ACFCF",
        eventIconImage: "ltblue-circle.png"
    }),

    /** 
     * Purple theme: <span style="background:#8E67FD">#8E67FD</span>
     *
     * @type TimeMapTheme
     */
    purple: new TimeMapTheme({
        icon: GIP + "purple-dot.png",
        color: "#8E67FD",
        eventIconImage: "purple-circle.png"
    }),

    /** 
     * Orange theme: <span style="background:#FF9900">#FF9900</span>
     *
     * @type TimeMapTheme
     */
    orange: new TimeMapTheme({
        icon: GIP + "orange-dot.png",
        color: "#FF9900",
        eventIconImage: "orange-circle.png"
    }),

    /** 
     * Yellow theme: <span style="background:#FF9900">#ECE64A</span>
     *
     * @type TimeMapTheme
     */
    yellow: new TimeMapTheme({
        icon: GIP + "yellow-dot.png",
        color: "#ECE64A",
        eventIconImage: "yellow-circle.png"
    }),

    /** 
     * Pink theme: <span style="background:#E14E9D">#E14E9D</span>
     *
     * @type TimeMapTheme
     */
    pink: new TimeMapTheme({
        icon: GIP + "pink-dot.png",
        color: "#E14E9D",
        eventIconImage: "pink-circle.png"
    })
};

// save to window
window.TimeMap = TimeMap;
window.TimeMapFilterChain = TimeMapFilterChain;
window.TimeMapDataset = TimeMapDataset;
window.TimeMapTheme = TimeMapTheme;
window.TimeMapItem = TimeMapItem;

})();