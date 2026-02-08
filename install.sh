#!/usr/bin/env bash
set -e

PROJECT=vehicle-system

composer create-project laravel/laravel $PROJECT
cd $PROJECT

# copy files from kit (parent folder)
KIT_DIR="$(cd .. && pwd)/laravel_upgrade_kit"

rsync -av "$KIT_DIR/app/" app/
rsync -av "$KIT_DIR/routes/" routes/
rsync -av "$KIT_DIR/resources/" resources/
rsync -av "$KIT_DIR/database/" database/
rsync -av "$KIT_DIR/public/" public/

# env
cp .env.example .env
php artisan key:generate

# migrate + seed
php artisan migrate --seed

echo "Done. Run: cd $PROJECT && php artisan serve"
