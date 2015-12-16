#!/bin/bash

WWWDATA=/var/www/html
FILELIST=( php png css map woff2 eot svg ttf woff js )

if [ ! -f ${WWWDATA}/.keep ]; then
    touch ${WWWDATA}/.keep
fi

clean() {
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

exit $?

