/* 
 * Timemap.js Copyright 2010 Nick Rabinowitz.
 * Licensed under the MIT License (see LICENSE.txt)
 */

/**
 * @fileOverview
 * JSON Loaders (JSONP, JSON String)
 *
 * @author Nick Rabinowitz (www.nickrabinowitz.com)
 */
 
// for JSLint
/*global TimeMap */

(function() {
    var loaders = TimeMap.loaders;

/**
 * @class
 * JSONP loader - expects a service that takes a callback function name as
 * the last URL parameter.
 *
 * <p>The jsonp loader assumes that the JSON can be loaded from a url with a "?" instead of
 * the callback function name, e.g. "http://www.test.com/getsomejson.php?callback=?". See
 * <a href="http://api.jquery.com/jQuery.ajax/">the jQuery.ajax documentation</a> for more
 * details on how to format the url, especially if the parameter is not called "callback".
 * This works for services like Google Spreadsheets, etc., and accepts remote URLs.</p>
 * @name TimeMap.loaders.jsonp
 * @augments TimeMap.loaders.remote
 *
 * @example
TimeMap.init({
    datasets: [
        {
            title: "JSONP Dataset",
            type: "jsonp",
            options: {
                url: "http://www.example.com/getsomejson.php?callback=?"
            }
        }
    ],
    // etc...
});
 *
 * @constructor
 * @param {Object} options          All options for the loader:
 * @param {String} options.url          URL of JSON service to load, callback name replaced with "?"
 * @param {mixed} [options[...]]        Other options (see {@link loaders.remote})
 */
loaders.jsonp = function(options) {
    var loader = new loaders.remote(options);
    
    // set ajax settings for loader
    loader.opts.dataType = 'jsonp';
    
    return loader;
};

/**
 * @class
 * JSON string loader - expects a plain JSON array.
 *
 * <p>The json_string loader assumes an array of items in plain JSON, with no
 * callback function - this will require a local URL.</p>
 * @name TimeMap.loaders.json
 * @class
 *
 * @augments TimeMap.loaders.remote
 *
 * @example
TimeMap.init({
    datasets: [
        {
            title: "JSON String Dataset",
            type: "json_string",
            options: {
                url: "mydata.json"    // Must be a local URL
            }
        }
    ],
    // etc...
});
 *
 * @param {Object} options          All options for the loader
 * @param {String} options.url          URL of JSON file to load
 * @param {mixed} [options[...]]        Other options (see {@link loaders.remote})
 */
loaders.json = function(options) {
    var loader = new loaders.remote(options);
    
    // set ajax settings for loader
    loader.opts.dataType =  'json';
    
    return loader;
};

// For backwards compatibility
loaders.json_string = loaders.json;

})();
