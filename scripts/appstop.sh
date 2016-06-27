#!/bin/bash

WWWDATA=/var/www

backup() {
    export PGPASSFILE=/home/ubuntu/.pgpass
    export PGHOST=localhost
    export PGUSER=demonio
    export PGPASSWORD=s3cur3s0c
    export PGDATABASE=netpivot
    local DBDIR=`psql -l | grep -q netpivot`
    local DBDUMP=/home/ubuntu/netpivot/netpivot-`date "+%F_%H-%M-%S_%Z"`.sql.xz
    local BACKUP=/home/ubuntu/netpivot/netpivot-`date "+%F_%H-%M-%S_%Z"`.tbz2

    invoke-rc.d --quiet postgresql status
    if [ $? -gt 0 ]; then
	invoke-rc.d --quiet postgresql start
    fi

    if [ ! -f ${PGPASSFILE} ]; then
	echo "localhost:5432:netpivot:demonio:s3cur3s0c" >> ${PGPASSFILE}
	chmod 0600 ${PGPASSFILE}
    fi

    if [ ! -d `dirname ${DBDUMP}` ]; then
        mkdir -pv `dirname ${DBDUMP}`
    fi

    psql -l | grep -q netpivot
    if [ $? -eq 0 ]; then
	pg_dump ${PGDATABASE} | xz -z9q > ${DBDUMP}
    fi

    tar -cvjf ${BACKUP} -C ${WWWDATA} .
}

clean() {
    local FILELIST=( html css map php eot svg ttf woff woff2 png js f5conv )
    local DIRLIST=( `find ${WWWDATA} -type d` )

    if [ ! -f ${WWWDATA}/files/.keep ]; then
	touch ${WWWDATA}/files/.keep
    fi

    for file in ${FILELIST[*]}; do
	find ${WWWDATA} -name "*.${file}" -type f -exec rm -vf {} \;
    done
    rm -vf /home/ubuntu/user_clean.sh

    for dir in ${DIRLIST[*]}; do
	if [ ! -f $dir/.keep ]; then
	    rmdir -v $dir
	fi
    done
}

backup
clean

