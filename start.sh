#!/bin/sh

# simple start for docker-compose

# ip address used to configure the debug session an the database connection
ip_addres=$(eval hostname -I | awk '{print $1}')

# you can define to not install xdebug if you want
use_xdebug=false

# database connection configuration {
    # -- database drive
    db_driver=??

    # -- database name
    db_name=??

    # -- database user
    db_user=??

    # -- database password
    db_pass=??
# }

IP=$ip_addres \
INSTALL_XDEBUG=$use_xdebug \
DB_DRIVER=$db_driver \
DB_HOST=$ip_addres \
DB_NAME=$db_name \
DB_USER=$db_user \
DB_PASS=$db_pass \
docker-compose up -d