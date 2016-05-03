#!/bin/bash
# Initialize .tx/config for all files
# Does not need to be done again
exit

cd ../po
for f in po-en_US/*.pot
do
	fname=$(basename $f)
	fbname=${fname%.*}
	tx set --auto-local -r ushahidi-v2.$fbname "po-<lang>/$fbname.po" --source-lang en_US --source-file po-en_US/$fbname.pot --execute
done

