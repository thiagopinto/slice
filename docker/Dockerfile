# docker/Dockerfile

FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libssl-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libmemcached-dev \
    zlib1g-dev \
    libcurl4-openssl-dev \
    libwebp-dev \
    libxpm-dev \
    libgd-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP principais
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    intl \
    gd \
    opcache \
    sockets \
    soap

# Instalar Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalar Xdebug (para desenvolvimento)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Permissões
RUN chown -R www-data:www-data /var/www

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]