#!/bin/bash

export PGPASSFILE=/home/ubuntu/.pgpass

export PGHOST=localhost
export PGUSER=demonio
export PGPASSWORD=s3cur3s0c
export PGDATABASE=netpivot

DBDIR=`psql -l | grep -q netpivot`

create() {
    local DBCREATE=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/pgsql_create.sql
    #local DBCREATE=/home/ubuntu/codedeploy/scripts/db_create.sql
    su - postgres -c "psql -b -f ${DBCREATE}"

#    if [ ! -d /opt/netpivot_kernel ]; then
#	mkdir -p -m 0755 /opt/netpivot_kernel
#	chown -R www-data.www-data /opt/netpivot_kernel
#    fi
}

alter() {
    local DBALTER=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/pgsql_update.sql
    #local DBALTER=/home/ubuntu/codedeploy/scripts/db_update.sql
    su - postgres -c "psql -b -f ${DBALTER}"
}

invoke-rc.d --quiet postgresql status
if [ $? -gt 0 ]; then
    invoke-rc.d --quiet postgresql start
fi

if [ ! -f ${PGPASSFILE} ]; then
    echo "localhost:5432:netpivot:demonio:s3cur3s0c" >> ${PGPASSFILE}
    chmod 0600 ${PGPASSFILE}
fi

if [ ! -d ${DBDIR} ]; then
    rm -f /var/www/html/index.html

    create
else
    alter
fi

