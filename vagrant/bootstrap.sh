#!/bin/sh

sudo chmod 0777 -R /var/www/furniture.dev/app/cache
sudo chmod 0777 -R /var/www/furniture.dev/app/logs
echo "cd /var/www/furniture.dev" >> /home/vagrant/.bashrc

cd /var/www/furniture.dev
/usr/local/bin/composer install --no-interaction
