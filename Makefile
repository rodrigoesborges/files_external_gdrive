# Makefile for building the project

app_name=files_external_gdrive
project_dir=$(CURDIR)
build_dir=$(CURDIR)/build/artifacts
appstore_dir=$(build_dir)/appstore
source_dir=$(build_dir)/source
sign_dir=$(build_dir)/sign
package_name=$(app_name)
cert_dir=$(HOME)/.nextcloud/certificates

.PHONY: all
all: appstore

.PHONY: clean
clean:
	rm -rf $(build_dir)

.PHONY: composer.phar
composer.phar:
	curl -sS https://getcomposer.org/installer | php

.PHONY: install
install: clean install-deps

.PHONY: install-deps
install-deps: install-composer-deps-dev

.PHONY: install-composer-deps
install-composer-deps: composer.phar
	php composer.phar install --no-dev -o

.PHONY: install-composer-deps-dev
install-composer-deps-dev: composer.phar
	php composer.phar install -o

.PHONY: dev-setup
dev-setup: install-composer-deps-dev

.PHONY: update-composer
update-composer: composer.phar
	rm -f composer.lock
	php composer.phar install --prefer-dist

.PHONY: appstore
appstore: clean install-deps
	mkdir -p $(sign_dir)
	rsync -a \
	--exclude=.* \
	--exclude=/build \
	--exclude=composer.json \
	--exclude=composer.lock \
	--exclude=composer.phar \
	--exclude=CONTRIBUTING.md \
	--exclude=coverage \
	--exclude=Gruntfile.js \
	--exclude=issue_template.md \
	--exclude=js/tests \
	--exclude=karma.conf.js \
	--exclude=l10n/no-php \
	--exclude=Makefile \
	--exclude=nbproject \
	--exclude=/node_modules \
	--exclude=package.json \
	--exclude=phpunit*xml \
	--exclude=screenshots \
	--exclude=tests \
	--exclude=vendor/bin \
	$(project_dir) $(sign_dir)
	tar -czf $(build_dir)/$(app_name).tar.gz \
	   -C $(sign_dir) $(app_name)
	openssl dgst -sha512 -sign $(cert_dir)/$(app_name).key $(build_dir)/$(app_name).tar.gz | openssl base64 > $(build_dir)/$(app_name).b64
	rm -rf $(build_dir)/artifacts
	cat $(build_dir)/$(app_name).b64 \
