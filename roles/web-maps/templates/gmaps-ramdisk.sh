#!/bin/bash

ISMOUNT=`grep "gmaps" /etc/fstab`

if [ "$ISMOUNT" == "" ]; then
    echo "none  /var/www/html/gmaps  tmpfs defaults,noatime,nosuid,nodev,noexec,size=5M,mode=1777	  0 0" >> /etc/fstab
fi

mount -a
