#!/bin/bash

# Check if Passport tables exist
if php artisan migrate:status | grep -q "oauth_auth_codes"; then
    echo "Passport tables already exist. Skipping migration..."
else
    echo "Installing Passport tables..."
    php artisan passport:install
fi

# Create a personal access client
echo "Creating personal access client..."
PERSONAL_CLIENT=$(php artisan passport:client --personal --no-interaction)
echo "Personal client output:"
echo "$PERSONAL_CLIENT"

# Extract client ID and secret using sed instead of grep -P
PERSONAL_CLIENT_ID=$(echo "$PERSONAL_CLIENT" | sed -n 's/.*Client ID[. ]*\([0-9][0-9]*\)[. ]*/\1/p' | tr -d ' ')
PERSONAL_CLIENT_SECRET=$(echo "$PERSONAL_CLIENT" | sed -n 's/.*Client secret[. ]*\([a-zA-Z0-9]*\)[. ]*/\1/p' | tr -d ' ')

echo "Extracted personal client ID: $PERSONAL_CLIENT_ID"
echo "Extracted personal client secret: $PERSONAL_CLIENT_SECRET"

# Create password grant client
echo "Creating password grant client..."
PASSWORD_CLIENT=$(php artisan passport:client --password --name="Password Grant Client" --no-interaction)
echo "Password client output:"
echo "$PASSWORD_CLIENT"

# Extract password client ID and secret
PASSWORD_CLIENT_ID=$(echo "$PASSWORD_CLIENT" | sed -n 's/.*Client ID[. ]*\([0-9][0-9]*\)[. ]*/\1/p' | tr -d ' ')
PASSWORD_CLIENT_SECRET=$(echo "$PASSWORD_CLIENT" | sed -n 's/.*Client secret[. ]*\([a-zA-Z0-9]*\)[. ]*/\1/p' | tr -d ' ')

echo "Extracted password client ID: $PASSWORD_CLIENT_ID"
echo "Extracted password client secret: $PASSWORD_CLIENT_SECRET"

# Verify we got all the credentials
if [ -z "$PERSONAL_CLIENT_ID" ] || [ -z "$PERSONAL_CLIENT_SECRET" ] || [ -z "$PASSWORD_CLIENT_ID" ] || [ -z "$PASSWORD_CLIENT_SECRET" ]; then
    echo "Error: Failed to extract all required credentials"
    echo "Debug info:"
    echo "PERSONAL_CLIENT_ID: ${PERSONAL_CLIENT_ID:-not set}"
    echo "PERSONAL_CLIENT_SECRET: ${PERSONAL_CLIENT_SECRET:-not set}"
    echo "PASSWORD_CLIENT_ID: ${PASSWORD_CLIENT_ID:-not set}"
    echo "PASSWORD_CLIENT_SECRET: ${PASSWORD_CLIENT_SECRET:-not set}"
    exit 1
fi

# Update .env file
echo "Updating .env file with credentials..."
sed -i '' "s/PASSPORT_PERSONAL_ACCESS_CLIENT_ID=.*/PASSPORT_PERSONAL_ACCESS_CLIENT_ID=$PERSONAL_CLIENT_ID/" .env
sed -i '' "s/PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=.*/PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=$PERSONAL_CLIENT_SECRET/" .env
sed -i '' "s/PASSPORT_PASSWORD_GRANT_CLIENT_ID=.*/PASSPORT_PASSWORD_GRANT_CLIENT_ID=$PASSWORD_CLIENT_ID/" .env
sed -i '' "s/PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=.*/PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=$PASSWORD_CLIENT_SECRET/" .env

# Verify the credentials were set
echo "Verifying credentials in .env file..."
if grep -q "PASSPORT_PERSONAL_ACCESS_CLIENT_ID=$PERSONAL_CLIENT_ID" .env && \
   grep -q "PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=$PERSONAL_CLIENT_SECRET" .env && \
   grep -q "PASSPORT_PASSWORD_GRANT_CLIENT_ID=$PASSWORD_CLIENT_ID" .env && \
   grep -q "PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=$PASSWORD_CLIENT_SECRET" .env; then
    echo "✅ Passport credentials have been successfully set in .env file"
else
    echo "❌ Error: Failed to verify credentials in .env file"
    exit 1
fi
