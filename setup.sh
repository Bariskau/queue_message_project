#!/bin/bash

# Exit on error
set -e

echo "1. Setting up environment variables..."
cp .env.example .env

echo "2. Starting Docker containers..."
docker-compose up -d

echo "3. Waiting for containers to be ready..."
sleep 10

echo "4. Installing dependencies..."
docker-compose exec -T app composer install

echo "5. Generating application key..."
docker-compose exec -T app php artisan key:generate

echo "6. Running database migrations..."
docker-compose exec -T app php artisan migrate --seed

echo "Setup completed successfully!"

# Add error handling
if [ $? -eq 0 ]; then
    echo "✅ Installation completed successfully!"
    echo "You can now access your application"
else
    echo "❌ An error occurred during installation"
    exit 1
fi
