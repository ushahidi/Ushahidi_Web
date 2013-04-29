# Changes

## master (not tagged yet)

## Version 0.97.2 (From infinity)

* Preparing to jQuery 1.6 - akzhan
* Fixed infinite loops - frost-nzcr4
* rmFormat a bit improved - frost-nzcr4
* Fixed issue in Firefox when insertHtml is used - frost-nzcr4
* Useless disabled attribute on LI tags has been removed, disabled class added instead of - akzhan

## Version 0.97.1 (Follow me)

Take a note that version number in jquery.wysiwyg.js is not changed to make Drupal users happy.
Also I want to note that 0.97.1 supports ECMAScript strict mode.

* jslint (fixes for common asset compressors) - filiptepper
* Fix paste issues for Microsoft Word formatter - frost-nzcr4

## Version 0.97 (Next step)

* Bugfixes and core enhancements - frost-nzcr4
* jslint - frost-nzcr4, akzhan
* Documentation - vjt, ctide, afilina, elektronaut
* Controls: increase/decrease font size, highlight, code - vjt, ctide, Tudmotu
* Sorting controls by user - vjt
* New options: initialMinHeight and maxLength - ctide
* Enhance modal dialogs - ctide
* New API method selectAll - ctide
* Fix dialog in image control - simsalabim
* CSS fixes - Jason Orrill, afilina
* Switch to Uglify compressor - akzhan
* Add Jasmine test suite - frost-nzcr4
* Dev tools to generate unicode entities - EvanCarroll, akzhan
* Enhance event handler - brentkirby
* Fixed bugs in event system - EvanCarroll
* Editor specific event system - alecgorge
* XHTML5 and Unicode Entity Handling - EvanCarroll, alecgorge
* Dutch locale for jwysiwyg - Erik van Dongen
* Polish locale for jwysiwyg - aherok

## Version 0.96 (Pretty girl)

* Plugin rmFormat: fix for Word and IE markup - SugaSlide
* Proper dialog focus with IE7/8 - frost-nczr4, academo
* Closure for autoSaveFunction - mbj
* New options - mbj, frost-nczr4
* Encode entities - alecgorge
* Link control - academo
* Fixes for Internet Explorer - frost-nczr4
* Updated structure of repository and GitHub pages - akzhan
* Brazilian Portuguese locale for jwysiwyg - Marcelo Wergles
* Czech locale for jwysiwyg - deepj
* German locale for jwysiwyg - mbj
* Italian locale for jwysiwyg - maurofranceschini
* Japanese locale for jwysiwyg - rosiro
* Slovenian locale for jwysiwyg - peterz
* Spanish locale for jwysiwyg - academo
* Ability to translate dialogs - frost-nczr4

## Version 0.95 (Kino)

