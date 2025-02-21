#!/bin/bash

# Install Passport
php artisan passport:install

# Get the client IDs and secrets
PERSONAL_CLIENT_ID=$(php artisan passport:client --personal --no-interaction --quiet | grep -oP 'Client ID: \K[0-9]+')
PERSONAL_CLIENT_SECRET=$(php artisan passport:client --personal --no-interaction --quiet | grep -oP 'Client secret: \K[^\n]+')

PASSWORD_CLIENT_ID=$(php artisan passport:client --password --name="Password Grant Client" --no-interaction --quiet | grep -oP 'Client ID: \K[0-9]+')
PASSWORD_CLIENT_SECRET=$(php artisan passport:client --password --name="Password Grant Client" --no-interaction --quiet | grep -oP 'Client secret: \K[^\n]+')

# Update .env file
sed -i '' "s/PASSPORT_PERSONAL_ACCESS_CLIENT_ID=.*/PASSPORT_PERSONAL_ACCESS_CLIENT_ID=$PERSONAL_CLIENT_ID/" .env
sed -i '' "s/PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=.*/PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=$PERSONAL_CLIENT_SECRET/" .env
sed -i '' "s/PASSPORT_PASSWORD_GRANT_CLIENT_ID=.*/PASSPORT_PASSWORD_GRANT_CLIENT_ID=$PASSWORD_CLIENT_ID/" .env
sed -i '' "s/PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=.*/PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=$PASSWORD_CLIENT_SECRET/" .env

echo "Passport credentials have been set in .env file"
