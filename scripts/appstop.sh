#!/bin/bash

clean() {
    local WWWDATA=/var/www/html
    if [ -n "$DEPLOYMENT_ID" ]; then
	local BACKUP=/home/ubuntu/netpivot/netpivot-${DEPLOYMENT_ID}.txz
    else
	local BACKUP=/home/ubuntu/netpivot/netpivot-BACKUP.txz
    fi
    local FILELIST=( html php png css map woff2 eot svg ttf woff js )
    local DIRLIST=( `find ${WWWDATA} -type d` )

    rm -vf ${BACKUP}
    tar -cvJf ${BACKUP} -C ${WWWDATA} .

    if [ ! -f ${WWWDATA}/.keep ]; then
	touch ${WWWDATA}/.keep
    fi

    for file in ${FILELIST[*]}; do
	find ${WWWDATA} -name "*.${file}" -exec rm -vf {} \;
    done

    for dir in ${DIRLIST[*]}; do
	if [ ! -f $dir/.keep ]; then
	    rmdir -v $dir
	fi
    done
}

clean

