#!/bin/bash

# Set APP_URL to the correct HTTPS URL
export APP_URL=https://testing-again-1.onrender.com

# Update .env file with correct settings
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

# Create SQLite database file
mkdir -p database
touch database/database.sqlite

# Clear all caches to ensure fresh configuration
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations to create tables
php artisan migrate --force

# Cache configuration with correct settings
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
