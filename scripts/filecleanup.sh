#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

usage() {
    echo "USAGE: $0 <number of days>" >&2
    echo "  This script will delete files from the system that are older than <number of days>." >&2
    exit 1
}

if [ $(id -u) != "0" ]; then
    echo "This script must by run as SUDO." >&2
    usage
fi

if [ -z "$1" ]; then
    echo "Please provide a date." >&2
    usage
fi

which php > /dev/null
if [ "$?" != "0" ]; then
    echo "PHP is needed for this script to run properly." >&2
    usage
fi

days=$1
startdate=$(date --date="$(date) -${days}days" +%F)

uuids=$(php ${DIR}/filelist.php $startdate)
for f in $uuids; do
    set -e
    php ${DIR}/filedelete.php $f
    set +e
done