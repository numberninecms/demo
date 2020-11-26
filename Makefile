.DEFAULT_GOAL := help

DOCKER = docker-compose exec php
SYMFONY = $(DOCKER) php bin/console
CURDIR_NAME = $(notdir $(CURDIR))
DOCKER_IMAGE = repo/image:tag # Change this with your app image

##
##Database
.PHONY: db-reset db-prune db-migrate db-fixtures

db-reset: db-prune db-migrate db-fixtures

db-prune: ## Delete database and recreated it empty
	$(DOCKER) sh -c 'rm -f migrations/*.php'
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create --if-not-exists

db-migrate: ## Create migration file and update schema
	$(SYMFONY) doctrine:migrations:diff --allow-empty-diff --no-interaction
	$(SYMFONY) doctrine:migrations:migrate --allow-no-migration --no-interaction

db-fixtures: ## Load fixtures
	$(DOCKER) sh -c 'rm -rf public/uploads/*'
	$(SYMFONY) doctrine:fixtures:load --no-interaction


##
##Cache
.PHONY: cc

cc: ## Clear and warmup cache
	$(SYMFONY) cache:clear


##
##Installation
.PHONY: install

install: ## Create database, load fixtures, update files to the latest
	doctl auth init
	doctl registry login --expiry-seconds 300
	docker pull $(DOCKER_IMAGE)
	docker run --rm -i -v $(CURDIR_NAME)_app_files:/tmp/app $(DOCKER_IMAGE) sh -c 'rsync -aq --exclude "/public/uploads" --exclude "/migrations" --exclude "/var" --exclude= --delete-after ./ /tmp/app/'
	docker run --rm -i -v $(CURDIR_NAME)_app_files:/tmp/app $(DOCKER_IMAGE) chown -R '1000:1000' /tmp/app/
	@make db-reset --no-print-director
	@make cc --no-print-director


##
##Deployment
.PHONY: deploy

deploy: ## Deploy new files and update database schema
	doctl auth init
	doctl registry login --expiry-seconds 300
	docker pull $(DOCKER_IMAGE)
	docker run --rm -i -v $(CURDIR_NAME)_app_files:/tmp/app $(DOCKER_IMAGE) sh -c 'rsync -aq --exclude "/public/uploads" --exclude "/migrations" --exclude "/var" --exclude= --delete-after ./ /tmp/app/'
	docker run --rm -i -v $(CURDIR_NAME)_app_files:/tmp/app $(DOCKER_IMAGE) chown -R '1000:1000' /tmp/app/
	@make db-migrate --no-print-director
	@make cc --no-print-director


##
##Help
help: ## List of all commands
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
