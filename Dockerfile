# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (skip scripts that require artisan)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files
COPY package.json package-lock.json ./

# Install Node dependencies
RUN npm install

# Copy application code
COPY . .

# Run composer scripts now that artisan is available
RUN composer run-script post-autoload-dump

# Build frontend assets
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage/logs \
    && chmod -R 775 /var/www/html/storage/framework \
    && chmod -R 775 /var/www/html/storage/framework/cache \
    && chmod -R 775 /var/www/html/storage/framework/sessions \
    && chmod -R 775 /var/www/html/storage/framework/views \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configure Apache
RUN a2enmod rewrite
COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Create basic .env file (will be overwritten by start.sh)
RUN echo "APP_NAME=\"CRM Module\"" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "APP_URL=https://testing-again-1.onrender.com" >> .env \
    && echo "APP_KEY=base64:$(openssl rand -base64 32)" >> .env \
    && echo "DB_CONNECTION=sqlite" >> .env \
    && echo "DB_DATABASE=database/database.sqlite" >> .env \
    && echo "CACHE_DRIVER=file" >> .env \
    && echo "SESSION_DRIVER=file" >> .env \
    && echo "QUEUE_CONNECTION=sync" >> .env

# Copy and make startup script executable
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Start Apache using our custom script
CMD ["/usr/local/bin/start.sh"]
