#!/usr/bin/env bash

sed -i 's/^      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini*/#      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini/g' docker-compose.yml
docker compose --env-file .env.dev.local up -d --build php-service >> /dev/null
echo "Xdebug DISABLED"

docker exec -i wallet-php-container-dev bash -c 'cd /var/www/ && make tests'

sed -i 's/^#      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini*/      - .\/docker\/php\/conf.d\/xdebug.ini:\/usr\/local\/etc\/php\/conf.d\/docker-php-ext-xdebug.ini/g' docker-compose.yml
docker compose --env-file .env.dev.local up -d --build php-service >> /dev/null
echo "Xdebug ENABLED"
