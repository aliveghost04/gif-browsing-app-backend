#!/bin/sh

# Run the migrations
php artisan migrate

# Start the web server
php -S 0.0.0.0:80 -t /app/public
