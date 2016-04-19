Generating translations
=======================

Transifex Client - http://docs.transifex.com/developer/client/
-------------------------------------------------------------

### Upload translations to transifex

#### Uploading source

    tx push -s

#### Uploading translations (inluding --skip to skip errors such as empty translation files)

    tx push -t --skip

### Downloading translations from transifex

    tx pull

#### Download translations for just one language

    tx pull -l fr_FR

#### Resync strings from Transifex (push source and pull translations) ###
Make sure you have the entry below in your ~/.transifexrc file

    [https://www.transifex.net]
    hostname = https://www.transifex.net
    username = username
    password = password
    token =

Then run

    tools/full-sync.sh

*You don't need to run this manually* This is run on a cronjob on qa.ushahididev.com

This script assumes there is a Ushahidi deployment on the same level as the 
Ushahidi-Localization repo, with the
[kohana-i18n-manager](https://github.com/rjmackay/kohana-i18n-manager)
module enabled.

### Adding a new language

To add a new language simply create the language through the
[transifex web UI](http://transifex.net/projects/p/ushahidi-v2/)
The new language will be picked up automatically when cron runs.

#### Adding a language with existing translation files

1. Copy the translation files into Ushahidi-Localization repo
2. Convert the php files to po using kohana-i18n-manager.
Run this in the web root (swapping xy_ZW for the language code):

  ```
  php index.php "i18n/php2po?lang=xy_ZW"
  ```

3. Commit the new php and po files to github    
4. Push the translations to transifex

  ```
  tx push -t --skip -l xy_ZW
  ```

5. The translations from now on should be edited in the Transifex client
and will sync to github automatically.

### Adding a new resource

Each PHP file in the i18n/en_US directory maps to a different resource in Transifex.
When a new file is added to the PHP source files we need to tell transifex about it.

1. Create the new php file in en_US (for example en_US/incident.php)
2. Create a pot file to get started

  ```
  php index.php "i18n/php2po?lang=en_US&group=incident"
  ```

3. Edit .tx/config and add the resource to the end of the file
(You can do this using the tx client but its safer to edit the .tx/config file manually)

  ```
  [ushahidi-v2.incident]
  file_filter = po/po-<lang>/incident.po
  source_file = po/po-en_US/incident.pot
  source_lang = en_US
  type = PO
  ```
4. Push the file to transifex

  ```
  tx push -s -r ushahidi-v2.incident
  ```

Converting PHP files to PO and back
-----------------------------------

This is done using the Kohana i18n manager module, see that module repo for docs
https://github.com/rjmackay/kohana-i18n-manager

