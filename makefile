#
# Makefile
#

.PHONY: help
.DEFAULT_GOAL := help


help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ------------------------------------------------------------------------------------------------------------

setup: ## Installs Downtown
	docker-compose up -d
	docker-compose exec cli sh -c 'composer install'
	docker-compose exec cli sh -c 'bin/console system:setup'
	docker-compose exec cli sh -c 'bin/console system:install --create-database --basic-setup --force'
	docker-compose exec cli sh -c 'bin/console system:generate-jwt-secret --force'
	docker-compose exec cli sh -c 'bin/console system:generate-app-secret'

merchant: ## Runs the merchant portal
	cd src/MerchantFrontend npm install && npm run start

db: ## Open MySQL container
	docker-compose exec mysql mysql -p

shop: ## Opens the shop
	docker-compose exec app_server sh
