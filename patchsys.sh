#!/bin/bash
 apt-get update
 apt-get install wireless-tools
 apt-get install libssh2-php
 spt-get install php-ssh2
 service apache2 restart
 service lighttpd restart
 chown www-data:$USER /etc/wpa_supplicant/wpa_supplicant.conf
 touch /etc/wpa_supplicant/env
 chown www-data:$USER /etc/wpa_supplicant/env
 chomod 775 /etc/wpa_supplicant/wpa_supplicant.conf
 touch /etc/wpa_supplicant/env
ifconfig -a
echo "put your wlan interface as WLAN=wlan0 in next  file and save Ctrl+O then Ctrl X. y to cntinue..."
read y
 nano /etc/wpa_supplicant/env
# put WLAN=<your interface name> (once) n /etc/wpa_supplicant/env
 chown $USWER:$USER /etc/wpa_supplicant/env
 chmod 755 /etc/wpa_supplicant/env
echo "put your visudo as in https://github.com/comarius/wifiphp-one doc. y to cntinue..."
read y
visudo
echo "Make changes to wpa service as https://github.com/comarius/wifiphp-one. y to cntinue..."
read y
nano /lib/systemd/system/wpa_supplicant.service
systemctl restart wpa_supplicant.service

