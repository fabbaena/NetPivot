#!/bin/bash

if [ "$(id -u)" != "0" ]; then
    echo "This script must be run as 'sudo'" >&2
    exit 1
fi
rsync -r content/* /var/www/html/
chown -R www-data.www-data /var/www/html/*
