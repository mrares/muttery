#!/bin/bash

curl -s https://getcomposer.org/installer | php

php composer.phar self-update
php composer.phar update

php app/console cache:clear

sudo setfacl -R -m u:nobody:rwx -m u:`whoami`:rwx app/cache app/logs
sudo setfacl -dR -m u:nobody:rwx -m u:`whoami`:rwx app/cache app/logs


php app/console assets:install --symlink
php app/console assetic:dump 
