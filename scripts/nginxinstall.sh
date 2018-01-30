#!/bin/bash
#
# This script installs manually nginx converter
#

sudo rmdir -p /opt/netpivot/usr/share/
sudo mkdir -p /opt/netpivot/usr/share/
sudo chown ubuntu.ubuntu /opt/netpivot/usr/share
cd /opt/netpivot/usr/share/
sudo apt-get install -y git
git clone git@github.com:netscalerconfig/nginxconverter.git nginx
if [ -d "nginx" ]; then
    sudo apt-get install -y python3-pip
    cd nginx
    sudo pip3 install -r requirements.pip
    git submodule init
    git submodule update
else
    echo "Unable to download nginx"
fi
