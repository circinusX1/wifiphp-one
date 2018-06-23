# wifiphp-config
One php page to configure wifi client

## prerequisites
### applications
    - apache with php enabled
    - or lighttpd with php cgi enabled
    - wpa-supplicant https://blog.nelhage.com/2008/08/using-wpa_supplicant-on-debianubuntu/
    - php with https://serverpilot.io/community/articles/how-to-install-the-php-ssh2-extension.html
        -sudo apt-get install libssh2-php 
    - clean slate
        - sudo apt-get install git lphp5-gd php-cgi php-gd php5-gd php-cgi php-gd
            - sudo lighttpd-enable-mod fastcgi 
            - sudo lighttpd-enable-mod fastcgi-php
            - sudo  /etc/init.d/lighttpd force-reloighttpd  
         
#### system

```javascript
  sudo apt-get install wireless-tools
  sudo chown www-data:$USER /etc/wpa_supplicant/wpa_supplicant.conf
  sudo touch /etc/wpa_supplicant/env
  sudo chown www-data:$USER /etc/wpa_supplicant/env
  sudo chomod 775 /etc/wpa_supplicant/wpa_supplicant.conf
  sudo touch /etc/wpa_supplicant/env
  # put WLAN=<your interface name> (once) n /etc/wpa_supplicant/env
  sudo chown $USWER:$USER /etc/wpa_supplicant/env
  sudo chmod 755 /etc/wpa_supplicant/env
  
  
  ls -l  /etc/wpa_supplicant/wpa_supplicant.conf
  -rw-rw-r-- 1 www-data marius 116 Apr 10 20:01 /etc/wpa_supplicant/wpa_supplicant.conf
  -rw-rw-r-- 1 www-data marius 116 Apr 10 20:01 /etc/wpa_supplicant/env
```
    - kill and disable any other network managers / service.
        - like: 'connman' and friends...
    - add to sudoers following programs.
    

#### system

```javascript
you ALL=(ALL) NOPASSWD:/sbin/ifconfig
you ALL=(ALL) NOPASSWD:/usr/sbin/service
you ALL=(ALL) NOPASSWD:/usr/bin/wpa_passphrase
you ALL=(ALL) NOPASSWD:/sbin/ifdown
you ALL=(ALL) NOPASSWD:/sbin/ifup
you ALL=(ALL) NOPASSWD:/sbin/dhclient
you ALL=(ALL) NOPASSWD:/sbin/wpa_cli
you ALL=(ALL) NOPASSWD:/sbin/ip
you ALL=(ALL) NOPASSWD:/sbin/iwlist
you ALL=(ALL) NOPASSWD:/bin/systemctl
you ALL=(ALL) NOPASSWD:/sbin/iw
```

#### wpa_supplicabt /lib/systemd/system/wpa_supplicant.service  changes. Add Environment Line and -i${WLAN}

edit the pgpg and change password and username in ssh2 connect


````javascript
[Service]
Type=dbus
EnvironmentFile=/etc/wpa_supplicant/env
BusName=fi.epitest.hostap.WPASupplicant
ExecStart=/sbin/wpa_supplicant -u -s -O -i${WLAN} /run/wpa_supplicant -c/etc/wpa_supplicant/wpa_supplicant.conf

````



    - save
    - copy the php on your web server root 
    - change new Ssh("localhost","user","user");  with new Ssh("localhost","your user name","your password");
    - access the page.
    
        
        
        
        
![alt text](https://raw.githubusercontent.com/comarius/wifiphp-config/master/wifiphpone.png "wifipho-config")



If you contribute please 'KISS'.

    - tested om:
        - BBB
        - C. H. I. P.
        - Orange Pi Wifi
        - Nano pi Wifi
        - Nano pi neo with wifi dongle
        



