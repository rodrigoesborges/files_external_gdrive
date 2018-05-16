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
	--exclude=bower.json \
	--exclude=.bowerrc \
	--exclude=/build \
	--exclude=composer.json \
	--exclude=composer.lock \
	--exclude=composer.phar \
	--exclude=CONTRIBUTING.md \
	--exclude=coverage \
	--exclude=.git \
	--exclude=.gitattributes \
	--exclude=.github \
	--exclude=.gitignore \
	--exclude=Gruntfile.js \
	--exclude=.hg \
	--exclude=issue_template.md \
	--exclude=.jscsrc \
	--exclude=.jshintignore \
	--exclude=.jshintrc \
	--exclude=js/tests \
	--exclude=karma.conf.js \
	--exclude=l10n/no-php \
	--exclude=.tx \
	--exclude=Makefile \
	--exclude=nbproject \
	--exclude=/node_modules \
	--exclude=package.json \
	--exclude=.phan \
	--exclude=phpunit*xml \
	--exclude=screenshots \
	--exclude=.scrutinizer.yml \
	--exclude=tests \
	--exclude=.travis.yml \
	--exclude=vendor/bin \
	$(project_dir)/ $(sign_dir)/$(app_name)
	@if [ -f $(cert_dir)/$(app_name).key ]; then \
		echo "Signing app files…"; \
		php ../../occ integrity:sign-app \
			--privateKey=$(cert_dir)/$(app_name).key\
			--certificate=$(cert_dir)/$(app_name).crt\
			--path=$(sign_dir)/$(app_name); \
	fi
	tar -czf $(build_dir)/$(app_name).tar.gz \
		-C $(sign_dir) $(app_name)
	@if [ -f $(cert_dir)/$(app_name).key ]; then \
		echo "Signing package…"; \
		openssl dgst -sha512 -sign $(cert_dir)/$(app_name).key $(build_dir)/$(app_name).tar.gz | openssl base64; \
fi
