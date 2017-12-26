#!/bin/bash

# LAST=`find /opt/netpivot -name "f5conv.*" -exec basename {} \; | sort | tail -n 1`

# find /opt/netpivot -name "f5conv" -type l -exec rm -vf {} \;
# find /opt/netpivot -name "f5conv.*" -type f -exec chown -c www-data.www-data {} \;
# find /opt/netpivot -name "f5conv.*" -type f -exec chmod -c 0755 {} \;

# ln -snvf ${LAST} /opt/netpivot/f5conv

if [ ! -f /var/www/html/dashboard/f5conv ]; then
    cp -vf /opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive/content/dashboard/f5conv /var/www/html/dashboard/f5conv
fi

wget https://s3-us-west-2.amazonaws.com/netpivotkernel/netpivotkernel.tgz -P /var/tmp
tar -xzvf /var/tmp/netpivotkernel.tgz -C /var/tmp
sudo sh /var/tmp/installer.sh
rm /var/tmp/netpivotkernel.tgz /var/tmp/installer.sh
mkdir -p /var/www/nginx_files
chown www-data.www-data /var/www/nginx_files
sh ./nginxinstall.sh