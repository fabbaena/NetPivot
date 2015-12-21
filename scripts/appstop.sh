#!/bin/bash

app_stop() {
    invoke-rc.d --quiet apache2 status
    if [ $? -eq 0 ]; then
        invoke-rc.d --quiet apache2 stop
    fi

    invoke-rc.d --quiet mysql status
    if [ $? -eq 0 ]; then
	invoke-rc.d --quiet mysql stop
    fi
}

clean() {
    local WWWDATA=/var/www/html
    local FILELIST=( php png css map woff2 eot svg ttf woff js )

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

app_stop
clean

exit $?

