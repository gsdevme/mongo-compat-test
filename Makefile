.PHONY: all
default: all;

mongo=mongo30

build:
	docker-compose \
		-f docker-compose.yml \
		-f docker-compose.$(mongo).yml \
		--project-directory $(CURDIR) \
		build

stop:
	docker-compose \
		-f docker-compose.yml \
		-f docker-compose.$(mongo).yml \
		--project-directory $(CURDIR) \
		down --remove-orphans

shell:
	docker-compose \
		-f docker-compose.yml \
		-f docker-compose.$(mongo).yml \
		--project-directory $(CURDIR) run php bash

run:
	docker-compose \
		-f docker-compose.yml \
		-f docker-compose.$(mongo).yml \
		--project-directory $(CURDIR) run php make ci

ci:
	composer install -q
	php src/index.php
