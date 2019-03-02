# CNTP - Custom New Tab Page

About
---
New standalone version of the Chrome Extension [Chrome-CNTP](https://github.com/sen87/Chrome-CNTP).

Features
---
- **Feed Reader** :: supports Atom 1.0, RSS 1.0, RSS 2.0 and Media RSS
- **Weather Forecast** :: [provided by yr.no](https://hjelp.yr.no/hc/en-us/articles/360009342833-XML-weather-forecasts)
- **Bookmarks** ::  add URLs for quick access
- **Notes** :: quickly write down notes (text URLs will be linkified)
- **Page Layout Generator** :: create your own custom grid-based layout
- **CSS Themes** :: choose one of the packaged themes or create your own custom look with pure CSS
- **Cross-Platform** :: desktop and mobile layout
- **User Management** :: comes with a complete login system
- **Database** :: uses a mysql database for caching and the configuration


Live
---
https://cntp.nya.pub

Screenshots
---
<img height="160" src="https://user-images.githubusercontent.com/16217416/53687939-c8a2fb80-3d3b-11e9-9db0-c83adb71350b.png"/> <img height="160" src="https://user-images.githubusercontent.com/16217416/53687940-c8a2fb80-3d3b-11e9-991a-9dbb9a0e3196.png"/> <img height="160" src="https://user-images.githubusercontent.com/16217416/53687941-c8a2fb80-3d3b-11e9-9e2a-956462851f8f.png"/>

Installation
---
Requirements:
- Web Server (Apache)
- MySQL DB (MariaDB)
- PHP

Web Server:<br>
Follow a [guide](https://wiki.archlinux.org/index.php/Apache_HTTP_Server) and check out other [helpful resources](https://github.com/h5bp/html5-boilerplate/blob/master/dist/.htaccess).<br>
If you just want to run the page locally, configuring `Listen 127.0.0.1:80` and pointing the `DocumentRoot` to the `CNTP/webroot` folder should be sufficient.<br>
Make sure that the web server can access the files. Example for user/group http:

    chgrp -R http CNTP/
    chmod -R 750 CNTP/

PHP:<br>
Follow a [guide](https://wiki.archlinux.org/index.php/PHP).
The login system requires a sendmail client (I use [msmtp](https://wiki.archlinux.org/index.php/Msmtp)).<br>
Here are some required (or recommended) settings in the php.ini:

    extension=curl
    extension=mysqli
    sendmail_path = "/usr/bin/msmtp -t"
    session.use_cookies = 1
    session.cookie_lifetime = 2592000
    
Database:
- create a user and a new database ([help](https://wiki.archlinux.org/index.php/MariaDB))
- initialize the db with the provided sql dump --> `cntp_db_dump.sql`
- fill out `db_credentials.php`
<img height="160" src="https://user-images.githubusercontent.com/16217416/53687942-c93b9200-3d3b-11e9-9e0a-dc78aa0934ee.png" />

General Page Setting:<br>
See `config.php`.
In the `precache` folder is an optional systemd service+timer that automatically precaches feeds for active users every 15 minutes.

Disclaimer
---
This is just a hobby project. If you find bugs or have suggestions please let me know! :)
<br><br>
<p align="center">
  <img src="https://cloud.githubusercontent.com/assets/16217416/11696937/4d0c1ec8-9eb7-11e5-9b3a-7367182466dc.png"/>
</p>
