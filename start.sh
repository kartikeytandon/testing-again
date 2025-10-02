#!/bin/bash

# Set APP_URL to the correct HTTPS URL
export APP_URL=https://testing-again-1.onrender.com

# Clear all caches to ensure fresh configuration
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configuration with correct APP_URL
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
