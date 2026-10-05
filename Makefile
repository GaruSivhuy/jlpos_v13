.PHONY: deploy

deploy:
	@read -p "⚠️  Warning: This will deploy the application. Are you sure? [y/N] " confirm; \
	if [ "$$confirm" = "y" ] || [ "$$confirm" = "Y" ]; then \
		./vendor/bin/dep deploy; \
	else \
		echo "Operation cancelled."; \
		exit 1; \
	fi
