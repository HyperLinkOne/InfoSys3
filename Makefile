SHELL:=/usr/bin/bash
PROJECT=php

.DEFAULT_GOAL:=help

.PHONY: build up down exec start stop restart


DOCKER_COMPOSE_COMMAND := @docker compose -f ${PWD}/docker-compose.yaml # -f ${PWD}/docker-compose-dev.yaml

UNAME_S := $(shell uname -s)

help: ## Show this help.
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

#####################
###  Development  ###
#####################
build: ## build docker image
#	cp .env.dev .env
	${DOCKER_COMPOSE_COMMAND} up -d  --build
	${DOCKER_COMPOSE_COMMAND} exec ${PROJECT} composer install

up: ## just up
	${DOCKER_COMPOSE_COMMAND} up

upd: ## just up
	${DOCKER_COMPOSE_COMMAND} up -d

down: ## just down
	${DOCKER_COMPOSE_COMMAND} down

exec: ## Exec into the container
	${DOCKER_COMPOSE_COMMAND} exec  ${PROJECT} bash

stop: ## Stop the container
	${DOCKER_COMPOSE_COMMAND} stop  ${PROJECT}

start: ## Start the container
	${DOCKER_COMPOSE_COMMAND} start  ${PROJECT}

restart: ## Restart the container
	${DOCKER_COMPOSE_COMMAND} restart  ${PROJECT}

echo: ##just echo
	@echo ${DOCKER_COMPOSE_COMMAND}
