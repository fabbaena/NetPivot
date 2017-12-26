#!/bin/bash
#
# This script installs manually nginx converter
#

sudo mkdir -p /opt/netpivot/usr/share/
sudo chown ubuntu.ubuntu /opt/netpivot/usr/share
cd /opt/netpivot/usr/share/
git clone git@gitlab.com:nemacrux/samana-prototype.git nginx
if [ -d "nginx" ]; then
    sudo apt-get install python3-pip
    cd nginx
    sudo pip3 install -r requirements.pip
    git clone -b python3-update https://github.com/netscalerconfig/netscaler
    git clone https://github.com/fatiherikli/nginxparser
    cd nginxparser
    sudo python3 setup.py install
else
    echo "Unable to download nginx"
fi
