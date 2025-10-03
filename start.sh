#!/bin/bash

# Set APP_URL to the correct HTTPS URL
export APP_URL=https://testing-again-1.onrender.com

# Remove any existing .env file
rm -f .env

# Create fresh .env file with correct settings
cat > .env << EOF
APP_NAME="CRM Module"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://testing-again-1.onrender.com
APP_KEY=base64:$(openssl rand -base64 32)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="CRM Module"
EOF

# Set proper permissions
chmod 755 storage
chmod 755 storage/logs
chmod 755 storage/framework
chmod 755 storage/framework/cache
chmod 755 storage/framework/sessions
chmod 755 storage/framework/views
chmod 755 bootstrap/cache

# Create SQLite database file
mkdir -p database
touch database/database.sqlite
chmod 666 database/database.sqlite

# Clear ALL caches and remove cached files
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Remove cached configuration files
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

# Run migrations to create tables
php artisan migrate --force

# Cache configuration with correct settings
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
