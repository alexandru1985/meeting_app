FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev 

# Copy custom configuration file    
COPY php.conf /usr/local/etc/php-fpm.d/

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip exif pcntl

# Install Node.js    
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | /bin/sh - 
RUN apt-get install -y nodejs 
RUN echo "NODE Version:" && node --version
RUN echo "NPM Version:" && npm --version

# Install composer    
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY . /var/www

# Change current user to www-data
USER www-data

EXPOSE 9000
CMD ["php-fpm"]

