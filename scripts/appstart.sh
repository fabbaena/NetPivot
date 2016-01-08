#!/bin/bash

invoke-rc.d --quiet mysql status
if [ $? -gt 0 ]; then
    invoke-rc.d --quiet mysql start
fi

invoke-rc.d --quiet apache2 restart