* Directory structure of repository has been reorganized to be more friendly for Drupal users - frost-nzcr4, sun
* Plugins API implemented - frost-nzcr4
* Internationalization API implemented - frost-nzcr4
* Color picker plugin - arincool, frost-nzcr4
* rmFormat plugin - frost-nzcr4
* Some core functionality has been splitted into plugins and has been extended (like inserting of images or tables) - frost-nzcr4
* Image tag editing implemented in image control - frost-nzcr4
* jQuery UI Dialog integration code fixed - mydevel
* "this.get is not a function" error when trying to add a link fixed - everlee
* In the saveContent function, the html needs to be saved if in html view - Justin Lewis
* Multiple fixes to eliminate using of for..in loop - rych, frost-nzcr4, akzhan
* Insertion of images and tables should trigger autogrow - J. Weir
* loadCss option has been renamed to autoload - frost-nzcr4
* Massive update of documentation - frost-nzcr4
* jslint issues - akzhan, frost-nzcr4
* Fixed exception in s.addRange() when savedRange is undefined - frost-nzcr4
* new rmUnusedControls option added (see #52) - frost-nzcr4
* French locale for jwysiwyg - MappaM
* Swedish locale for jwysiwyg - ippa
* Russian locale for jwysiwyg - frost-nzcr4

## Version 0.94 (phase 2)

* focus is properly returned after clicking on buttons - alecgorge
* fix for getContent operation by class selector - alecgorge
* new option for custom toolbar items (look at tests/issue 26.html for details) - alecgorge
* fix IE8/XP compatibility issue - jsch
* Fix incorrect handling of iFrameClass option - bbrewder
* Refactoring - frost-nzcr4
* Fixing of destroy, documentSelection in ltr, rtl  modes - frost-nzcr4
* Adding of CSS autoload, initialContent option - frost-nzcr4
* Adding of autoGrow option - Lukom
* Use Cmd key on Macs - boutell
* JSlint fixes - akzhan, filiptepper

## Version 0.93 (koken)

* Hide wysiwyg while html shown - akzhan
* jwysiwyg destroy fixed - jalada
* jwysiwyg iframe body now marked with wysiwyg class - TheQueenDrinksTea
* Common save event for catching all the modifications added - Janne Hietamaki
* Custom handler for chaining toolbar ordering etc. added - Janne Hietamaki
* Iframe now can use class name to be styled - chris.haumesser
* Version string in source file must ended with version number for Drupal integration folks - xeto

## Version 0.92 (arigatou gozaimasu)

* Fix work under quirks mode of Internet Explorer - kris.schwab
* Workaround for Mozilla/WebKit misfunctionality of RemoveFormat over headings - aiveldesign
* Experimental support for switching between LTR/RTL modes (no icons provided and markup issues) - abduljawad.mahmoud
* More robust selection check in createLink - systeembeheer

## Version 0.91 (maintenance release)

* Editor now throw errors on unknown actions - akzhan
* Getter methods were broken. Fixed - wordituk
* headings formatting has been fixed in IE, Firefox and Chrome - kolpak

## Version 0.9 (maintenance release)

* Buttons are unselectable now and have no anchors (CSS reviewed) - mrapczynski
* Way to return focus to editor has been corrected - mappam0
* $.fn.documentSelection has been removed to minimize pollution of $.fn namespace - akzhan
* Source mode fixed for all browsers (was inspired by 0.8)  - silvermuru

## Version 0.8 (revival)

+ enabled, destroy, removeFormat, save actions added to $('#elt').wysiwyg(action) - fomojola
+ insertTable action/button added - academo
+ insertTable and insertImage buttons now support jQuery UI Dialog and SimpleModal plugin - academo
+ Event handlers supported through events - akzhan
+ Editor now supports jQuery UI resizable plugin through resizeOptions - akzhan
* jWysiwyg now wraps Mozilla bug that disables editor creation in AJAX calls - akzhan
* *MSG_EN* and *TOOLBAR* replaced with $.fn.wysiwyg.defaults/controls - akzhan
* separators replaced with group indexes - akzhan
* Directory structure reorganized - academo
* $.fn.document has been removed to minimize pollution of $.fn namespace - akzhan

## Version 0.7

* Ctrl+B, Ctrl+I and Ctrl+U keystrokes in non-IE browsers now works like IE ones - akzhan
* insertHtml in non-focused editor works now - akzhan
* Appearance of toolbar buttons fixed under IE7/8 - ibnteo, mail2lx
! Code reviewed.

## Version 0.6

* New $().wysiwyg('insertHtml', string) method - akzhan
* New example (nearby full editor) added - deansofer
* CSS styling replaced with tags in Firefox -  Svel.Sontz, tobinl, AndreyKostromin ans others
* Minor problems with focus in non-IE fixed - tyler.schacht
* Correct setup of form events - Mickael.Hoareau
* Editor now correctly sets its tabindex on initialization - denis.vysotskiy
* Insertion of headers in safari fixed - gpearman
* More correct styling of editor - gamingforever
* Correct behaviour on IE using https protocol - jf.stgermain
* Incorrect initialization of editor when content contains "$" characters fixed - JackPDouglas
* Improved CSS degradability - deansofer
* Improved ARIA accessability - akzhan
! Requires jQuery 1.3 or higher! Tested under jQuery 1.4 too.

