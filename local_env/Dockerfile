FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY ../server /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data ../server /var/www

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev
RUN composer dump-autoload --optimize

# Copy nginx configuration
COPY nginx.conf /etc/nginx/sites-available/default

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Change current user to www
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 80
CMD ["/usr/bin/supervisord"]