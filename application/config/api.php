<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * The Ushahidi API configuration
 *
 * This file contains entries for routing API tasks whose libraries files cannot be inferred from the
 * the name of the API task. API task handlers are first looked up in this file before the API service
 * attempts to determine the name of the implementing library from the API task name
 *
 */
 $config = array
 (
     // Version
     "version" => array("System", "get_version_number"),

     // MHI Enabled
     "mhienabled" => array("System", "get_mhi_enabled"),
     
     // SSL Enabled
     "httpsenabled" => array("System", "get_https_enabled"),
     
     // Map center
     "mapcenter" => array("Private_Func", "map_center"),
     
     // Statistics
     "statistics" => array("Private_Func", "statistics"),

     "sms" => array("Private_Func", "sms"),
 
     "country" => "Countries", 
     "location" => "Locations",
     "3dkml" => "Kml",
     
     // Geographic midpoint
     "geographicmidpoint" => array("Incidents", "get_geographic_midpoint"),
     
     // Incident count
     "incidentcount" => array("Incidents", "get_incident_count"),
     
     // Media tagging
     "tagnews" => "Tag_Media",
     "tagvideo" => "Tag_Media",
     "tagphoto" => "Tag_Media",
 
     // Admin report functions 
     "reports" => "Admin_Reports",
                         	
     // Category Action 
     "category" => array("Admin_Category","category_action"),

     // Comment Action
     "comments" => "Comments",
     
     // Twitter action
     "twitteraction" => array( "Twitter", "twitter_action"),
     
     // Email action
     "emailaction" => array( "Email", "email_action"),
     
     // SMS action
     "smsaction" => array( "Sms", "sms_action"),

     // Swiftriver action
     "swiftriver" => "Swiftriver_Report",
 );
