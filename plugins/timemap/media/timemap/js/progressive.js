/* 
 * Timemap.js Copyright 2010 Nick Rabinowitz.
 * Licensed under the MIT License (see LICENSE.txt)
 */

/**
 * @fileOverview
 * Progressive loader
 *
 * @author Nick Rabinowitz (www.nickrabinowitz.com)
 */
 
// for JSLint
/*global TimeMap */

/**
 * @class
 * Progressive loader class - basically a wrapper for another remote loader that can
 * load data progressively by date range, depending on timeline position.
 *
 * <p>The progressive loader can take either another loader or parameters for 
 * another loader. It expects a loader with a "url" attribute including placeholder
 * strings [start] and [end] for the start and end dates to retrieve. The assumption 
 * is that the data service can take start and end parameters and return the data for 
 * that date range.</p>
 *
 * @example
TimeMap.init({
    datasets: [
        {
            title: "Progressive JSONP Dataset",
            type: "progressive",
            options: {
                type: "jsonp",
                url: "http://www.test.com/getsomejson.php?start=[start]&end=[end]callback="
            }
        }
    ],
    // etc...
});
 *
 * @example
TimeMap.init({
    datasets: [
        {
            title: "Progressive KML Dataset",
            type: "progressive",
            options: {
                loader: new TimeMap.loaders.kml({
                    url: "/mydata.kml?start=[start]&end=[end]"
                })
            }
        }
    ],
    // etc...
}); 
 * @see <a href="../../examples/progressive.html">Progressive Loader Example</a>
 *
 * @constructor
 * @param {Object} options          All options for the loader
 * @param {TimeMap.loaders.remote} [options.loader] Instantiated loader class (overrides "type")
 * @param {String} [options.type]                   Name of loader class to use
 * @param {String|Date} options.start               Start of initial date range, as date or string
 * @param {Number} options.interval                 Size in milliseconds of date ranges to load at a time
 * @param {String|Date} [options.dataMinDate]       Minimum date available in data (optional, will avoid
 *                                                  unnecessary service requests if supplied)
 * @param {String|Date} [options.dataMaxDate]       Maximum date available in data (optional, will avoid
 *                                                  unnecessary service requests if supplied)
 * @param {Function} [options.formatUrl]            Function taking (urlTemplate, start, end) and returning
 *                                                  a URL formatted as needed by the service
 * @param {Function} [options.formatDate={@link TimeMap.util.formatDate}]           
 *                                                  Function to turn a date into a string formatted
 *                                                  as needed by the service
 * @param {mixed} [options[...]]                    Other options needed by the "type" loader
 */
TimeMap.loaders.progressive = function(options) {
    // get loader
    var loader = options.loader, 
        type = options.type;
    if (!loader) {
        // get loader class
        var loaderClass = (typeof(type) == 'string') ? TimeMap.loaders[type] : type;
        loader = new loaderClass(options);
    }
    
    // save loader attributes
    var baseUrl = loader.opts.url, 
        baseLoadFunction = loader.load,
        interval = options.interval,
        formatDate = options.formatDate || TimeMap.util.formatDate,
        formatUrl = options.formatUrl || function(url, start, end) {
            return url
                .replace('[start]', formatDate(start))
                .replace('[end]', formatDate(end));
        },
        parseDate = TimeMap.dateParsers.hybrid,
        zeroDate = parseDate(options.start), 
        dataMinDate = parseDate(options.dataMinDate), 
        dataMaxDate = parseDate(options.dataMaxDate),
        loaded = {};
    
    // We don't start with a TimeMap reference, so we need
    // to stick the listener in on the first load() call
    var addListener = function(dataset) {
        var band = dataset.timemap.timeline.getBand(0);
        // add listener
        band.addOnScrollListener(function() {
            // determine relevant blocks
            var now = band.getCenterVisibleDate(),
                currBlock = Math.floor((now.getTime() - zeroDate.getTime()) / interval),
                currBlockTime = zeroDate.getTime() + (interval * currBlock),
                nextBlockTime = currBlockTime + interval,
                prevBlockTime = currBlockTime - interval,
                // no callback necessary?
                callback = function() {
                    dataset.timemap.timeline.layout();
                };
            
            // is the current block loaded?
            if ((!dataMaxDate || currBlockTime < dataMaxDate.getTime()) &&
                (!dataMinDate || currBlockTime > dataMinDate.getTime()) &&
                !loaded[currBlock]) {
                // load it
                // console.log("loading current block (" + currBlock + ")");
                loader.load(dataset, callback, new Date(currBlockTime), currBlock);
            }
            // are we close enough to load the next block, and is it loaded?
            if (nextBlockTime < band.getMaxDate().getTime() &&
                (!dataMaxDate || nextBlockTime < dataMaxDate.getTime()) &&
                !loaded[currBlock + 1]) {
                // load next block
                // console.log("loading next block (" + (currBlock + 1) + ")");
                loader.load(dataset, callback, new Date(nextBlockTime), currBlock + 1);
            }
            // are we close enough to load the previous block, and is it loaded?
            if (prevBlockTime > band.getMinDate().getTime() &&
                (!dataMinDate || prevBlockTime > dataMinDate.getTime()) &&
                !loaded[currBlock - 1]) {
                // load previous block
                // console.log("loading prev block (" + (currBlock - 1)  + ")");
                loader.load(dataset, callback, new Date(prevBlockTime), currBlock - 1);
            }
        });
        // kill this function so that listener is only added once
        addListener = false;
    };
    
    /**
     * Load data based on current time
     * @name TimeMap.loaders.progressive#load
     * @function
     * @param {TimeMapDataset} dataset      Dataset to load data into
     * @param {Function} callback           Callback to execute when data is loaded
     * @param {Date} start                  Start date to load data from
     * @param {Number} currBlock            Index of the current time block
     */
    loader.load = function(dataset, callback, start, currBlock) {
        // set start date, defaulting to zero date
        start = parseDate(start) || zeroDate;
        // set current block, defaulting to 0
        currBlock = currBlock || 0;
        // set end by interval
        var end = new Date(start.getTime() + interval);
        
        // set current block as loaded
        // XXX: Failed loads will give a false positive here...
        // but I'm not sure how else to avoid multiple loads :(
        loaded[currBlock] = true;
        
        // put dates into URL
        loader.opts.url = formatUrl(baseUrl, start, end);
        
        // load data
        baseLoadFunction.call(loader, dataset, function() {
            // add onscroll listener if not yet done
            if (addListener) {
                addListener(dataset);
            }
            // run callback
            callback();
        });
    };
    
    return loader;
};
