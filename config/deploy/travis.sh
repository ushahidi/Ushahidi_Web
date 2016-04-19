#!/usr/bin/env bash

mkdir -p ~/.ssh
mv config/deploy/id_rsa_deploy ~/.ssh
chmod -R 0600 ~/.ssh
chmod 0700 ~/.ssh

echo '
Host mapa.desastre.ec
  Port 2231
' > ~/.ssh/config

cap staging deploy
