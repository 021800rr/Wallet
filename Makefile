SHELL := /bin/bash

tests:
	symfony console cache:clear -n --env=test
	symfony console doctrine:database:drop --force --env=test || true
	symfony console doctrine:database:create --env=test
	symfony console doctrine:migrations:migrate -n --env=test
	symfony console doctrine:fixtures:load -n --env=test
	symfony console cache:clear -n --env=test
	SYMFONY_DEPRECATIONS_HELPER='disabled=1' php bin/phpunit && \
		vendor/bin/phpstan analyse -l 9 --memory-limit 1G src tests && \
		tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
.PHONY: tests