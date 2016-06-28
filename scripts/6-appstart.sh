#!/bin/bash

invoke-rc.d --quiet postgresql status
if [ $? -gt 0 ]; then
    invoke-rc.d --quiet postgresql start
fi

invoke-rc.d --quiet apache2 restart

