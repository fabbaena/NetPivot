#!/bin/bash

LAST=`find /opt/netpivot -name "f5conv.*" -exec basename {} \; | sort | tail -n 1`

find /opt/netpivot -name "f5conv" -type l -exec rm -vf {} \;
find /opt/netpivot -name "f5conv.*" -type f -exec chown -c www-data.www-data {} \;
find /opt/netpivot -name "f5conv.*" -type f -exec chmod -c 0755 {} \;

ln -snvf ${LAST} /opt/netpivot/f5conv

