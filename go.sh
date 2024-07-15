docker compose --env-file .env.prod.local down --remove-orphans && \
docker compose --env-file .env.prod.local up -d
docker ps -a
