ARG PHP_VERSION=7.3.9

FROM php:${PHP_VERSION}-fpm-alpine

# persistent / runtime deps
RUN apk add --no-cache \
        openjdk8=8.242.08-r0 \
		acl \
		file \
		gettext \
		git \
		redis \
		poppler-utils \
		freetype \
        libpng \
        libjpeg-turbo \
        freetype-dev \
        libpng-dev \
        jpeg-dev \
        libjpeg \
        libjpeg-turbo-dev \
	;

# fonts needed for java jdk
RUN apk add --update ttf-dejavu && rm -rf /var/cache/apk/*

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && apk del pcre-dev ${PHPIZE_DEPS}

ARG APCU_VERSION=5.1.12
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		libxslt-dev \
		libxml2-dev \
	&& docker-php-ext-configure gd \
            --with-freetype-dir=/usr \
            --with-png-dir=/usr \
            --with-jpeg-dir=/usr \
        && docker-php-ext-install gd \
        && docker-php-ext-install exif \
        && docker-php-ext-install json \
        && docker-php-ext-install xml \
        && docker-php-ext-install openssl \
    && docker-php-ext-enable imagick \
	; \
	\
	docker-php-ext-configure zip --with-libzip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
		bcmath \
		xsl \
		mysqli \
		soap \
#		imagick \
#		json \
#		mbstring \
#		openssl \
		pdo_mysql \
	; \
	\
    # Install php-ext-gmp
    \
    apk add --no-cache --no-progress --virtual BUILD_DEPS_PHP_GMP gmp-dev \
    && apk add --no-cache --no-progress gmp \
    && docker-php-ext-install gmp; \
    \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps BUILD_DEPS_PHP_GMP

RUN set -ex \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS imagemagick-dev libtool \
    && export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" \
    && pecl install imagick-3.4.3 \
    && docker-php-ext-enable imagick \
    && apk add --no-cache --virtual .imagick-runtime-deps imagemagick \
    && apk del .phpize-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_host=$(/sbin/ip route|awk '/default/ { print $3 }')" >> /usr/local/etc/php/php.ini

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /application

# build for dev
ARG APP_ENV=dev

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock ./
RUN set -eux; \
	composer clear-cache

COPY . ./

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
#	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /application/var
RUN mkdir -p user_private_files
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

# fix xdebug log error permission
RUN touch /var/log/xdebug.log; \
    chmod 666 /var/log/xdebug.log

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
