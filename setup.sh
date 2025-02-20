#!/bin/bash
set -e

setOrUpdateEnvVariable() {
    local key=$1
    local value=$2

    if grep -qE "^${key}=" ".env"; then
        # Update existing variable (the -i option does in-place editing)
        sed -i "s/^${key}=.*/${key}=${value}/" ".env"
    else
        # Append the variable at the end of the file
        echo "${key}=${value}" >> ".env"
    fi
}

required_commands=( "docker-compose" "docker" "php" )

# Generate random values for the Reverb app key, secret, and ID
REVERB_APP_KEY=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 20)
REVERB_APP_SECRET=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 20)
REVERB_APP_ID=$(shuf -i 100000-999999 -n 1)

# Create the .env file if it doesn't exist
if [ ! -f ".env" ]; then
    cp ".env.example" ".env"
fi

# Set or update the Reverb app key, secret, and ID in the .env file
setOrUpdateEnvVariable "REVERB_APP_KEY"     "$REVERB_APP_KEY"    && echo "REVERB_APP_KEY: $REVERB_APP_KEY"
setOrUpdateEnvVariable "REVERB_APP_SECRET"  "$REVERB_APP_SECRET" && echo "REVERB_APP_SECRET: $REVERB_APP_SECRET"
setOrUpdateEnvVariable "REVERB_APP_ID"      "$REVERB_APP_ID"     && echo "REVERB_APP_ID: $REVERB_APP_ID"

# Check each required command
for cmd in "${required_commands[@]}"; do
    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo "Error: $cmd is not installed. Please install $cmd and try again."
        exit 1
    fi
done

# Generate new application key
php artisan --no-interaction --force --env=production key:generate

# Start the Docker container
docker-compose --env-file .env up -d
