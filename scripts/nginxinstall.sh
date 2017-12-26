#!/bin/bash
#
# This script installs manually nginx converter
#

mkdir -p /opt/netpivot/usr/share/
cd /opt/netpivot/usr/share/
git clone gitlab:nemacrux/samana-prototype.git nginx
cd nginx
git clone https://github.com/fatiherikli/nginxparser
python3 nginxparser/setup.py install
apt-get install python3-pip
pip3 install -r requirements.pip
git clone -b python3-update https://github.com/netscalerconfig/netscaler
