#!/bin/bash

# Set APP_URL to the correct HTTPS URL
export APP_URL=https://testing-again-1.onrender.com

# Ensure proper permissions
chmod 755 storage
chmod 755 storage/logs
chmod 755 storage/framework
chmod 755 storage/framework/cache
chmod 755 storage/framework/sessions
chmod 755 storage/framework/views
chmod 755 bootstrap/cache

# Ensure SQLite database exists and is writable
mkdir -p database
touch database/database.sqlite
chmod 666 database/database.sqlite

# Start Apache
apache2-foreground
