#!/bin/bash

if [ ! -d "/var/www/html/vendor" ]; then
    composer install
fi

/usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
