#!/usr/bin/env bash

umount -v /var/www/html

apt-get install --no-install-recommends -y wget rsync
if [ ! -f /etc/apt/sources.list.d/pgdg.list ]; then
    echo "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" > /etc/apt/sources.list.d/pgdg.list
    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
fi
apt-add-repository -y ppa:phalcon/stable

apt-get -y update
apt-get -y upgrade
apt-get -y dist-upgrade
apt-get -y autoremove

#APACHE
apt-get install --no-install-recommends -y apache2 apache2-utils ssl-cert openssl-blacklist
service apache2 stop
a2dismod *
a2enmod mpm_prefork authn_core authz_core rewrite dir access_compat
sed -i\.orig -e "s/AllowOverride None/AllowOverride FileInfo/" /etc/apache2/apache2.conf
service apache2 start
#rsync -avz -PC --delete /vagrant/frontend/ /var/www/html/
#chown -cR www-data.www-data /var/www/html

#POSTGRESQL
apt-get install --no-install-recommends -y postgresql-9.5 postgresql-9.5-ip4r postgresql-contrib-9.5
su -l postgres -c "psql -b -q -f /vagrant/scripts/pgsql_create.sql"

#PHP
apt-get install --no-install-recommends -y libapache2-mod-php5 php5-phalcon php5-pgsql php5-memcache php5-mcrypt php5-xcache

mount.vboxsf -w -o uid=33,gid=33 content /var/www/html
mkdir -pvm 0755 /var/www/files
chown -cR www-data.www-data /var/www/files

