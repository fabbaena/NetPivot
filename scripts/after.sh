#!/bin/bash

LAST=`find /var/www/html -name "f5conv.*" -exec basename {} \; | sort | tail -n 1`

find /var/www/html -name "f5conv" -exec rm -f {} \;
find /var/www/html -name "f5conv.*" -type f -exec chmod 0755 {} \;

ln -snf ${LAST} /var/www/html/dashboard/f5conv

