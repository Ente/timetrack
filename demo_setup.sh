#!/bin/bash
set -e

echo "TimeTrack Demo Setup starting..."
WORKDIR="$(dirname "$(realpath "$0")")"
cd "$WORKDIR"

echo "Stopping existing demo container..."
docker compose -f docker-compose.yml down -v || true

echo "Building image..."
docker build -t openducks/timetrack .

echo "Starting demo instance..."
docker compose -f docker-compose.yml up -d

echo "Waiting for database to be ready..."
sleep 10


echo "Running migrations..."
docker exec timetrack vendor/bin/phinx migrate -e production

echo "Seeding demo data..."
docker exec timetrack vendor/bin/phinx seed:run -s DemoSeed -e production


echo "✅ Demo instance ready!"
echo "   URL: http://localhost:8080"
echo "   Admin: demo_admin / demo123"
echo "   User : demo_user / demo123"
