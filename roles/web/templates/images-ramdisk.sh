#!/bin/bash

ISMOUNT=`grep "images-tmp" /etc/fstab`

if [ "$ISMOUNT" == "" ]; then
    echo "none  /var/www/html/images-tmp  tmpfs defaults,noatime,nosuid,nodev,noexec,size=5M,mode=1777	  0 0" >> /etc/fstab
fi

mount -a
