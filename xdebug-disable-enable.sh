#!/usr/bin/env bash

if [ "$#" -ne 1 ]; then
    SCRIPT_PATH=`basename "$0"`
    echo "Usage: $SCRIPT_PATH enable|disable"
    exit 1;
fi

if [ "$1" == "enable" ]; then
    sed -i 's/^#      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini*/      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini/g' docker-compose.yml
    docker compose --env-file .env.dev.local up -d --build php-service >> /dev/null
    echo "Xdebug ENABLED"
else
    sed -i 's/^      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini*/#      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini/g' docker-compose.yml
    docker compose --env-file .env.dev.local up -d --build php-service >> /dev/null
    echo "Xdebug DISABLED"
fi
