#!/bin/sh
php artisan down
supervisorctl stop all
sudo -u www git pull
php artisan migrate
composer install
supervisorctl restart all
php artisan up
