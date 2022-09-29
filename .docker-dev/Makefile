include .env
UID:= id -u ${DOCKER_USER}
GID:= id -g ${DOCKER_USER}
export DOCKER_UID = $(shell $(UID))
export DOCKER_GID = $(shell $(GID))

up:
	docker-compose up -d
stop:
	docker-compose stop
ps:
	docker-compose ps
cache:
	docker-compose exec recruitis-php find /tmp/symfony-cache/ -type f -exec rm -f {} + ;
bash:
	docker-compose exec recruitis-php bash
bashr:
	docker-compose exec --user root recruitis-php bash
bashw:
	docker-compose exec recruitis-nginx ash
log:
	tail -F ../var/log/dev.log
logp:
	docker-compose logs recruitis-php
logw:
	docker-compose logs recruitis-nginx
build:
	docker-compose up -d --build --force-recreate
test:
	docker-compose exec recruitis-php php bin/phpunit
