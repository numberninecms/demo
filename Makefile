-include .env.local
.DEFAULT_GOAL := help

PHP := php
COMPOSER := composer
PHPUNIT := php bin/phpunit
SYMFONY := php bin/console
SYMFONY_BIN := symfony
YARN := yarn
PHPSTAN := vendor/bin/phpstan
TWIGCS := vendor/bin/twigcs
APP_NAME ?= numbernine
DBHOST := $(shell echo "$(DATABASE_URL)" | sed -r -e "s|^(.*\@)([^\/?\#:]+)(.*)$$|\2|g")
DOCKER ?= 0

ifeq ($(DOCKER), 1)
#	ifneq ($(DATABASE_URL),)
#		DATABASE_REPLACE := $(shell echo "$(DATABASE_URL)" | sed -r -e "s|^(.*\@)([^\/?\#:]+)(.*)$$|\1mysql\3|g")
#		DATABASE_URL := $(DATABASE_REPLACE)
#	endif
	PHP := docker exec $(APP_NAME)_php $(PHP)
	SYMFONY := docker exec $(APP_NAME)_php $(SYMFONY)
endif

##
##Development tools
dump-routing: ## Dump routes to Admin project
	@$(SYMFONY) fos:js-routing:dump --callback="export default " --target=../admin/src/assets/routes/fos_js_routes.ts

##
##Database
.PHONY: db db-cache fixtures

db: db-reset fixtures ## Reset database and load fixtures

db-reset: ## Reset database
	@$(PHP) -r 'echo "Waiting for database...\n"; set_time_limit(30); for(;;) { if(@fsockopen("$(DBHOST):".(3306))) { break; }}'
	@rm -rf migrations/*.php
	@$(SYMFONY) doctrine:database:drop --quiet --if-exists --force
	@$(SYMFONY) doctrine:database:create --quiet --if-not-exists
	@$(SYMFONY) doctrine:migrations:diff --quiet --no-interaction
	@$(SYMFONY) doctrine:migrations:migrate --quiet --no-interaction

db-cache: ## Clear doctrine database cache
	@$(SYMFONY) doctrine:cache:clear-metadata
	@$(SYMFONY) doctrine:cache:clear-query
	@$(SYMFONY) doctrine:cache:clear-result
	@echo "Cleared doctrine cache"

fixtures: ## Load fixtures
	@rm -rf public/uploads
	@$(SYMFONY) doctrine:fixtures:load --no-interaction

##
##Lint
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
##Node
.PHONY: assets

node_modules: package.json yarn.lock ## Install yarn packages
	@$(YARN)

assets: node_modules ## Run Webpack Encore to compile assets
	@$(YARN) dev

##
##PHP
composer.lock: composer.json
	@echo compose.lock is not up to date.

vendor: composer.lock ## Install dependencies in /vendor folder
	@$(COMPOSER) install

##
##Project
.PHONY: numbernine install start update cache-clear cache-warmup clean reset

numbernine: vendor ## Create NumberNine admin symlink and .env.local
	@$(SYMFONY) numbernine:install --force

install: numbernine ## Install project dependencies
	@$(MAKE) --no-print-director db
	@$(MAKE) --no-print-director assets

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
##Local server
.PHONY: serve unserve security

serve: ## Run symfony web server in the background
	@$(SYMFONY_BIN) local:server:ca:install
	@$(SYMFONY_BIN) serve -d

stop: ## Stop symfony web server
	@$(SYMFONY_BIN) server:stop

security: vendor ## Check packages vulnerabilities (using composer.lock)
	@$(SYMFONY_BIN) check:security


##
##Tests
.PHONY: tests

tests: vendor ## Run tests
	@$(PHPUNIT)

phpstan:
	@$(PHPSTAN) analyse

twigcs:
	@$(TWIGCS) lib

##
##Utils
.PHONY: purge

purge: ## Purge cache and logs
	@rm -rf var/cache/* var/log/*
	@echo -e "Cache and logs have been deleted !"

docker-install:
	@echo 'DATABASE_URL=mysql://user:user@mysql:3306/numbernine_app?serverVersion=5.7' > .env.local
	@$(MAKE) --no-print-director numbernine
	@docker-compose up -d
	@$(MAKE) --no-print-director DOCKER=1 install

##
##Help
help: ## List of all commands
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
