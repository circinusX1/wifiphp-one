# wifiphp-config
One php page to configure wifi client

## prerequisites
### applications
    - apache with php enabled
    - or lighttpd with php cgi enabled
    - wpa-supplicant https://blog.nelhage.com/2008/08/using-wpa_supplicant-on-debianubuntu/
    - php with https://serverpilot.io/community/articles/how-to-install-the-php-ssh2-extension.html
    
### system

```javascript
  sudo chown www-data:$USER /etc/wpa_supplicant/wpa_supplicant.conf
  ls -l  /etc/wpa_supplicant/wpa_supplicant.conf
  -rw-r--r-- 1 www-data marius 116 Apr 10 20:01 /etc/wpa_supplicant/wpa_supplicant.conf
```
    - kill and disable any othe rnetwork managers.
        - like: connman and friends
    - visudo
    
```javascript

sudo visudo

s2w ALL=(ALL) NOPASSWD:/sbin/ifconfig
s2w ALL=(ALL) NOPASSWD:/usr/sbin/service
s2w ALL=(ALL) NOPASSWD:/usr/bin/wpa_passphrase
s2w ALL=(ALL) NOPASSWD:/sbin/ifdown
s2w ALL=(ALL) NOPASSWD:/sbin/ifup
s2w ALL=(ALL) NOPASSWD:/sbin/dhclient
s2w ALL=(ALL) NOPASSWD:/sbin/wpa_cli
s2w ALL=(ALL) NOPASSWD:/sbin/ip
s2w ALL=(ALL) NOPASSWD:/sbin/iwlist
s2w ALL=(ALL) NOPASSWD:/bin/systemctl
s2w ALL=(ALL) NOPASSWD:/sbin/iw


```
    - save
    
        


