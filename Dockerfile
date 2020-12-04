FROM php:5.5-cli as base

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN buildDeps=" \
        wget \
        git \
        less \
        zip \
        unzip \
        libzip-dev \
        libssh-dev \
        libicu-dev \
    "; \
    set -x \
    && apt-get update \
    && apt-get install -y $buildDeps --no-install-recommends

RUN docker-php-ext-install -j$(nproc) zip json intl sockets \
    && pecl install mongo \
    && docker-php-ext-enable mongo \
    && rm -rf /var/lib/apt/lists/* \
    && echo "memory_limit=-1" > /usr/local/etc/php/conf.d/z-memory.ini

RUN mkdir -p /app/ && chown -R www-data:www-data /app/

COPY composer.json /app/composer.json
COPY Makefile /app/Makefile

WORKDIR /app/

