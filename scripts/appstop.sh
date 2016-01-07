#!/bin/bash

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

clean

