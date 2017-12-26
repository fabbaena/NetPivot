#!/bin/bash
#
# This script installs manually nginx converter
#

sudo mkdir -p /opt/netpivot/usr/share/
sudo chown ubuntu.ubuntu /opt/netpivot/usr/share
cd /opt/netpivot/usr/share/
git clone git@gitlab.com:nemacrux/samana-prototype.git nginx
if [ -d "nginx" ]; then
    cd nginx
    git clone https://github.com/fatiherikli/nginxparser
    sudo python3 nginxparser/setup.py install
    sudo apt-get install python3-pip
    sudo pip3 install -r requirements.pip
    git clone -b python3-update https://github.com/netscalerconfig/netscaler
else
    echo "Unable to download nginx"
fi
