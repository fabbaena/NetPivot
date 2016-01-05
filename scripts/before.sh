#!/bin/bash

CONFFILE=/home/ubuntu/.my.cnf

HOST=localhost
USER=demonio
PASSWORD=s3cur3s0c
DBNAME=NetPivot

DBDIR=/var/lib/mysql/$DBNAME

backup() {
    local DBDUMP=/home/ubuntu/netpivot/netpivot-${DEPLOYMENT_ID}.sql
    local BACKUP=/home/ubuntu/netpivot/netpivot-${DEPLOYMENT_ID}.txz

    if [ ! -d `dirname ${DBDUMP}` ]; then
	mkdir -p `dirname ${DBDUMP}`
    fi

    if [ ! -f ${DBDUMP} ]; then
	mysqldump --defaults-file=${CONFFILE} --compact -c --delayed-insert -e -f -n -t -q --single-transaction --tz-utc --skip-quote-names ${DBNAME} > ${DBDUMP}
    fi

    if [ ! -f ${BACKUP} ]; then
	tar -cJf ${BACKUP} -C /var/www/html .
    fi
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

invoke-rc.d --quiet mysql status
if [ $? -gt 0 ]; then
    invoke-rc.d --quiet mysql start
fi

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
    rm -f /var/www/html/index.html

    mysql --no-defaults --no-auto-rehash -q -s -u root -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('s3cur3s0c');"
    create
else
    backup
    alter
fi

