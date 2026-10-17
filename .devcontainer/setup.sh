#!/usr/bin/env bash
# Перше налаштування Codespace: розширення PHP, залежності, .env, база з демо-даними, збірка фронтенду.
# Безпечно запускати повторно: наявні .env і база не перезаписуються.
set -euo pipefail

echo "▶ PHP-розширення intl, zip"
sudo apt-get update -qq
sudo apt-get install -y -qq libicu-dev libzip-dev sqlite3 > /dev/null
sudo docker-php-ext-install -j"$(nproc)" intl zip > /dev/null

echo "▶ Composer і npm"
composer install --no-interaction --prefer-dist
npm install --no-audit --no-fund

if [ ! -f .env ]; then
    echo "▶ .env з .env.example"
    cp .env.example .env
    sed -i 's/^APP_ENV=.*/APP_ENV=local/; s/^APP_DEBUG=.*/APP_DEBUG=true/; s/^APP_LOCALE=.*/APP_LOCALE=uk/' .env
fi
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

if [ ! -f database/database.sqlite ]; then
    echo "▶ База SQLite з демо-даними"
    touch database/database.sqlite
    php artisan migrate --seed --force
else
    php artisan migrate --force
fi

php artisan storage:link || true
npm run build
rm -f public/hot
php artisan optimize:clear

echo
echo "✅ Готово. Запуск сайту:  php artisan serve --port=8000"
echo "   Адміністратор:         php artisan app:create-admin ваш@email.com --password=пароль"
