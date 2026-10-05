.PHONY: deploy

DEPLOY_HOST ?= sivhuy@139.59.248.243
DEPLOY_PATH ?= /var/www/jlpos
DEPLOY_BRANCH ?= master

deploy:
	@read -p "⚠️  Warning: This will deploy the application. Are you sure? [y/N] " confirm; \
	if [ "$$confirm" != "y" ] && [ "$$confirm" != "Y" ]; then \
		echo "Operation cancelled."; \
		exit 1; \
	fi
	npm run build
	rsync -az --delete --chmod=D775,F664 public/build/ $(DEPLOY_HOST):$(DEPLOY_PATH)/public/build/
	ssh $(DEPLOY_HOST) "bash -s -- $(DEPLOY_PATH) $(DEPLOY_BRANCH)" < deploy.sh
