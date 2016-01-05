#!/bin/bash

find /var/www/html -name "*f5conv*" -type f -exec chmod 0755 {} \;

