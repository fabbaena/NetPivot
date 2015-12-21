#!/bin/bash

app_start() {
    invoke-rc.d --quiet mysql status
    if [ $? -gt 0 ]; then
        invoke-rc.d --quiet mysql start
    fi

    invoke-rc.d --quiet apache2 status
    if [ $? -gt 0 ]; then
        invoke-rc.d --quiet apache2 start
    fi
}

app_start

exit $?

