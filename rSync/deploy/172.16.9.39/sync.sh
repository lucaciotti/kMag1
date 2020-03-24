#!/bin/bash

rsync -avz -e ssh --delete --exclude-from 'exclude.txt' /c/PROJECTS/kMag2/ ced@172.16.9.39:/var/www/html/kMag2/ > ./log/backup$(date +%Y%m%d%H%M%S).log

#--log-file=/var/log/backup_log