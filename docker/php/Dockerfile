FROM php:8.1-fpm

USER root

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    sudo \
    build-essential \
    curl \
    libzip-dev \
    zip \
    libcurl4-openssl-dev  \
    pkg-config  \
    libssl-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_mysql pgsql mysqli exif zip

# Install xdebug 3.2.2 (compatible with php 8.2)
RUN pecl install xdebug-3.2.2 && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add a non-root user for the application
RUN groupadd user-service-group
RUN useradd -ms /bin/bash -g user-service-group user-service-user
USER user-service-user

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
