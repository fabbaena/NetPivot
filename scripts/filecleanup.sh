#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

usage() {
    echo "USAGE: $0 <date>" >&2
    echo "  This script will delete files from the system that are older than <date>." >&2
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

startdate=$1

uuids=$(php ${DIR}/filelist.php $startdate)
for f in $uuids; do
    set -e
    php ${DIR}/filedelete.php $f
    set +e
done