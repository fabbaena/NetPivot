#!/bin/bash

LAST=`find /opt/netpivot -name "f5conv.*" -exec basename {} \; | sort | tail -n 1`

find /opt/netpivot -name "f5conv" -type l -exec rm -f {} \;
find /opt/netpivot -name "f5conv.*" -type f -exec chown www-data.www-data {} \;
find /opt/netpivot -name "f5conv.*" -type f -exec chmod 0755 {} \;

ln -snf ${LAST} /opt/netpivot/f5conv

