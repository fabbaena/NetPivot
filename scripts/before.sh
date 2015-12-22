#!/bin/bash

CONFFILE=/home/ubuntu/.my.cnf

HOST=localhost
USER=demonio
PASSWORD=password
DBNAME=NetPivot

DBDIR=/var/lib/mysql/$DBNAME

depends() {
    local PACKAGES=( apache2 mariadb-server mariadb-client php5 php5-mysqlnd )

    DEBIAN_FRONTEND=noninteractive apt-get -y update
    DEBIAN_FRONTEND=noninteractive apt-get -y upgrade
    DEBIAN_FRONTEND=noninteractive apt-get -y dist-upgrade
    DEBIAN_FRONTEND=noninteractive apt-get -y autoremove

    for i in ${PACKAGES[*]}; do
	local INSTALLED=`dpkg-query -W --showformat='${db:Status-Abbrev}\n' $i`
	if [ "$INSTALLED" != "ii" ]; then
	    DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends $i
	fi
    done
}

backup() {
    local DBDUMP=/home/ubuntu/netpivot/netpivot-`date +%F_%H-%M-%S%z`.sql
    if [ ! -d `dirname ${DBDUMP}` ]; then
	mkdir -p `dirname ${DBDUMP}`
    fi

    mysqldump --defaults-file=${CONFFILE} --compact -c --delayed-insert -e -f -n -t -q --single-transaction --tz-utc --skip-quote-names ${DBNAME} > ${DBDUMP}
}

create() {
    local DBCREATE=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/db_create.sql
    #local DBCREATE=/home/ubuntu/codedeploy/scripts/db_create.sql
    mysql --defaults-file=${CONFFILE} -f -q -s < ${DBCREATE}
}

alter() {
    local DBALTER=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/db_update.sql
    #local DBALTER=/home/ubuntu/codedeploy/scripts/db_update.sql
    mysql --defaults-file=${CONFFILE} -f -q -s < ${DBALTER}
}

if [ ! -f ${CONFFILE} ]; then
    echo "[mysqldump]" >> ${CONFFILE}
    echo "host=${HOST}" >> ${CONFFILE}
    echo "user=${USER}" >> ${CONFFILE}
    echo "password=${PASSWORD}" >> ${CONFFILE}
    echo "[mysql]" >> ${CONFFILE}
    echo "host=${HOST}" >> ${CONFFILE}
    echo "user=root" >> ${CONFFILE}
    echo "password=s3cur3s0c" >> ${CONFFILE}
fi

if [ ! -d ${DBDIR} ]; then
    depends

    rm -f /var/www/html/index.html
    invoke-rc.d --quiet mysql status
    if [ $? -gt 0 ]; then
        invoke-rc.d --quiet mysql start
    fi
    mysql --no-defaults --no-auto-rehash -q -s -u root -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('s3cur3s0c');"

    create
else
    backup
    alter
fi

