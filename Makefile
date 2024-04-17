#!/bin/bash
include .makerc

DOCKER_BE = ${PROJECT_NAME}-be
DOCKER_WEB = ${PROJECT_NAME}-web
DOCKER_NETWORK = ${PROJECT_NAME}-network
DOCKER_PHPMYADMIN = ${PROJECT_NAME}-phpmyadmin

OS := $(shell uname)

ifeq ($(OS),Darwin)
	UID = $(shell id -u)
else ifeq ($(OS),Linux)
	UID = $(shell id -u)
else
	true = $true
	UID = 1000
endif

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

initialize-project: ## inicialize the project name, rebuilds all containers, aplication, create the jwt key for authentication and creates and fills the database
	$(MAKE) build && U_ID=${UID} docker-compose --env-file ./docker/.env.dev up -d && $(MAKE) composer-install && $(MAKE) generate-jwt-key && $(MAKE) run-fixtures && $(MAKE) restart

run: ## Start the containers
	docker network create ${DOCKER_NETWORK} || echo $(true)
	U_ID=${UID}  docker-compose --env-file ./docker/.env.dev up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose  stop

restart: ## Restart the containers
	$(MAKE) stop && $(MAKE) run

build: ## Rebuilds all the containers
	$(MAKE) stop && U_ID=${UID} PROJECT_NAME=${PROJECT_NAME} docker-compose build

prepare: ## Runs backend commands
	$(MAKE) composer-install

# Backend commands
composer-install: ## Installs composer dependencies
	U_ID=${UID} docker exec --user ${UID} -it ${DOCKER_BE} composer install --no-interaction --optimize-autoloader

be-logs: ## Tails the Symfony dev log
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} tail -f var/log/dev.log
# End backend commands

ssh-be: ## ssh's into the be container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash

ssh-be-root: ## ssh's into the be container
	docker exec -it --user 0 ${DOCKER_BE} bash

ssh-app: ## ssh's into the app container
	docker exec -it --user ${UID} ${DOCKER_APP} bash

code-style: ## Runs php-cs to fix code styling following Symfony rules
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php-cs-fixer fix src --rules=@Symfony
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php-cs-fixer fix tests --rules=@Symfony

generate-ssh-keys: ## Generates SSH keys for the JWT library
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} mkdir -p config/jwt
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} openssl genrsa -passout pass:767b453a97ac019714eb7ccbce781d16 -out config/jwt/private.pem -aes256 4096
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} openssl rsa -pubout -passin pass:767b453a97ac019714eb7ccbce781d16 -in config/jwt/private.pem -out config/jwt/public.pem

run-fixtures: ## Run the Fixtures
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:schema:update --dump-sql --force
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:fixtures:load --no-interaction

generate-jwt-key: ## Generates SSH keys for the API Platform JWT library
	docker exec -it --user ${UID} ${DOCKER_BE} php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

run-cache-clear: ## clear prod symfony cache
	rm -Rf ./var/cache/prod

run-cache-clear-dev: ## clear dev symfony cache
	rm -Rf ./var/cache/dev

remove-containers: ## Remove containers
	docker rm ${DOCKER_DB} ${DOCKER_WEB} ${DOCKER_BE} ${DOCKER_PHPMYADMIN}

remove-not-uses-images:
	docker image prune -a

remove-network: ## Remove network
	docker network rm ${DOCKER_NETWORK}

