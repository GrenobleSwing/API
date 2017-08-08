#! /bin/bash

# Install dependencies
docker-compose exec -T php composer install

# Update tables
docker-compose exec -T php php bin/console doctrine:schema:update --force

# Load some fixtures to test the app
docker-compose exec -T php php bin/console doctrine:fixtures:load -n

# Warmup the cache
docker-compose exec -T php php bin/console cache:clear --no-warmup
docker-compose exec -T php php bin/console cache:warmup

