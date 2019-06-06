#!/bin/sh
php artisan down
supervisorctl stop all
sudo -u www git pull
php artisan migrate
composer install
supervisorctl reload
supervisorctl start all
php artisan up
