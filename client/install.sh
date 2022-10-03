#!/bin/sh
sudo apt-get install libimage-exiftool-perl
sudo apt-get install libterm-readkey-perl
sudo apt-get install libdatetime-perl
mkdir client
cd client
[ -f "client.tar" ] && rm client.tar
wget --content-disposition --trust-server-names http://gl.m-k-c.ru/iapi/up.php?what=tar
[ ! -f "client.config" ] && wget --content-disposition --trust-server-names http://gl.m-k-c.ru/iapi/up.php?what=cfg
[ -f "runme.sh" ] && rm runme.sh
wget --content-disposition --trust-server-names http://gl.m-k-c.ru/iapi/up.php?what=sh
tar xf client.tar
chmod +x runme.sh
rm client.tar
