.DEFAULT_GOAL = help
VERBOSE ?= 0
QUIET = --quiet
SYSTEMQUIET = > /dev/null 2>&1

ifeq ($(VERBOSE),1)
	QUIET =
	SYSTEMQUIET =
endif

.PHONY: install install-intro
install: install-intro dependencies reset ## Install project. Use this command on first install.
	@echo "\nDone.\n"

install-intro:
	@echo "\n\033[33mInstallation\n============\033[0m"

.PHONY: dependencies
dependencies:
	@echo "\n\033[33mProject dependencies\n--------------------\033[0m\n"
	@echo "\033[32mLaunching Docker containers...\033[0m"
	@docker-compose up -d $(SYSTEMQUIET)
	@echo "\033[32mInstalling composer dependencies...\033[0m"
	@docker-compose run --rm php composer install $(QUIET)

.PHONY: reset
reset: ## Reset demo data on an existing installation.
	@echo "\n\033[33mResetting demo data\n-------------------\033[0m\n"
	@echo "\033[32mWaiting for database to be ready...\033[0m"
	@docker-compose exec -T php php -r 'set_time_limit(30); for(;;) { if(@fsockopen("mysql:".(3306))) { break; }}'
	@echo "\033[32mCleaning uploads...\033[0m"
	@rm -rf public/uploads
	@echo "\033[32mPreparing schema...\033[0m"
	@docker-compose exec -T php bin/console doctrine:database:drop --if-exists --force $(QUIET)
	@docker-compose exec -T php bin/console doctrine:database:create --if-not-exists $(QUIET)
	@docker-compose exec -T php bin/console doctrine:migrations:migrate --no-interaction $(QUIET)
	@echo "\033[32mLoading fixtures...\033[0m"
	@docker-compose exec -T php bin/console doctrine:fixtures:load --no-interaction $(QUIET)
	@docker-compose exec -T php bin/console numbernine:make:default-pages --username=admin --no-interaction $(QUIET)

.PHONY: cc
cc: ## Clear all caches (APCu, OPCache, Redis and Symfony).
	docker-compose exec php cachetool apcu:cache:clear
	docker-compose exec php cachetool opcache:reset
	docker-compose exec redis redis-cli flushall
	docker-compose exec php bin/console cache:clear

.PHONY: help
help: ## List all commands.
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
