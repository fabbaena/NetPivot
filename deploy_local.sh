#!/bin/bash

if [ "$(id -u)" != "0" ]; then
    echo "This script must be run as 'sudo'" >&2
    exit 1
fi
rsync -r content/* /var/www/html/
chown -R www-data.www-data /var/www/html/*

install -o root -g root -m 0755 scripts/filecleanup.sh /usr/local/bin
install -o root -g root -m 0755 scripts/filedelete.php /usr/local/bin
install -o root -g root -m 0755 scripts/filelist.php /usr/local/bin
