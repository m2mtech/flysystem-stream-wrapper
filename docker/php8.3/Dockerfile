FROM php:8.3-rc-cli-alpine
WORKDIR /app
RUN apk add --no-cache --virtual .persistent-deps \
    bash \
    icu-libs \
  && set -eux \
  && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    icu-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install \
    intl \
    pcntl shmop sysvmsg \
  && apk del --no-cache .build-deps
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser
