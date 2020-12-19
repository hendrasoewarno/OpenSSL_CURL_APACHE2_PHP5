# OpenSSL_CURL_APACHE2_PHP5
Proses Upgrade Package OpenSSL, Curl, Apache2 dan PHP5 pada Server Ubuntu 9.04

## INSTALASI OPENSSL, CURL, APACHE2, PHP5
--------------------------------------

lsb_release -a

updatedb
untuk mengupdate database locate

file yang dibutuhkan:
1. apache.sh
2. config.nice

** UPDATE SOURCES.LIST untuk memungkinkan instalasi pada Ubuntu versi lama
https://askubuntu.com/questions/91815/how-to-install-software-or-upgrade-from-an-old-unsupported-release

gedit /etc/apt/sources.list
** ganti semua us-archieve.ubuntu.com menjadi old-releases.ubuntu.com

** pada ubuntu untuk mendapatkan package depedencies 
sudo apt-get build-dep apache2
sudo apt-get build-dep php5

https://stackoverflow.com/questions/9520957/get-current-php-install-settings

** untuk openssl
apt update
apt install build-essential checkinstall zlib1g-dev -y


** untuk curl
apt-get update
apt-get install -y libssl-dev autoconf libtool make


** untuk apache2
apt-get install libapr1 libapr1-dev libaprutil1-dev apache2-threaded-dev

** untuk php5
apt-get install \
    libxml2-dev \
    libcurl4-openssl-dev \
    libjpeg62-dev \
    libpng12-dev \
    libxpm-dev \
    libmysqlclient15-dev \
    libicu-dev \
    libfreetype6-dev \
    libldap2-dev \
    libxslt-dev \
    libssl-dev \
    libldb-dev \
    libbz2-dev \
    libt1-dev \
    libgmp3-dev \
    libmcrypt-dev \
    libsasl2-dev \
    freetds-dev \
    libpspell-dev \
    librecode-dev \
    libsnmp-dev \
    libtidy-dev

INSTALASI OPENSSL
-----------------
openssl version

kalau saya coba pada ubuntu 9.04, versi 1.0.1o masih bisa dikompilasi

https://www.howtoforge.com/tutorial/how-to-install-openssl-from-source-on-linux/

cd /usr/local/src/
wget https://www.openssl.org/source/openssl-1.0.1o.tar.gz
tar -xf openssl-1.0.1o.tar.gz
cd openssl-1.0.1o

./config --prefix=/usr/local/ssl --openssldir=/usr/local/ssl shared zlib

kalau ubuntu original
#./config --prefix=/usr --openssldir=/usr/lib/ssl shared zlib

** Untuk ubuntu diupayakan sama dengan ubuntu original

** Akan menginstalasi open ssl pada /usr/local/ssl dan library pada /usr/local/ssl
make
make test
make install

-------------------------------------ini dibutuhkan kalau beda dengan /usr/lib/ssl
** konfigurasi shared library
cd /etc/ld.so.conf.d/
sudo pico openssl-1.0.1o.conf
	dan ketikan /usr/local/ssl/lib
	
sudo ldconfig -v
	pastikan library telah berhasil diload
	
** backup file openssl yang lama

	mv /usr/bin/c_rehash /usr/bin/c_rehash.old
	mv /usr/bin/openssl /usr/bin/openssl.old
	
** tambahkan path ke openssl baru /usr/local/ssl/bin 
	sudo pico /etc/environment
	PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/usr/local/ssl/bin"

** reload environment 
	source /etc/environment
	echo $PATH
-------------------------------------ini dibutuhkan kalau beda dengan /usr/lib/ssl

which openssl
openssl version -a


INSTALASI CURL
--------------

kalau saya coba pada ubuntu 9.04, versi 7.49.1 masih bisa dikompilasi

https://blog.usejournal.com/how-to-manually-update-curl-on-ubuntu-server-899476062ad6

https://curl.se/download/curl-7.49.1.tar.gz

curl -V
which curl

openssl version -d
** catat lokasi OPENSSLDIR

./configure -disable-shared -with-ssl=/usr/local/ssl

Ingat folder harus disesuaikan dengan lokasi [OPENSSLDIR]

make

**backup curl yang lama
	mv /usr/bin/curl /usr/bin/curl.bak
	
make install

cp /usr/local/bin/curl /usr/bin/curl

pastikan ketika:

curl -V
adalah menggunakan openssl 1.0.1o


INSTALASI APACHE2
-----------------

** pastikan backup konfigurasi APACHE2 saat ini
sudo cp -r /etc/apache2 ~/apache2_conf_back

sudo su
cd /usr/local/src
curl https://archive.apache.org/dist/httpd/httpd-2.2.31.tar.gz --output httpd-2.2.31.tar.gz
tar -xf httpd-2.2.31.tar.gz
cd httpd-2.2.31

make clean

openssl version -d
** catat lokasi OPENSSLDIR

sudo pico apache.sh
** cari dan ganti baris "--with-ssl=/usr/local/ssl", harus sama dengan lokasi [OPENSSLDIR]

./apache.sh
make

make install

ldd /usr/lib/apache2/modules/mod_ssl.so
** pastikan versi libssl sudah sesuai

** kembalikan backup konfigurasi APACHE2 hasil backup
sudo rm -rf /etc/apache2
sudo cp -r ~/apache2_conf_back /etc/apache2

apache2 -v
** akan ditampilkan versi apache saat ini

** periksa keberadaan ssl.conf dan ssl.load pada /etc/apache2/mods-available
cd ..

** pastikan ssl.conf dan ssl.load sudah dienbale pada /etc/apache2/mods-enabled
ln -s ../mod-available/ssl.load ssl.load
ln -s ../mod-available/ssl.conf ssl.conf

INSTALASI PHP
-------------

https://www.php.net/distributions/php-5.4.45.tar.gz

kalau download harus pakai opsi --insecure

http://weblives.biz/2013/01/how-to-install-php-5-3-and-5-2-together-on-ubuntu/


make clean
** Buka config.nice dan koreksi baris '--with-curl=shared,/usr/bin/curl' \
./config.nice
make install

---------------------------------------------Jika terjadi error
  apxs:Error: Activation failed for custom /etc/apache2/httpd.conf
  file..
  apxs:Error: At least one `LoadModule' directive already has to exist..
  make: *** [install-sapi] Error 1

	tambahkan baris dummy ke /etc/apache2/httpd.conf

	#LoadModule dummy_module /usr/lib/apache2/modules/mod_dummy.so

	find /etc/php5/conf.d/ -name "*.ini" -exec sed -i -re 's/^(\s*)#(.*)/\1;\2/g' {} \;

  dan remark beberapa error
---------------------------------------------Jika terjadi error

libtool --finish /usr/local/src/php-5.4.45/libs


** karena curl dicompile sebagai module, sehingga perlu diaktifkan sebagai extension
buat curl.ini pada /etc/php5/conf.d

; configuration for php CURL module
extension=curl.so

