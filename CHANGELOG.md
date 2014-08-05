Ushahidi 2.7.4 (Bug fix release) - 05/08/2014
-------------------------------------

### Major Fixes

* Upgraded Openlayers to version 2.13.1 (https://github.com/ushahidi/Ushahidi_Web/pull/1391) 
* Stop checking what characters are in passowrd (https://github.com/ushahidi/Ushahidi_Web/pull/1373) 
* Added option for admins to change the maximum file size for attaching photos to reports (https://github.com/ushahidi/Ushahidi_Web/pull/1369)
* Delete cache after changing theme so changes reflect immediately (https://github.com/ushahidi/Ushahidi_Web/pull/1368)

Ushahidi 2.7.3 (Bug fix release) - 23/04/2014
-------------------------------------

### Major Fixes

* Timeline upgrade (https://github.com/ushahidi/Ushahidi_Web/pull/1341) 
* Fixed issue with dual alert sign up (https://github.com/ushahidi/Ushahidi_Web/pull/1339) 
* Updated sms helper and scheduler to add url in sms (https://github.com/ushahidi/Ushahidi_Web/pull/1339)
* Check category translation on csv import to avoid category duplication (https://github.com/ushahidi/Ushahidi_Web/pull/1345)
* Fixed issue with the reverse geocoder (https://github.com/ushahidi/Ushahidi_Web/pull/1336)
* Fixed broken video embeds in reports when viewed over HTTPS (https://github.com/ushahidi/Ushahidi_Web/pull/1328)
* Added South Sudan and Kosovo to the country list (https://github.com/ushahidi/Ushahidi_Web/pull/1324) 
* Fixed issue with feed category 
* Replaced feedback email after submit report with site email

Ushahidi 2.7.2 (Bug fix release) - 28/01/2014
-------------------------------------

### Major Fixes

* Tiding up conflicting logic on email alerts(https://github.com/ushahidi/Ushahidi_Web/issues/943) 
* Escaping db password (https://github.com/ushahidi/Ushahidi_Web/issues/1265) 
* Added Feed category (https://github.com/ushahidi/Ushahidi_Web/issues/1291) 
* Fix incosistency in custom fields position(https://github.com/ushahidi/Ushahidi_Web/issues/1296) 
* Handle bad file encodings when importing CSV files (https://github.com/ushahidi/Ushahidi_Web/issues/1140) 
* Adding Nominatin as a geocoder option (https://github.com/ushahidi/Ushahidi_Web/issues/794) 
* Fixed reports date picker 
* Added reverse geocoder 
* Moved header_nav.php to themes/default/views/ to allow theme override 
* Cleaned up alerts scheduler 
* Removed ci_cumulus theme 
* Removed checkins 
* Added Delete All Reports for Admin
* Fixed timeline js timing out
* Lots of code clean up, error handling and coding standards.

Ushahidi 2.7.1 (Bug fix release) - 04/09/2013
-------------------------------------

### Major Fixes

* Ushahidi should not upsize uploaded images, only downsize them[#1132](https://github.com/ushahidi/Ushahidi_Web/issues/1132)
* Tagline running under report button[#1040](https://github.com/ushahidi/Ushahidi_Web/issues/1040)
* Long, Lat order on mouse hover[#1092](https://github.com/ushahidi/Ushahidi_Web/issues/1092)
* Bulk deletions coupled with single report deletion[#1093](https://github.com/ushahidi/Ushahidi_Web/issues/1093)
* Unexpected Function error[#1123](https://github.com/ushahidi/Ushahidi_Web/issues/1123)
* ‘Undefined’ on hover[#1067](https://github.com/ushahidi/Ushahidi_Web/issues/1067)
* Wrong Content Type for RSS Feeds[#991](https://github.com/ushahidi/Ushahidi_Web/issues/991)
* Twitter Credential Error[#1109](https://github.com/ushahidi/Ushahidi_Web/issues/1109)
* Validation error on custom fields dropdown[#1113](https://github.com/ushahidi/Ushahidi_Web/issues/1113)
* CSV Uploader[#1181](https://github.com/ushahidi/Ushahidi_Web/issues/1181)
* Error with reports view page[#1176,1166,1178](https://github.com/ushahidi/Ushahidi_Web/issues/1176,https://github.com/ushahidi/Ushahidi_Web/issues/1166,https://github.com/ushahidi/Ushahidi_Web/issues/1178)
* Login page issues[#1161](https://github.com/ushahidi/Ushahidi_Web/pull/1161)
* Issues on Crowdmap[#1158](https://github.com/ushahidi/Ushahidi_Web/issues/1158)
* Database table prefix installation[#1154](https://github.com/ushahidi/Ushahidi_Web/issues/1154)
* Upload images .jpeg fails[#1136](https://github.com/ushahidi/Ushahidi_Web/issues/1136)
* Database Error[#1149](https://github.com/ushahidi/Ushahidi_Web/issues/1149)
* Category impact charts not loading[#1147](https://github.com/ushahidi/Ushahidi_Web/issues/1147)
* Fatal error in stats page[#1134](https://github.com/ushahidi/Ushahidi_Web/pull/1134)
* Twitter scheduler bugs[#1133](https://github.com/ushahidi/Ushahidi_Web/pull/1133)
* CSV downloaded form platform throws errors on attempt to upload[#1140](https://github.com/ushahidi/Ushahidi_Web/issues/1140)
* Default report time on reports always wrong[#1130](https://github.com/ushahidi/Ushahidi_Web/pull/1130)
* Date picker issues in default theme[#1126](https://github.com/ushahidi/Ushahidi_Web/pull/1126)
* Make nav helper more friendly and filterable for plugins and themes[#1125](https://github.com/ushahidi/Ushahidi_Web/pull/1125)
* Cannot enable clean URLs from admin[#1124](https://github.com/ushahidi/Ushahidi_Web/pull/1124)
* Unable to create new users, database error[#1122](https://github.com/ushahidi/Ushahidi_Web/pull/1122)
* Incident location not on incident pages[#1121](https://github.com/ushahidi/Ushahidi_Web/issues/1122)
* Table name escaping creates SQL error when there’s a table prefix[#1118](https://github.com/ushahidi/Ushahidi_Web/pull/1118)
* Error adding report to a photo (Crowdmap Classic)[#1099](https://github.com/ushahidi/Ushahidi_Web/pull/1099)


Ushahidi 2.7 (Bamako) - 02/05/2013
-------------------------------------

### Major changes

* Use OAuth to grab twitter feeds
* Better XSS protection
	- Add HTMLPurifier library for proper HTML sanitization
	- Add function to html helper: html::escape() html::strip_tags() html::clean()
	  These should be used instead of htmlentities, string_tags or other built in HTML cleaning functions
* Theming changes
	- Use CDN for theme files too [#904](https://github.com/ushahidi/Ushahidi_Web/issues/904)
	- Add theme inheritance and css/js overriding
		* This still default to including the default theme
		* Allows themes to specify CSS/JS files to include through readme.txt
		* Allow themes to override CSS/JS from parent theme by include a file of the same name
	- Split out themes/default/css/style.css
	- Handle all CSS / JS includes through 1 library: Requirements
		* This enables us to combine and compress these files
		* We're adding CSSMin and JSMin to compress files
		* A bunch of new options in application/config/requirements.php
	- Add support for RTL css files through Requirements library.
		* All CSS files can be replaced by a file of the same name with the -rtl suffix.
	- Further documentation here: https://wiki.ushahidi.com/display/WIKI/Managing+CSS+and+JS+in+Ushahidi
* Reworking reports upload and download
	- Adding support for upload/download of reports via XML format
	- Adding Form_id to downloaded CSV, allowing for import of reports/field responses matched with their respective forms [#792](https://github.com/ushahidi/Ushahidi_Web/issues/792).
		* Custom fields within different forms but with the same name shall be differentiated by the form_id appended to column names
* New hooks and events
	- Added hook for getting the incident object from the member's report controller [#891](https://github.com/ushahidi/Ushahidi_Web/issues/891)
	- Add new event to change members main tabs [#882](https://github.com/ushahidi/Ushahidi_Web/issues/882)
	- Add event to allow adding extra variables to a view [#550](https://github.com/ushahidi/Ushahidi_Web/issues/550)
	- Add report_save hook to incidents model [#913](https://github.com/ushahidi/Ushahidi_Web/issues/913)

### Other changes and fixes

* Removing hard coded HTTP requests
	- Add config.external_site_protocol setting to control if external requests use HTTP
* Ushahidi.js / Other mapping improvements
	- Restore Openlayers TMS support so cloudmade works again [#911](https://github.com/ushahidi/Ushahidi_Web/issues/911)
	- Fix broken map on /reports/view/XXX pages
	- Improve handling of marker selection in Ushahidi.js [#780](https://github.com/ushahidi/Ushahidi_Web/issues/780)
	- Fix handling for layer urls with query parameters
	- Make map helper handle TMS layers
	- Extend timeline by day to up to 6 month [#964](https://github.com/ushahidi/Ushahidi_Web/issues/964)
	- Make main map filter by start and end date on first load [#964](https://github.com/ushahidi/Ushahidi_Web/issues/964)
	- Set Google maps language based on current locale
	- Fix json/cluster when some reports have no location [#907](https://github.com/ushahidi/Ushahidi_Web/issues/907)
	- Improve JSON controller for easier extension [#853](https://github.com/ushahidi/Ushahidi_Web/issues/853)
	- Build cities list from OSM instead of Geonames [#979](https://github.com/ushahidi/Ushahidi_Web/issues/979)
* Custom forms fixes
	- Fix form field visibility/submission permissions [#744](https://github.com/ushahidi/Ushahidi_Web/issues/744)
	- Fix custom form fields with large list of select options [#906](https://github.com/ushahidi/Ushahidi_Web/issues/906)
	- Fix custom form fields permissions [#695](https://github.com/ushahidi/Ushahidi_Web/issues/695)
	- Don't assume all users have roles that are pushed in customforms helper.
	- Removing index in form_field table for those upgrading [#922](https://github.com/ushahidi/Ushahidi_Web/issues/922).
* API fixes
	- Comments API fixes [#918](https://github.com/ushahidi/Ushahidi_Web/issues/918)
	- Fix fatal errors in KML api
	- Fix for API authentication on installations that use CrowdmapID
	- Fix incidents API returning spam comments. Closes [#1002](https://github.com/ushahidi/Ushahidi_Web/issues/1002)
	- Make api?task=reports able to submit reports too [#988](https://github.com/ushahidi/Ushahidi_Web/issues/988)
	- Allow HTTP Basic Auth for authentication anywhere, not just API. Particularly useful for private deployments
	- Only reset session for non-ajax API requests [#791](https://github.com/ushahidi/Ushahidi_Web/issues/791)
	- Added support for custom fields in Ushahidi API
* Scheduler
	- Optimize cleanup scheduler to only load image media type
	- Scheduler: Add locking mechanism base on using mysql
	- Scheduler: Check and increase max_execution_time if its too low
* Optimizations
	* Adding indexes based on @jetherton's blog post to speed up sql queries
	* Optimize User_Model::has_permission() to only load roles once
	* Load feed items for feed block with the feed data in 1 query 
* Fixing and improving date fitlers:
	- Allow date filters with only 'from' or 'to' value, not both
	- Fix /reports date filter: make 'All Time' filter work [#91](https://github.com/ushahidi/Ushahidi_Web/issues/91)
	- Make fetch_incidents() date search from beginning till end of day [#220](https://github.com/ushahidi/Ushahidi_Web/issues/220)
* Other miscellaneous changes
	- Allow deleting multiple feed items [#981](https://github.com/ushahidi/Ushahidi_Web/issues/981)
	- Fix redirect to addons/plugins when clean urls are off. Closes [#1061](https://github.com/ushahidi/Ushahidi_Web/issues/1061)
	- Fix unicorn theme with man nav items. Closes [#952](https://github.com/ushahidi/Ushahidi_Web/issues/952)
	- Clarify what facebook settings are for. Closes [#1059](https://github.com/ushahidi/Ushahidi_Web/issues/1059)
	- Site banner setting: accept jpeg and add error message. Closes [#579](https://github.com/ushahidi/Ushahidi_Web/issues/579)
	- Fix incident rating: get total incident rating, not single rating entry
	- Delete form responses when deleting an incident.
	- Correct OSM attribution. Closes [#1029](https://github.com/ushahidi/Ushahidi_Web/issues/1029)
	- Fix public listing: Pass lat,lon of map center to public listing form.
	- Fix lat/lon checks on reports/edit form
	- Fix more info form when member logs in [#300](https://github.com/ushahidi/Ushahidi_Web/issues/300)
	- Fix [#993](https://github.com/ushahidi/Ushahidi_Web/issues/993) undefined variable when resetting password.
	- Fix missing table prefix when listing messages by reporter [#992](https://github.com/ushahidi/Ushahidi_Web/issues/992)
	- Rewrote a large portion of the CrowdmapID authentication driver to resolve character encoding issues and improve error handling.
	- Fix Category_Model::get_categories with prefixes in the database [#994](https://github.com/ushahidi/Ushahidi_Web/issues/994)
	- Support thumbnails for Videos
	- Better handling of youtube URL without v= first [#982](https://github.com/ushahidi/Ushahidi_Web/issues/982)
	- Better handling of missing settings in hooks/2_settings.php [#963](https://github.com/ushahidi/Ushahidi_Web/issues/963)
	- More information link on /reports [#935](https://github.com/ushahidi/Ushahidi_Web/issues/935)
	- Add config option to enable the profiler everywhere
	- Fix data switcher on /reports so it sits above the map
	- Make blocks::render() handle missing block classes gracefully [#916](https://github.com/ushahidi/Ushahidi_Web/issues/916)
	- Add extra class to custom field ```<tr>``` and check to show empty fields [#914](https://github.com/ushahidi/Ushahidi_Web/issues/914)
	- Fix error in reports::verify_approve() if no authenticated user [#912](https://github.com/ushahidi/Ushahidi_Web/issues/912)
	- Add admin reports search form [#220](https://github.com/ushahidi/Ushahidi_Web/issues/220)
	- Fix html escaping with UTF8 characters [#908](https://github.com/ushahidi/Ushahidi_Web/issues/908)
	- Make Settings_Model::save_setting() work when inserting new records too
	- Fix errors when signing up for mobile alerts [#895](https://github.com/ushahidi/Ushahidi_Web/issues/895)
	- Fix category::form_tree() not closing ```<li>``` and ```<ul>``` tags. [#905](https://github.com/ushahidi/Ushahidi_Web/issues/905)
	- Fixing bug: Editing a pre-existing incident as a member creates a duplicate incident [#897](https://github.com/ushahidi/Ushahidi_Web/issues/897)
	- Don't append country name to locations in /admin/reports. Fixes [#880](https://github.com/ushahidi/Ushahidi_Web/issues/880)
	- Add new lines between phone number [#879](https://github.com/ushahidi/Ushahidi_Web/issues/879).

Ushahidi 2.6.1 - Security Fix Release, 20-11-2012
-------------------------------------

* Vulnerability: Forgotten password challenge guessable. 

Ushahidi 2.6 (Tripoli), 23-10-2012
-------------------------------------
* Improved way of handling translations
	- Ushahidi translations are now managed through Transifex  - Ushahidi
	  translations are now managed through Transifex 
	- The Ushahidi-Localizations repo is now included in core through a
	  submodule. Rungit submodule update --init when you next pull from git or
	  git clone --recursive git://github.com/ushahidi/Ushahidi_Web.git to
	  clone and include submodules
	- All localizations will be shipped with core releases
	- In addition , support for category translations have been added to the
	  categories API
	- For more on Localization, check out the wiki
	  -> https://wiki.ushahidi.com/display/WIKI/Localization
* Support for JSONP  
	- We have added JSONP(http://en.wikipedia.org/wiki/JSONP) support to the API to allow cross-domain interaction.
* Zooming Controls - With an upgrade to openlayers 2.12 , several cool things have been added
	- kinetic dragging: http://openlayers.org/dev/examples/kinetic.html
	- animated panning:http://openlayers.org/dev/examples/animated_panning.html
	- zoom transitions: http://openlayers.org/dev/examples/transition.html / http://openlayers.org/dev/examples/google-v3.html
* Better handling of reports layer
	- Better handling of reports layer
	- Old layer not removed until new one loaded.
	- Fade between old/new zoom layer
	- No reload on zoom unless clustering
* Actions: The actions feature has had some more work done i.e
	- Added UI for editing and action
	- Added UI for deleting an action
	- Added actions trigger for geotagged feed items
* Refactor JSON controller for easier extension: Increases code reuse and add the following events
	- ushahidi_filter.json_replace_markers
	- ushahidi_filter.json_replace_markers
	- ushahidi_filter.json_features
* More events in main view
	- ushahidi_action.main_sidebar_pre_filters
	- ushahidi_action.main_sidebar_post_filters
* Add events to tweak get_incident / get_neighbouring_incidents SQL
	- ushahidi_filter.get_neighbouring_incidents_sql
	- ushahidi_filter.get_incidents_sql
* Rework Upload and Download Feature
	- Upload of custom form fields
	- Case insensitivity issues fixed - Previously, csv content in lower case
	  would not be recognised and would cause upload failure, forcing users to
	  adhere to uppercase content for particular fields. Users can now upload
	  content regardless of what case the csv content is in.
	- Added support for upload/download of Incident reporters
	- Compatibility issues with CSV files generated by non UNIX systems


Ushahidi 2.5 (Cairo), 01-08-2012
-------------------------------------

* Mapping: 
	- The mapping code has been refactored and decoupled from the timeline. 
	- There are now events triggered for actions such as zoom changes, changine layer, etc. , which can be used to extend mapping function
	- The timeline can now be turned on or off.
* Themes: 
	- The views have been namespaced on a per-controller basis.
	- Views for the front-end (including JS) have all been moved to the themes folder
* Settings table:
	- The structure for the settings table has been modified so that data are stored as key/value pairs.
* Installer:
	- The installer has been reworked in order to work with the new settings table structure
	- Additionally, we now perform the installation check in index.php
* Configuration files:
	- The following files are no longer in the repository: config.php, auth.php and encryption.php.
		In their place are templates (.template.php files) which are used by the installer to generate the respective config files.
* Roles and Permission:
	- User permissions have been refactored into a separate table form roles. This now allows plugins to add their own permissions.
* Alerts
	- Allow admin to view, search and delete user alerts in the system
* Security fixes thanks to OWASP Portland
	- Multiple SQL injections  (Thanks: Timothy D. Morgan, Kees Cook, postmodern)
	- Missing authentication on comments, reports and email API calls  (Thanks: Kees Cook, Dennison Williams)
	- Admin user hijacking through the installer  (Thanks: Wil Clouser)
	- Stored XSS on member profile pages  (Thanks: Amy K. Farrell)
	- User data exposed in comment API
* Further details on migrating to 2.5 are available on the wiki
  -> https://wiki.ushahidi.com/display/WIKI/Migrating+to+Ushahidi+2.5


Ushahidi 2.4.1 - Upgrader fix release, 01-08-2012
-------------------------------------

* Add support for removing old files during upgrade (#716)
* Clear cache after new files copied over
* Fix for DB upgrades 


Ushahidi 2.4 - Bug fix release, 30-05-2012
-------------------------------------

* Fix A 500 error that was being thrown by the Scheduler (#387)
* Category icons now showing up on the map (#390)
* Fix map styling on the Actions/Triggers module (#435)
* Security fixes
	 - Cross Site Scripting (XSS)
* More on the bugs fixed can be found here:
	-> https://docs.google.com/a/ushahidi.com/spreadsheet/ccc?key=0AizezfooB3k9dC1GX3RsNl9ocnQ3U1lmTUtSbFAwMlE#gid=7



Ushahidi 2.3.2 - Bug fix release, 08-05-2012
-------------------------------------

* Security fix for Admin API - prevent member role from accessing the admin API. (SA-WEB-2012-005)
* Fix Google maps base layers - base layer type were being incorrectly escaped
* Fix session driver change from 2.3.1 - Set session driver back to cookie as other drivers are buggy

Ushahidi 2.3.1 - Security fix release, 30-04-2012
-------------------------------------

* Security fix for session storage - sessions from any deployment were valid on any other deployment (SA-WEB-2012-004)

Ushahidi 2.3 - (Juba) Bug fix release, 24-04-2012
--------------------------
* Cleaned up database Schema
* Few improvements on the installer 
	– This includes the introduction of an admin login email configuration.
	- Hiding of admin password once installation is complete.
* Added HTML editing and more attributes to the page editor. 
* Security fixes
    - Cross Site Request Forgery (CSRF)
    - Cross Site Scripting (XSS)

Ushahidi 2.2.1 - bug fix release, 04-04-2012
--------------------------
* Map JS unification (#278)
* Make Scheduler JS options (#331)
* Update child categories of parent category that was reassigned to id 999 updated to match the correct parent_id 999 and not 5 (#322) 
* Zoom level is never set on Get Alerts map (#358)
* Security fixes
    - JSON controller returned non-approved reports
    - JSON controller exposed on private deployment
    - Markers controller exposed on private deployment
    - json/single/ID SQL injection vulnerability
    - Download reports (admin) could inadvertently return non-approved reports

Ushahidi 2.2 (Juba), 13-03-2012
---------------------------------
* Riverid Integration
    - RiverID is an authentication and identity management system that provides users with a secure central sign-on facility.
    - With RiverID, your are able to access all the Ushahidi products using just one username and password. This includes all the Crowdmaps you've set up and, when launched, your SwiftRiver streams.
    - This eliminates the need for multiple passwords per person per Crowdmap deployment. Though, you are free to use as many different passwords as you choose.
* Base Map defaulted to OSM
    - The default map version has been set to OpenStreetMap (OSM). There is a drop-down to select your map. You can select to use a variety of maps including Google.
* Badges
	- Badges are a great way for Ushahidi deployers to award their users.
	- Developers can also find badge image resources to include in their projects.
	- These are badge images in a variety of categories which can be used in Ushahidi or Crowdmap deployments or other services.	
* Automated Actions
    - The admin panel now allows you to set up automated actions on your deployment.
	- You are now able to set a chain of events into motion when certain conditions that you specify are hit.
* Alerts
    - Subscription of Alerts via SMS.
    - Unsubscribing from mobile alerts.
* Security
    - Plugged SQL and HTML injection vulnerabilites
* Plugins
    - Sharing feature moved into a plugin
	- New plugins, adsense and Viddler
* Public Listing
    - Plublic listing of deployment which increases your deployment's discoverability.
* Features changed on core
    - Changed the Pages editor from Tinymce to JWYSIWYG
* Themes
	- More themes to choose from.


Ushahidi 2.1 (Tunis), 09-08-2011
---------------------------------
* Usability
    - A new multi-faceted reports listing page that provides the user with the ability
    to filter reports using a variety of options such as categories, verification status, report submission channel
    - Centralized the validation and saving of reports. This is now handled by the reports helper
    - Streamlined the list of parameters for filtering reports on the frontend
    - Implemented category sorting in a drag and drop fashion
    - A new blocks feature to allow the user to toggle the display of certain sections on the front-end
    - Enhanced the custom forms feature (courtesy of the Konpa Group) to better augment data collection via the reports submission page
    - Ability to add geometries (polygons) to a report
    - Option to get a taller/wider map in the report viewing page
* Checkins (Experimental)
    - Checkins system
* Facebook Module
* Member Module
    - New member module that allows people to log in to a deployment via OpenID or a username/password
    pair specific to the deployment
* Performance
    - Refactored the json controller to fetch all the reports via a single query instead of using multiple
    queries
    - Switched to straight SQL instead of ORM for fetching the reports
* Security
    - Plugged SQL injection vulnerabilites
* Testing
    - Added test framework (PHPUnit for unit tests and Selenium for functional tests) and tests
* JavaScript
    - Switched from GML to Vector layers in timline.js for both the reports and KML layers on the main map
* Features removed from the core
    - Removed Laconica from the list of message services. No longer supported

Ushahidi 2.0 (Luanda), 22-11-2010
---------------------------------
* Usability
    - Improved reports listing to convey different sets of information quickly and in a practical way
* Themes
    - Themes can now be created without having to tamper with the code.
* API
    - Refactored the API controller and implemented the API system in a modular fashion. 
      API tasks/features are now availed by way of libraries
* Plugins
    - New plugin architecture based on Kohana Events
    - Implemented FrontlineSMS and Clickatell SMS services as plugins and added them to the list of stock plugins
    - SMSSync plugin for the SMSSync Android application
* Scheduler
    - Ability to schedule tasks in cron like fashion
* Upgrader
    - An upgrader to automatically update the platform the latest release
    
Ushahidi 1.2 (After Haiti in Jan 2010)
--------------------------------------
* Usability
    - A collapsible category tree on the submit report page
* Clustering
    - Clustering of reports is now on the server side. It was previously being done on the client side via JavaScript
* Custom Forms
    - A custom forms feature to collect additional information in addition to that in the "Submit Report" page
* Report Edit Logs
    - Edit logs for a report are now available so a user of the admin console can see the series of changes/revisions/edits 
      that have been made to a report

Ushahidi 1.0 (Mogadishu) 10-12-2009
-----------------------------------
* A web installer is now bundled with the platform to ease the process of deploying/installing the platform
* New CSS based design that is easier to skin
* Admin email notifications on emails and comments
* Site statistics and analytics
* Feature to add custom pages and tabs
* Auto-geotagging of news feeds
* Ability to create reports from news feeds
* Feature to add KML/KMZ overlays on the map
