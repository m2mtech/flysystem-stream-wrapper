FROM php:8.0-cli-alpine
WORKDIR /app
RUN docker-php-ext-install pcntl shmop sysvmsg
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
