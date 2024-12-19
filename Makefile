.PHONY: build up composer export-products import-products bash

all: build up composer

build:
	docker compose up --build

up:
	docker compose up -d

composer:
	docker exec -it pobo-sdk-php composer install

export:
	docker exec -it pobo-sdk-php php bin/console app:products:export

import:
	docker exec -it pobo-sdk-php php bin/console app:products:import

bash:
	docker exec -it pobo-sdk-php bash

help:
	@echo "Available targets:"
	@echo "  make build           - Build docker containers"
	@echo "  make up              - Run docker containers in background"
	@echo "  make composer        - Install composer dependencies"
	@echo "  make export 		  - Run product export command"
	@echo "  make import	      - Run product import command"
	@echo "  make bash            - Open bash in php container"