#!/bin/bash
INSTALL_DIR=/usr/local/bin
TEMP_DIR=`mktemp -d /tmp/motion.XXXXXX`
motion_url="http://downloads.sourceforge.net/project/motion/motion%20-%203.2/3.2.12/motion-3.2.12.tar.gz?r=&ts=1446122652&use_mirror=skylink"
touch /tmp/compilation_motion_in_progress
echo 0 > /tmp/compilation_motion_in_progress
check_run()  {
    "$@"
    local status=$?
    if [ $status -ne 0 ]; then
        echo "error with $1" >&2
	exit
    fi
    return $status
}

# Check for root priviledges
if [ $(id -u) != 0 ]
then
	echo "Superuser (root) priviledges are required to install eibd"
	echo "Please do 'sudo -s' first"
	exit 1
fi
echo "*****************************************************************************************************"
echo "*                                Desinstallation des dépendance                                    *"
echo "*****************************************************************************************************"
sudo apt-get autoremove -y --force-yes  motion
rm  /etc/motion/*
echo "*****************************************************************************************************"
echo "*                                          Installation de FFMPEG                                   *"
echo "*****************************************************************************************************"
echo 1 > /tmp/compilation_motion_in_progress
test=$(grep '#http://www.deb-multimedia.org' /etc/apt/sources.list)
if [ -z "$test" ] || [ $test = " " ] || [ $test = "" ]
then 
	echo "#http://www.deb-multimedia.org" | sudo tee -a /etc/apt/sources.list
	echo "deb http://www.deb-multimedia.org jessie main non-free" | sudo tee -a /etc/apt/sources.list
	echo "deb-src http://www.deb-multimedia.org jessie main non-free" | sudo tee -a /etc/apt/sources.list
fi 
sudo apt-get -y update
sudo apt-get install-y --force-yes  deb-multimedia-keyring
sudo apt-get -y update
echo 2 > /tmp/compilation_motion_in_progress
echo "*****************************************************************************************************"
echo "*                                Installing additional libraries                                    *"
echo "*****************************************************************************************************"
sudo apt-get install -y --force-yes curl
echo 4 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes debhelper
echo 5 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes dpkg-dev
echo 6 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes dh-autoreconf
echo 7 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libavcodec-dev
echo 8 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libavformat-dev
echo 9 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libjpeg-dev
echo 10 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libpq-dev
echo 11 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libv4l
echo 12 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libv4l-dev
echo 13 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes zlib1g-dev
echo 14 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes libav-tools 
echo 15 > /tmp/compilation_motion_in_progress
sudo apt-get install -y --force-yes ffmpeg
echo 20 > /tmp/compilation_motion_in_progress
echo "*****************************************************************************************************"
echo "*                                          Compilation de motion:                                   *"
echo "*****************************************************************************************************"
sudo rm /etc/motion/*
sudo apt-get install -y --force-yes motion
echo 70 > /tmp/compilation_motion_in_progress
#cd $TEMP_DIR
#check_run wget -q $motion_url -O - | tar -zx
#cd motion-3.2.12
#./configure --without-mysql --without-pgsql 
#make
#sudo make install
echo "*****************************************************************************************************"
echo "*                                     Demarrage automatique                                         *"
echo "*****************************************************************************************************"
sudo tee /etc/default/motion <<- 'EOF'
start_motion_daemon=yes
EOF
echo 75 > /tmp/compilation_motion_in_progress
echo "*****************************************************************************************************"
echo "*                                     Configuration de motion                                       *"
echo "*****************************************************************************************************"
sudo rm /etc/motion/*
sudo chmod -R 777 /etc/motion/
sudo usermod -a -G motion www-data
#echo 80 > /tmp/compilation_motion_in_progress
#php /usr/share/nginx/www/jeedom/plugins/motion/core/php/UpdateMotionConf.php
echo 100 > /tmp/compilation_motion_in_progress
rm /tmp/compilation_motion_in_progress
