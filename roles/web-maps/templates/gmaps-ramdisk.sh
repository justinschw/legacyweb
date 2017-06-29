#!/bin/bash

mount -t ramfs -o size=5m ramfs /var/www/html/gmaps

ISMOUNT=`grep "gmaps" /etc/fstab`

if [ "$ISMOUNT" == "" ]; then
    echo "ramfs /var/www/html/gmaps nodev,nosuid,noexec,nodiratime,size=5M 0 0" >> /etc/fstab
fi
