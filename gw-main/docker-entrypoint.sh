#!/bin/bash

composer install --no-interaction --no-dev --optimize-autoloader

chmod -R 777 storage/

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
