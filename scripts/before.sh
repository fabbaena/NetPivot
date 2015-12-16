#!/bin/bash

CONFFILE=/home/ubuntu/.my.cnf

DBDUMP=/home/ubuntu/netpivot/netpivot-`date +%F_%H-%M-%S%z`.sql
DBCREATE=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/db_create.sql
DBALTER=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/db_update.sql
#DBCREATE=/home/ubuntu/codedeploy/scripts/db_create.sql
#DBALTER=/home/ubuntu/codedeploy/scripts/db_update.sql

HOST=localhost
USER=demonio
PASSWORD=password
DBNAME=NetPivot

DBDIR=/var/lib/mysql/$DBNAME

backup() {
    if [ ! -d `dirname ${DBDUMP}` ]; then
	mkdir -p `dirname ${DBDUMP}`
    fi

    mysqldump --defaults-file=${CONFFILE} --compact -c --delayed-insert -e -f -n -t -q --single-transaction --tz-utc --skip-quote-names ${DBNAME} > ${DBDUMP}
}

create() {
    mysql --defaults-file=${CONFFILE} -f -q -s < ${DBCREATE}
}

alter() {
    mysql --defaults-file=${CONFFILE} -f -q -s < ${DBALTER}
}

echo "" > ${CONFFILE}

echo "[mysqldump]" >> ${CONFFILE}
echo "host=${HOST}" >> ${CONFFILE}
echo "user=${USER}" >> ${CONFFILE}
echo "password=${PASSWORD}" >> ${CONFFILE}
echo "[mysql]" >> ${CONFFILE}
echo "host=${HOST}" >> ${CONFFILE}
echo "user=root" >> ${CONFFILE}
echo "password=s3cur3s0c" >> ${CONFFILE}

if [ ! -d ${DBDIR} ]; then
    create
else
    backup
    alter
fi

exit $?

