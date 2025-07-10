FROM php:8.4-fpm

# Установка системных зависимостей
RUN apt-get update && \
    apt-get install -y \
        libicu-dev \
        libzip-dev \
        unzip \
        git \
        libpq-dev \
        librdkafka-dev \
        curl \
    && docker-php-ext-install \
        intl \
        zip \
        pdo_pgsql \
        pgsql \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Установка Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get install -y symfony-cli \
    && symfony server:ca:install

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Копируем кастомные настройки PHP
COPY custom-php.ini /usr/local/etc/php/conf.d/

# Рабочая директория
WORKDIR /var/www/html
