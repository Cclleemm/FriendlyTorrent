![PreviewImage](http://cclleemm.github.io/FriendlyTorrent/github-page/img/logo-mini.png)  FriendlyTorrent
=======

FriendlyTorrent is a web PHP script to download torrents with a beautiful an intuitive web responsive interface.
Moreover it allow you to explore and share download files "in the cloud" with your friends.
This software is based on [Transmission](http://www.transmissionbt.com) torrent software.

This is a BETA version, maybe you can help us ? :) [TODO List](https://github.com/Cclleemm/FriendlyTorrent/wiki/TODO)

![PreviewImage](http://cclleemm.github.io/FriendlyTorrent/github-page/img/home.png) 

### What's included

Within the download you'll find the following directories and files. You'll see something like this:

```
bootstrap/
├── board.sql
├── install.sh
└── src/
    ├── bootstrap/
    ├── controllers/
    ├── core/
    ├── models/
    ├── theme/
    ├── tmp/
    ├── tornado/
    ├── uploadify/
    ├── views/
    ├── .htaccess
    ├── favicon.ico
    └── index.php
```


## Automatic installation (Ubuntu, Debian ...)
* [Download the latest release](https://github.com/Cclleemm/FriendlyTorrent/archive/master.zip).
* run the install wizard with this following commands

``` 
sudo chmod +x ./install.sh
sudo ./install.sh 
``` 
* Enable HTACESS for the web folder (vhost file).

Solution to enable HTACCESS but it's not secure :

```
sudo nano /etc/apache2/apache2.conf
```
Change Directory directive
```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```

To

```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>
```

Go to : http://localhost/

## Manual installation

### Dependencies
You must have installed this before :
* Apache2
* php5
* mysql-server
* libapache2-mod-php5
* php5-curl
* php5-mysql
* memcached
* php5-memcache
* php5-memcached
* transmission
* transmission-daemon
*  zip

### Files Import
Put all files of the `src/` folder in your web folder (for example in /var/www/).

Create the download folder where all your downloads will be stored (ex : /var/downloads/)

Create `.transfert` folder (ex : /var/downloads/.transferts/)

Create user folder  (ex : /var/downloads/user-name/)

Give folders rights with `sudo chown www-data /var/downloads/ -R`

Cofigure the `src/core/config/global.php` file with yours settings :
```
<?php
define('ROOT_DOWNLOADS','/var/downloads/');
define('TRANSMISSION','/usr/bin/transmission-daemon');
?>
```

### MySQL Import
Import the MySQL database `board.sql`

Configure the `src/core/config/bdd.php` file with yours settings :
```
<?php
//CONFIG MYSQL
$BDD_MYSQL_SERVER = 'localhost';
$BDD_MYSQL_LOGIN = 'root';
$BDD_MYSQL_PASS = 'password';
$BDD_MYSQL_BDD = 'FriendlyTorrentDB';
?>
```

Add user in the `users` table with MD5 encryption for the password :
```
insert into users(login,mail,password,boxe,couleur,lastScan,rss,admin,port) values('USERNAME','-','MD5_PASSWORD','/YOUR_DOWNLOADS_FLODER/USERNAME', '78ba00', 0, '', 1, 9091);
```

### Apache Configuration

Enable memcache in your `php.ini` file (/etc/php5/apache2/php.ini) adding line `extension=memcache.so`

Enable HTACESS for the web folder.

Solution to enable HTACCESS but it's not secure :

```
sudo nano /etc/apache2/apache2.conf
```
Change Directory directive
```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```

To

```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>
```



Add thoose lines in the cron table `/etc/cron.d/php5`
```
*/1 * * * *     www-data wget "http://localhost/action/refreshTorrent/" -O /dev/null' >> /etc/cron.d/php5
```


### Initialization
Restart cron `sudo /etc/init.d/cron restart`

Restart Apache `sudo service apache2 reload`

Stop transmission `sudo service transmission stop`

Go to : http://localhost/

***

Read the [Official Website](http://cclleemm.github.io/FriendlyTorrent/) for more information.

***

## Community

Keep track of development and community news.

* Follow [@FriendlyTorrent on Twitter](http://twitter.com/friendlytorrent).
* Read and subscribe to [The Official Website](http://friendlytorrent.com).



