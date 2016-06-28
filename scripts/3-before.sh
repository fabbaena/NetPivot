#!/bin/bash


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

    psql -U demonio-b -f ${DBALTER} netpivot
}

invoke-rc.d --quiet postgresql status
if [ $? -gt 0 ]; then
    invoke-rc.d --quiet postgresql start
fi

export PGPASSFILE=/home/ubuntu/.pgpass
echo "localhost:5432:netpivot:demonio:s3cur3s0c" > ${PGPASSFILE}
chmod 0600 ${PGPASSFILE}

psql -l -U demonio netpivot | grep -q netpivot
if [ $? -ne 0 ]; then
    echo "Creating Database..."
    rm -f /var/www/html/index.html
    create
else
    echo "Altering Database..."
    alter
fi

