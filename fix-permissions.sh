#!/bin/bash

# Run the permission seeder
php artisan db:seed --class=PermissionSeeder

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild the cache
php artisan optimize

echo "Permissions have been updated and cache has been cleared."
echo "Now run the following command with your email:"
echo "php artisan admin:assign-permissions your.email@example.com" 