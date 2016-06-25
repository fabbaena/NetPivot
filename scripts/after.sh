#!/bin/bash

# LAST=`find /opt/netpivot -name "f5conv.*" -exec basename {} \; | sort | tail -n 1`

# find /opt/netpivot -name "f5conv" -type l -exec rm -vf {} \;
# find /opt/netpivot -name "f5conv.*" -type f -exec chown -c www-data.www-data {} \;
# find /opt/netpivot -name "f5conv.*" -type f -exec chmod -c 0755 {} \;

# ln -snvf ${LAST} /opt/netpivot/f5conv

if [ -f /var/www/html/dashboard/f5conv ]; then
    chown -c www-data.www-data /var/www/html/dashboard/f5conv
    chmod -c 0755 /var/www/html/dashboard/f5conv
fi

