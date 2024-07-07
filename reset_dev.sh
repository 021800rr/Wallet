symfony console cache:clear -n --env=dev
symfony console doctrine:database:drop --force --env=dev || true
symfony console doctrine:database:create --env=dev
symfony console doctrine:migrations:migrate -n --env=dev
symfony console doctrine:fixtures:load -n --env=dev
symfony console cache:clear -n --env=dev
