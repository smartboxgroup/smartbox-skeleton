DOCKER_COMPOSE  = docker-compose

EXEC_PHP        = $(DOCKER_COMPOSE) exec php

SYMFONY         = $(EXEC_PHP) app/console
COMPOSER        = $(EXEC_PHP) composer

## 
## Project
## -------
## 

build:
	@$(DOCKER_COMPOSE) pull --parallel --quiet --ignore-pull-failures 2> /dev/null
	$(DOCKER_COMPOSE) build --pull

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: ## Install and start the project
install: params build start assets rabbit

reset: ## Stop and start a fresh install of the project
reset: kill install

start: ## Start the project
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate

stop: ## Stop the project
	$(DOCKER_COMPOSE) stop


ping: ## Ping your application
	$(EXEC_PHP) php app/console skeleton:send:ping
	$(EXEC_PHP) php app/console skeleton:send:async-ping
	$(EXEC_PHP) php app/console smartesb:consumer:start queue://api/normal/skeleton/v0/asyncping

clean: ## Stop the project and remove generated files
clean: kill
	rm -rf app/config/parameters.yml vendor app/cache/*

clear-cache: ## Clear the cache.
	rm -Rf app/cache/*


no-docker:
	$(eval DOCKER_COMPOSE := \#)
	$(eval EXEC_PHP := )

rabbit:
	$(DOCKER_COMPOSE) exec rabbit.local rabbitmq-plugins enable rabbitmq_stomp

.PHONY: params build kill install reset start stop clean no-docker rabbit

##
## Utils
## -----
##

assets: ## Run assets:install
assets: vendor
	$(SYMFONY) assets:install

.PHONY: assets

##
## Tests
## -----
##

tests: ## Run flow tests
tests: vendor clear-cache
	$(EXEC_PHP) bin/simple-phpunit --stop-on-failure

.PHONY: tests

# rules based on files
composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(DOCKER_COMPOSE) exec php composer install --prefer-dist

params:
	cp app/config/parameters.yml.dist app/config/parameters.yml

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
