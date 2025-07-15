#!/bin/bash

set -e

echo "Configuring Ushahidi_Web... "

cat application/config/auth.template.php \
  > application/config/auth.php

cat application/config/config.template.php \
  > application/config/config.php

# DB_USERNAME
# DB_PASSWORD
# DB_HOST
# DB_PORT
# DB_DATABASE

cat application/config/database.template.php \
  | sed -E -e "s/('user' => )('username')/\\1'${DB_USERNAME}'/" \
  | sed -E -e "s/('pass' => )('password')/\\1'${DB_PASSWORD}'/" \
  | sed -E -e "s/('host' => )('localhost')/\\1'${DB_HOST}'/" \
  | sed -E -e "s/('port' => )(FALSE)/\\1'${DB_PORT:-3306}'/" \
  | sed -E -e "s/('database' => )('db')/\\1'${DB_DATABASE}'/" \
  > application/config/database.php

# SITE_DEFAULT_KEY

cat application/config/encryption.template.php \
  | sed -E -e "s/USHAHIDI-INSECURE/${SITE_DEFAULT_KEY:-USHAHIDI-INSECURE}/" \
  > application/config/encryption.php


dockerize -wait tcp://${DB_HOST}:${DB_PORT:-3306} -timeout 60s

exec "$@"
