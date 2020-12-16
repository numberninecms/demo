-include Makefile

MAKE = make -f Makefile.local.mk
PARENT_DIR = $(abspath $(dir $(lastword $(MAKEFILE_LIST)))/..)

ifeq ($(DOCKER), 1)
	PHP := docker run --rm -it -u '1000:1000' -v $(CURDIR):/srv/app -v $(PARENT_DIR):/srv/app/vendor/numberninecms --network $(CURDIR_NAME)_default -w /srv/app numberninecms/php:7.4-fpm-dev php
	COMPOSER := docker run --rm -it -u '1000:1000' -v $(CURDIR):/srv/app -v $(PARENT_DIR):/srv/app/vendor/numberninecms --network $(CURDIR_NAME)_default -w /srv/app numberninecms/php:7.4-fpm-dev composer
endif

print:
	@echo $(COMPOSER) install