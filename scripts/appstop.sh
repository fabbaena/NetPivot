#!/bin/bash

backup() {
    local CONFFILE=/home/ubuntu/.my.cnf
    local HOST=localhost
    local USER=demonio
    local PASSWORD=s3cur3s0c
    local DBNAME=NetPivot
    local DBDIR=/var/lib/mysql/$DBNAME
    local DBDUMP=/home/ubuntu/netpivot/netpivot-`date "+%F_%H-%M-%S_%Z"`.sql
    local BACKUP=/home/ubuntu/netpivot/netpivot-`date "+%F_%H-%M-%S_%Z"`.txz

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

    if [ ! -d `dirname ${DBDUMP}` ]; then
        mkdir -pv `dirname ${DBDUMP}`
    fi

    if [ -d ${DBDIR} ]; then
	mysqldump --defaults-file=${CONFFILE} --compact -c --delayed-insert -e -f -n -t -v --single-transaction --tz-utc --skip-quote-names ${DBNAME} > ${DBDUMP}
	bzip2 -zfv9 ${DBDUMP}
    fi

    tar -cvJf ${BACKUP} -C /var/www/html .
}

clean() {
    local WWWDATA=/var/www/html
    local BACKUP=/home/ubuntu/netpivot/netpivot-`date "+%F_%H-%M-%S_%Z"`.txz
    local FILELIST=( html php png css map woff2 eot svg ttf woff js )
    local DIRLIST=( `find ${WWWDATA} -type d` )

    tar -cvJf ${BACKUP} -C ${WWWDATA} .

    if [ ! -f ${WWWDATA}/.keep ]; then
	touch ${WWWDATA}/.keep
    fi

    for file in ${FILELIST[*]}; do
	find ${WWWDATA} -name "*.${file}" -type f -exec rm -vf {} \;
    done

    for dir in ${DIRLIST[*]}; do
	if [ ! -f $dir/.keep ]; then
	    rmdir -v $dir
	fi
    done
}

backup
clean

