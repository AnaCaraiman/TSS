#!/bin/bash

composer install --no-interaction --no-dev --optimize-autoloader

composer require laravel/octane vladimir-yuldashev/laravel-queue-rabbitmq
php artisan octane:install
php artisan migrate


chmod -R 777 storage/
echo "Testing Laravel logging..."
php artisan tinker --execute "Log::info('Test log message');"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
