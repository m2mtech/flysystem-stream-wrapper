FROM php:7.4-cli-alpine
WORKDIR /app
RUN docker-php-ext-install pcntl shmop sysvmsg
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
