#!/bin/bash

set -x
GITPATH=/usr/src/develop
DBPATH=${GITPATH}/scripts
CONTENTPATH=${GITPATH}/content
PASS="$4manaDefault"

sudo echo "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" \
    > /etc/apt/sources.list.d/pgdg.list
sudo wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    | sudo apt-key add -
sudo apt update
sudo apt install -y postgresql-9.5

sudo sed -i -e "s|^\(local\s\+all\s\+all\s\+\)peer|\1md5|" \
        /etc/postgresql/9.5/main/pg_hba.conf

sudo /etc/init.d/postgresql restart

sudo apt install -y git
cat <<EOF > ~/.pgpass
localhost:5432:netpivot:demonio:s3cur3s0c
localhost:5432:template1:demonio:s3cur3s0c
EOF
chmod 600 ~/.pgpass


## Install database
sudo mkdir -p ${GITPATH}
sudo chown ubuntu:ubuntu ${GITPATH}
git clone -b develop git@github.com:/fabbaena/NetPivot ${GITPATH}
sudo su - postgres sh -c psql -f ${DBPATH}/pgsql_create.sql
psql -U demonio -b -f ${DBPATH}/pgsql_update.sql netpivot
psql -U demonio -b -f ${DBPATH}/adc_hw.sql netpivot
psql -U demonio -b -f ${DBPATH}/CRMTables.sql netpivot
psql -U demonio -b -f ${DBPATH}/F5NSLink.sql netpivot
ADMINPASS=$(php5 -r "echo password_hash(\"$PASS\", PASSWORD_BCRYPT);)"
psql -U demonio -c "update users set password='$ADMINPASS' where id=1" netpivot

sudo apt install -y apache2 php5 php-pear php-mail php5-json php5-pgsql php5-readline

cat <<EOF | sudo tee /etc/apache2/conf-available/netpivot.conf
<Directory /usr/src/develop/content>
    Options Indexes FollowSymLinks
    AllowOverride None
    Require all granted
</Directory>
EOF

cd /etc/apache2/conf-enabled
sudo ln -s ../conf-available/netpivot.conf
sed -i -e "s|\(\s\+DocumentRoot \).*|\1 ${CONTENTPATH}|" \
    /etc/apache2/sites-enabled/000-default.conf

sudo mkdir /var/www/files
sudo chown www-data.www-data /var/www/files
sudo /etc/init.d/apache2 reload

wget https://s3-us-west-2.amazonaws.com/netpivotkernel/netpivotkernel.tgz -P /var/tmp
tar -xzvf /var/tmp/netpivotkernel.tgz -C /var/tmp
sudo sh /var/tmp/installer.sh
rm /var/tmp/netpivotkernel.tgz /var/tmp/installer.sh
