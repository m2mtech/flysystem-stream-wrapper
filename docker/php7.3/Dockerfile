FROM php:7.3-cli-alpine
WORKDIR /app
RUN docker-php-ext-install pcntl shmop sysvmsg
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser
