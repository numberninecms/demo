.DEFAULT_GOAL := help

COMPOSER = composer
PHPUNIT = php bin/phpunit
SYMFONY = php bin/console
SYMFONY_BIN = symfony
YARN = yarn
PHPSTAN = vendor/bin/phpstan
TWIGCS = vendor/bin/twigcs
DSYMFONY = docker exec numbernine_demo_php $(SYMFONY)
DCOMPOSER = docker exec numbernine_demo_composer

##
## Development tools
dump-routing: ## Dump routes to Admin project
	@$(SYMFONY) fos:js-routing:dump --callback="export default " --target=../admin/src/assets/routes/fos_js_routes.ts

##
## Database
.PHONY: db db-cache fixtures

db: db-reset fixtures ## Reset database and load fixtures

db-reset: ## Reset database
	@$(EXEC_PHP) php -r 'echo "Waiting for database...\n"; set_time_limit(30); for(;;) { if(@fsockopen(localhost.":".(3306))) { break; }}'
	@-$(SYMFONY) doctrine:database:drop --if-exists --force
	@-$(SYMFONY) doctrine:database:create --if-not-exists
	@$(SYMFONY) doctrine:schema:update --force

db-cache: ## Clear doctrine database cache
	@$(SYMFONY) doctrine:cache:clear-metadata
	@$(SYMFONY) doctrine:cache:clear-query
	@$(SYMFONY) doctrine:cache:clear-result
	@echo "Cleared doctrine cache"

fixtures: ## Load fixtures
	@rm -rf public/uploads
	@$(SYMFONY) doctrine:fixtures:load --no-interaction

##
## Lint
.PHONY: lint lint-container lint-twig lint-xliff lint-yaml

lint: vendor lint-container lint-twig lint-xliff lint-yaml ## Run all lint commands

lint-container: vendor ## Checks the services defined in the container
	@$(SYMFONY) lint:container

lint-twig: vendor ## Check twig syntax in /templates folder (prod environment)
	@$(SYMFONY) lint:twig templates -e prod

lint-xliff: vendor ## Check xliff syntax in /translations folder
	@$(SYMFONY) lint:xliff translations

lint-yaml: vendor ## Check yaml syntax in /config and /translations folders
	@$(SYMFONY) lint:yaml config translations


##
## Node.js
.PHONY: assets

yarn.lock: package.json
	$(YARN) upgrade

node_modules: yarn.lock ## Install yarn packages
	@$(YARN)

assets: node_modules ## Run Webpack Encore to compile assets
	@$(YARN) dev


##
## PHP
composer.lock: composer.json
	@$(COMPOSER) update

vendor: composer.lock ## Install dependencies in /vendor folder
	@$(COMPOSER) install


##
## Project
.PHONY: install start update cache-clear cache-warmup clean reset

install: vendor db assets ## Install project dependencies

start: install serve ## Install project dependencies and launch symfony web server

update: vendor node_modules ## Update project dependencies
	$(COMPOSER) update
	$(YARN) upgrade

cc: ## Clear and warmup cache
	@$(SYMFONY) cache:clear

cache-clear:
	@$(SYMFONY) cache:clear --no-warmup

cache-warmup: cache-clear
	@$(SYMFONY) cache:warmup

clean: purge ## Delete all dependencies
	@rm -rf .env.local node_modules var vendor
	@echo -e "Vendor and node_modules folder have been deleted !"

reset: unserve clean install


##
## Symfony bin
.PHONY: serve unserve security

serve: ## Run symfony web server in the background
	@$(SYMFONY_BIN) serve --daemon --no-tls

unserve: ## Stop symfony web server
	@$(SYMFONY_BIN) server:stop

security: vendor ## Check packages vulnerabilities (using composer.lock)
	@$(SYMFONY_BIN) check:security


##
## Tests
.PHONY: tests

tests: vendor ## Run tests
	@$(PHPUNIT)

phpstan:
	@$(PHPSTAN) analyse > var/log/phpstan.log

twigcs:
	@$(TWIGCS) lib > var/log/twigcs.log

##
## Utils
.PHONY: purge

purge: ## Purge cache and logs
	@rm -rf var/cache/* var/log/*
	@echo -e "Cache and logs have been deleted !"


##
## Docker

.PHONY: docker-db docker-db-cache docker-fixtures

docker-db: docker-db-reset docker-fixtures ## Reset database and load fixtures

docker-db-reset: ## Reset database
	@-$(DSYMFONY) doctrine:database:drop --if-exists --force
	@-$(DSYMFONY) doctrine:database:create --if-not-exists
	@$(DSYMFONY) doctrine:schema:update --force

docker-db-cache: ## Clear doctrine database cache
	@$(DSYMFONY) doctrine:cache:clear-metadata
	@$(DSYMFONY) doctrine:cache:clear-query
	@$(DSYMFONY) doctrine:cache:clear-result
	@echo "Cleared doctrine cache"

docker-fixtures: ## Load fixtures
	@$(DSYMFONY) doctrine:fixtures:load --no-interaction

docker-cc: ## Clear and warmup cache
	@$(DSYMFONY) cache:clear

##
## Help
help: ## List of all commands
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
