=== About ===
name: Analysis Matrix
website: http://www.ushahidi.com
description: Analyse incoming reports to find related items by proximity, time, category etc. Developed with the help of ICT4Peace.
version: 0.5
requires: 2.2
tested up to: 2.2
author: David Kobia
author website: http://www.dkfactor.com

== Description ==
Analyse incoming reports to find related items by proximity, time, category etc. Developed with the help of ICT4Peace.

== Installation ==
1. Copy the entire /analysis/ directory into your /plugins/ directory.
   (Note: the directory MUST be named analysis, not Ushahidi-plugin-analysis)
2. Activate the plugin.
3. Go to Admin, Click on Reports Tab - you should see Analysis Menu Option at Top Right

== Changelog ==
0.5
* Added 'Analysis' buttons to the frontend and linked to edit/view pages
* Added special 'Analysis' category
* Move source and information fields to a seperate table, out of core

0.3
* Added Information Evaluation Grid to Report Analysis
* Updated Mapping to Include New Map Helper

@todo Add API hooks for incident source and information fields (removed from core)
