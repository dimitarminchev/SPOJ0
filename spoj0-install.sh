#!/bin/bash

# COLORS
nocol="\e[0m"
green="\e[1;32m"
yellow="\e[1;33m"
red="\e[1;31m"

# TERMINAL BREAK (CTRL+C)
trap 'printf "\n${red}INSTALLATION TERMINATED!${nocol}\n"; exit 1' 2

# QUESTION
printf "${yellow}DO YOU WANT TO START? (yes/no) ${nocol}"
read answer
if [ $answer = "no" ]; then
   printf "${red}INSTALLATION TERMINATED!${nocol}\n"
   exit 1 # EXIT
fi

# TIMEZONE
timedatectl set-timezone Europe/Sofia

# INSTALL PACKAGES
printf "${green}INSTALL PACKAGES ...\n${nocol}"
apt-get --assume-yes update
apt-get --assume-yes install apache2
apt-get --assume-yes install php
apt-get --assume-yes install php-dev
apt-get --assume-yes install php-pear
apt-get --assume-yes install php-mysql
apt-get --assume-yes install libmcrypt-dev
apt-get --assume-yes install libapache2-mod-php
apt-get --assume-yes install libapache2-mod-perl2
apt-get --assume-yes install mysql-server
apt-get --assume-yes install mysql-client
apt-get --assume-yes install libdbi-perl
apt-get --assume-yes install libdbd-mysql-perl
apt-get --assume-yes install default-jdk
apt-get --assume-yes install g++
apt-get --assume-yes install mono-mcs

# launchtool 0.8.2
# http://archive.ubuntu.com/ubuntu/ubuntu/pool/universe/l/launchtool/
wget http://archive.ubuntu.com/ubuntu/ubuntu/pool/universe/l/launchtool/launchtool_0.8-2build1_amd64.deb
dpkg -i launchtool_0.8-2build1_amd64.deb
rm launchtool_0.8-2build1_amd64.deb

# mcrypt 1.0.6
# https://pecl.php.net/package/mcrypt
pecl channel-update pecl.php.net
printf "\n" | pecl install mcrypt-1.0.6

# CREATE USERS
printf "${green}CREATE USERS ...\n${nocol}"
useradd --create-home --password spoj0 spoj0
useradd --create-home --password spoj0run spoj0run
printf "DONE.\n"

# GET FROM REPO
printf "${green}GET FROM REPO ...\n${nocol}"
SPOJ_GIT_REPO=https://github.com/dimitarminchev/spoj0.git
git clone $SPOJ_GIT_REPO
cp -r spoj0/ /home/
rm -r spoj0/

# HOME
chmod 755 /home/spoj0
chmod 755 /home/spoj0/*.pl
chmod 755 /home/spoj0/*.sh
chown -R spoj0:spoj0 /home/spoj0

# MYSQL
printf "${green}MYSQL ...\n${nocol}"
mysql -u root < spoj0.sql
printf "OK\n"

# APACHE
printf "${green}APACHE ...\n${nocol}"
cat <<EOT > /etc/apache2/sites-available/spoj0.conf
Alias /spoj /home/spoj0/web
<Directory /home/spoj0/web>
Options MultiViews Indexes Includes FollowSymLinks ExecCGI
AllowOverride All
Require all granted
allow from all
</Directory>
EOT
a2ensite spoj0.conf
service apache2 reload

# SPOJ
printf "${green}SPOJ ...\n${nocol}"
now=$(date +"%Y-%m-%d %H:00:00")
cat << EOT  > /home/spoj0/sets/test/set-info.conf
name=TEST
start_time=$now
duration=60
show_sources=1
about=SELF TEST
EOT

# TEST IMPORT SET
./spoj0-control.pl import-set test

# TEST SOME SUBMITS
# milo
./spoj0-control.pl submit 1 3 sets/test.samples/hello_pe.java java hello_pe.java
./spoj0-control.pl submit 1 3 sets/test.samples/hello_re.java java hello_re.java
./spoj0-control.pl submit 1 3 sets/test.samples/hello_tl.java java hello_tl.java
./spoj0-control.pl submit 1 3 sets/test.samples/hello_wa.java java hello_wa.java
./spoj0-control.pl submit 1 3 sets/test.samples/hello_ok.java java hello_ok.java
./spoj0-control.pl submit 1 3 sets/test.samples/hello_pe.cpp cpp hello_pe.cpp
./spoj0-control.pl submit 1 3 sets/test.samples/hello_ok.cpp cpp hello_ok.cpp
./spoj0-control.pl submit 2 3 sets/test.samples/ab_ok.java java ab_ok.java
./spoj0-control.pl submit 2 3 sets/test.samples/ab_pe.cpp cpp ab_pe.cpp
./spoj0-control.pl submit 2 3 sets/test.samples/ab_wa.cpp cpp ab_wa.cpp
./spoj0-control.pl submit 2 3 sets/test.samples/ab_ok.cpp cpp ab_ok.cpp
# mitko
./spoj0-control.pl submit 1 4 sets/test.samples/ab_ok.cs cs ab_ok.cs
./spoj0-control.pl submit 1 4 sets/test.samples/hello_ok.cs cs hello_ok.cs
./spoj0-control.pl submit 2 4 sets/test.samples/hello_ok.cs cs hello_ok.cs
./spoj0-control.pl submit 2 4 sets/test.samples/ab_ok.cs cs ab_ok.cs

# RUN SPOJ0
./spoj0-control.pl start

# DONE
printf "${green}ENJOY SPOJ!${nocol}\n"
