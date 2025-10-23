#!/bin/bash
set -e

# Waiting for the database to be ready
echo "Waiting for database to be ready..."
sleep 10

# Run Phinx migrations
echo "Running database migrations..."
if [ -f /var/www/html/vendor/bin/phinx ]; then
  php /var/www/html/vendor/bin/phinx migrate || true
else
  echo "Phinx not found — skipping migration."
fi

# Start PC/SC daemon for smartcard/NFC
echo "Starting pcscd..."
pcscd --daemon &


# Start Apache
echo "Starting Apache..."
exec apache2-foreground
