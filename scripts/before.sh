#!/bin/bash

CONFFILE=/home/ubuntu/.my.cnf

HOST=localhost
USER=demonio
PASSWORD=s3cur3s0c
DBNAME=NetPivot

DBDIR=/var/lib/mysql/$DBNAME

create() {
    local DBCREATE=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/db_create.sql
    #local DBCREATE=/home/ubuntu/codedeploy/scripts/db_create.sql
    mysql --defaults-file=${CONFFILE} -f -v -s < ${DBCREATE}
}

alter() {
    local DBALTER=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/db_update.sql
    #local DBALTER=/home/ubuntu/codedeploy/scripts/db_update.sql
    mysql --defaults-file=${CONFFILE} -f -v -s < ${DBALTER} && exit 0
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

    mysql --no-defaults --no-auto-rehash -v -s -u root -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('s3cur3s0c');"
    create
else
    alter
fi

