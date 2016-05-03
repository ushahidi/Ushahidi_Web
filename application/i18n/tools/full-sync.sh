#!/bin/bash
# Run full sync
# Pulls translations from Transifex and converts to PHP
# Converts source strings to POT files and pushes to transifex
#
# This assumes there is a Ushahidi deployment on the same level
# as the Ushahidi-Localization repo, with the kohana-i18n-manager
# module enabled. (https://github.com/rjmackay/kohana-i18n-manager)

cd `dirname $0`/..

# Get latest translations from transifex, run po2php conversion on translations and push to github

echo "Pulling in latest changes from github..."
git pull

echo "Pulling changes from transifex"
# might need --force and --skip
tx pull --force --all

# generate the php files from transifex po files
echo "Generating php files from translations..."
cd ../../
php index.php "i18n/po2php"
cd application/i18n/

# add any new file generated
git add .

echo "Commiting changes..."
git commit -am 'Daily update from transifex'

echo "Pushing changes to github repo..."
git push

echo "Done!"

# Run php2po conversion on en_US and then sync the generated po file to github for transifex to pick it up

echo "Pulling in latest changes from github..."
git pull

# generate the pot files for en_US so transifex can pick it up
echo "Generating po files for en_US..."
cd ../../
php index.php "i18n/php2po?lang=en_US"
cd application/i18n

# add any new file generated
git add po/po-en_US

echo "Commiting changes..."
git commit -m 'Generate daily po for en_US'

echo "Pushing changes to github repo..."
git push

# Push changes to transifex
# Don't need to specify language here, transifex knows the source language is english
echo "Pushing changes to transifex"
tx push -s --skip

echo "Done!"

echo "All done!"
