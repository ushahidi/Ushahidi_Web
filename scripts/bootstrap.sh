#!/usr/bin/env bash

# Variables
MYSQL_USER="root"
MYSQL_PASSWORD="root"

echo -e "\n--- Updating packages list ---\n"
apt-get -qq update

# Servers & PHP5
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password ${MYSQL_PASSWORD}"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password ${MYSQL_PASSWORD}"

echo -e "\n--- Installing servers and dependencies ---\n"
apt-get install -y mysql-server apache2 libapache2-mod-php5 php5 php5-mysql php5-mcrypt php5-curl php5-imap php5-gd > /dev/null 2>&1

cat > /home/vagrant/.my.cnf <<EOF
[client]
user = ${MYSQL_USER}
password = ${MYSQL_PASSWORD}
EOF

echo -e "\n--- Creating the Apache virtualhost ---\n"
rm /etc/apache2/sites-enabled/000-default
cat > /etc/apache2/sites-enabled/000-default << "EOF"
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /vagrant
    DirectoryIndex index.php
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

echo -e "\n--- Enable Apache2 modules ---\n"
a2enmod rewrite > /dev/null 2>&1

echo -e "\n--- Restarting Apache ---\n"
service apache2 restart > /dev/null 2>&1

echo -e "\n--- All done! :) ---\n"
