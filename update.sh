#!/bin/bash

log() {
    echo -e "[\e[36mUPDATE\e[0m] $1"
}

abort() {
    echo -e "[\e[31mERROR\e[0m] $1"
    exit 1
}

require_root() {
    if [[ $EUID -ne 0 ]]; then
        abort "Please run this script as root or via sudo"
    fi
}

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

log "Directory: $SCRIPT_DIR"

if [[ ${1:-} == "--sudo" ]]; then
    require_root
fi

log "Pulling latest changes..."
git fetch --all
git reset --hard origin/main || git pull || abort "Git update failed!"
log "Git update done."

log "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
log "Composer done."

log "Running database migrations..."
"$SCRIPT_DIR/vendor/bin/phinx" migrate -e production || abort "Migrations failed"
log "Migrations done."

log "Updating folder permissions..."

sudo chown -R www-data:www-data "$SCRIPT_DIR/data" || abort "Failed to set owner for /data"
sudo chown -R www-data:www-data "$SCRIPT_DIR/api/v1/class/plugins/plugins"
sudo chown www-data:www-data "$SCRIPT_DIR/api/v1/toil/permissions.json"
sudo chmod -R www-data:www-data "$SCRIPT_DIR" || abort "Failed to set permissions for TimeTrack root directory"

log "Permissions updated."
log "Update done successfully"
