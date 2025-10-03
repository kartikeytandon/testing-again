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

# Create complete .env file with SQLite configuration
RUN echo "APP_NAME=\"CRM Module\"" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "APP_URL=https://testing-again-1.onrender.com" >> .env \
    && echo "APP_KEY=base64:$(openssl rand -base64 32)" >> .env \
    && echo "DB_CONNECTION=sqlite" >> .env \
    && echo "DB_DATABASE=database/database.sqlite" >> .env \
    && echo "CACHE_DRIVER=file" >> .env \
    && echo "SESSION_DRIVER=file" >> .env \
    && echo "QUEUE_CONNECTION=sync" >> .env \
    && echo "MAIL_MAILER=smtp" >> .env \
    && echo "MAIL_HOST=localhost" >> .env \
    && echo "MAIL_PORT=1025" >> .env \
    && echo "MAIL_USERNAME=null" >> .env \
    && echo "MAIL_PASSWORD=null" >> .env \
    && echo "MAIL_ENCRYPTION=null" >> .env \
    && echo "MAIL_FROM_ADDRESS=hello@example.com" >> .env \
    && echo "MAIL_FROM_NAME=\"CRM Module\"" >> .env

# Create SQLite database file
RUN mkdir -p database && touch database/database.sqlite && chmod 666 database/database.sqlite

# Clear all caches and run migrations
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan cache:clear \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Copy and make startup script executable
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Start Apache using our custom script
CMD ["/usr/local/bin/start.sh"]
