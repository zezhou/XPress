#!/bin/bash
USAGE="Usage: ./install.sh INSTALL_PATH" 
SOURCE_PATH=../
XPRESS_PATH=${SOURCE_PATH}xpress
if [ -n "$1" ];then
    XPRESS_PATH=$1
    sudo rm -rf $XPRESS_PATH
    sudo cp -rf ${SOURCE_PATH}release $XPRESS_PATH
    sudo chmod 0777 $XPRESS_PATH
    sudo chmod 0777 $XPRESS_PATH/admin
    sudo chmod 0777 $XPRESS_PATH/post
    sudo chmod 0777 $XPRESS_PATH/install.php
    sudo chmod 0777 $XPRESS_PATH/rss.xml
    echo "done."
else
    echo $USAGE
fi

