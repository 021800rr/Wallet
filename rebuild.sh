docker compose --env-file .env.prod.local down --remove-orphans && \
docker compose --env-file .env.prod.local build --no-cache && \
docker compose --env-file .env.prod.local up -d
