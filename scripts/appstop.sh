#!/bin/bash

clean() {
    local WWWDATA=/var/www/html
    local BACKUP=/home/ubuntu/netpivot/netpivot-${DEPLOYMENT_ID}.txz
    local FILELIST=( html php png css map woff2 eot svg ttf woff js f5conv* )

    rm -f ${BACKUP}
    tar -cJf ${BACKUP} -C ${WWWDATA} .

    if [ ! -f ${WWWDATA}/.keep ]; then
	touch ${WWWDATA}/.keep
    fi

    for file in ${FILELIST[*]}; do
	find ${WWWDATA} -name "*.${file}" -exec rm -f {} \;
    done

    DIRLIST=( `find ${WWWDATA} -type d` )
    for dir in ${DIRLIST[*]}; do
	if [ ! -f $dir/.keep ]; then
	    rmdir $dir
	fi
    done
}

clean

