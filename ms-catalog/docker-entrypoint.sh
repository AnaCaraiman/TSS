#!/bin/bash

composer install --no-interaction --no-dev --optimize-autoloader

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

chmod -R 777 storage/

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:restart

composer require laravel/octane
php artisan octane:install
composer require vladimir-yuldashev/laravel-queue-rabbitmq
php artisan migrate


echo "Testing Laravel logging..."
php artisan tinker --execute "Log::info('Test log message');"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
