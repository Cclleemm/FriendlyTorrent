#!/bin/bash

checkInstall()
{
	echo "------------ Check for $1 depedency ------------"
	
	PKG_OK=$(dpkg-query -W --showformat='${Status}\n' $1|grep "install ok installed")
	echo Checking for $1: $PKG_OK
	if [ "" == "$PKG_OK" ]; then
		echo "No $1. Setting up $1."
		if [ $1 == "sudo" ]; then
			apt-get --force-yes --yes install $1
		else
			sudo apt-get --force-yes --yes install $1
		fi
	fi
}

checkInstall sudo
checkInstall apache2
a2enmod rewrite
checkInstall php5
checkInstall mysql-server
checkInstall libapache2-mod-php5
checkInstall php5-curl
checkInstall php5-mysql
checkInstall memcached
checkInstall php5-memcache
checkInstall php5-memcached
checkInstall transmission
checkInstall transmission-daemon
checkInstall zip


echo "----------------------------------------------------"
echo "Welcome in the Friendly Torrent installation wizard"
echo "----------------------------------------------------"

#Ask for Website dir
echo -n "Application language (fr or en) [default : en] "
read language
if [[ $language == "" ]]
	then
		language=en
fi
echo -n "Website directory [default : /var/www/] "
read websiteFolder
if [[ $websiteFolder == "" ]]
	then
		websiteFolder=/var/www/ 
fi

cd src
cp -R ./ $websiteFolder 1> /dev/null
cd ..
echo "Files copied"

echo -n "Admin login : "
read login
echo -n "Admin password : "
read -s pass
echo ""

#Ask for download storage folder
echo -n "Downloads storage folder [default : /var/downloads/] "
read dossierBoxes
if [[ $dossierBoxes == "" ]]
	then
		dossierBoxes=/var/downloads/
fi
if mkdir $dossierBoxes
	then 
		echo "Folder created with success "
	else 
		echo "Can't create this folder"
fi

mkdir "$dossierBoxes/$login/"
mkdir "$dossierBoxes/.transferts/"
sudo chown www-data $dossierBoxes -R

echo "------------ PHP/Apache2 Configuration ------------ "
echo 'extension=memcache.so' >> /etc/php5/apache2/php.ini
echo "PHP Memcache extension enabled"
echo '*/1 * * * *     www-data wget "http://localhost/action/refreshTorrent/" -O /dev/null' >> /etc/cron.d/php5

if [ -f "$websiteFolder/index.html" ]; then
   sudo mv "$websiteFolder/index.html" "$websiteFolder/index-old.html"

fi

echo "------------ MySQL configuration ------------ "

while true; do
	echo -n "MySQL user : "
        read userBDD
	echo -n "MySQL password : "
        read -s passBDD
	echo ""
	echo -n "MySQL database: "
	read bdd
	mysql -u $userBDD -p$passBDD -e "CREATE DATABASE IF NOT EXISTS $bdd;"
	if mysql -u $userBDD -p$passBDD $bdd < board.sql
                then
                        echo "Database importation succeed ! "
                        break
                else
                        echo "Can't import the database !"
        fi
	
done

md5=`echo -n $pass|md5sum|awk '{ print $1 }'`
mysql -u $userBDD -p$passBDD $bdd << EOF
insert into users(login,mail,password,boxe,couleur,lastScan,rss,admin,port) values('$login','-','$md5','$dossierBoxes/$login', '78ba00', 0, '', 1, 9091);
EOF

mkdir "$websiteFolder""core/config/"

echo "<?php

//CONFIG MYSQL

\$BDD_MYSQL_SERVER = 'localhost';
\$BDD_MYSQL_LOGIN = '$userBDD';
\$BDD_MYSQL_PASS = '$passBDD';
\$BDD_MYSQL_BDD = '$bdd';

?>" > "$websiteFolder""core/config/bdd.php"

echo "<?php
define('ROOT_DOWNLOADS','$dossierBoxes');
define('TRANSMISSION','/usr/bin/transmission-daemon');
define('LANGUAGE','$language');
?>" > "$websiteFolder""core/config/global.php"

sudo service apache2 reload
sudo service transmission-daemon stop
sudo /etc/init.d/cron restart

echo "----------------------------------------------------"
echo "----- Thank you for installing Friendly Torrent ----"
echo "---------- Don't forget to enable HTACCESS ---------"
echo "----------- And go to : http://localhost -----------"
echo "----------------------------------------------------"
