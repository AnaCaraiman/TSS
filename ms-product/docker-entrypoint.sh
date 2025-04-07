#!/bin/bash

composer install --no-interaction --no-dev --optimize-autoloader

chmod -R 777 storage/

echo "Testing Laravel logging..."
php artisan tinker --execute "Log::info('Test log message');"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
