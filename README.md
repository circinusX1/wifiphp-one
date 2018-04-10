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
    - sudo visudo
    
```javascript



```
        
        


