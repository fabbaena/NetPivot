#!/bin/bash


DBPATH=/opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/scripts/
#DBPATH=/usr/src/netpivot/frontend/scripts
create() {
    su - postgres -c "psql -b -f ${DBPATH}/pgsql_create.sql"
}

alter() {
    psql -U demonio -b -f ${DBPATH}/pgsql_update.sql netpivot
    psql -U demonio -b -f ${DBPATH}/adc_hw.sql netpivot
    psql -U demonio -b -f ${DBPATH}/CRMTables.sql netpivot
    psql -U demonio -b -f ${DBPATH}/F5NSLink.sql netpivot
}

invoke-rc.d --quiet postgresql status
if [ $? -gt 0 ]; then
    invoke-rc.d --quiet postgresql start
fi

export PGPASSFILE=/home/ubuntu/.pgpass
echo "localhost:5432:netpivot:demonio:s3cur3s0c" > ${PGPASSFILE}
echo "localhost:5432:template1:demonio:s3cur3s0c" >> ${PGPASSFILE}
chmod 0600 ${PGPASSFILE}

psql -l -U demonio template1 | grep -q netpivot
if [ $? -ne 0 ]; then
    echo "Creating Database..."
    rm -f /var/www/html/index.html
    create
    echo "Altering Database..."
    alter
else
    echo "Altering Database..."
    alter
fi

