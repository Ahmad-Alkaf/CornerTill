# Laptop POS - Laravel 10 image for Coolify.
#
# One image, three roles (CONTAINER_ROLE):
#   all (default) - nginx + PHP-FPM + queue worker + scheduler under supervisord.
#   web           - nginx + PHP-FPM only (use with separate worker/scheduler containers).
#   worker        - php artisan queue:work only.
#   scheduler     - php artisan schedule:work only.
#
# On start the container caches config, routes, views, and events, and
# (roles all and web) runs `php artisan migrate --force`.
#
# The app listens on port 8080 as the www-data user. Health check: /api/health.
# Persistent data (uploads, sessions, logs): mount a volume at
# /var/www/html/storage. See README.md "Deploy on Coolify".
#
# All configuration comes from runtime environment variables (APP_KEY,
# DB_*, MAIL_*, ...). No value is needed at build time.

# ---------- php-base ----------
FROM php:8.2-fpm-alpine AS php-base
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions bcmath exif gd intl pcntl pdo_mysql zip \
    && apk add --no-cache nginx supervisor
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html

# ---------- vendor ----------
FROM php-base AS vendor
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist \
    --no-scripts --no-autoloader

# ---------- assets ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY tsconfig.json vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
# `npm run build` = tsc && vite build. Output: public/build.
RUN npm run build

# ---------- runner ----------
FROM php-base AS runner
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    FILESYSTEM_DISK=local \
    QUEUE_CONNECTION=database \
    CONTAINER_ROLE=all

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-laptop-pos.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-laptop-pos.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

COPY --chown=www-data:www-data --from=vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

# Optimized autoloader; its post-autoload-dump script runs package:discover.
# No .env and no database are needed.
# Uploads live in storage/app/public/{products-images,businesses-logo};
# the public/ links point there (same as `php artisan storage:link`).
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-interaction \
    && rm -f /usr/local/bin/composer \
    && rm -rf public/products-images public/businesses-logo bootstrap/cache/*.php \
    && ln -s /var/www/html/storage/app/public/products-images public/products-images \
    && ln -s /var/www/html/storage/app/public/businesses-logo public/businesses-logo \
    && mkdir -p storage/app/public/products-images storage/app/public/businesses-logo \
       storage/framework/cache/data storage/framework/sessions storage/framework/views \
       storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public

USER www-data
EXPOSE 8080
# Only the all and web roles serve HTTP; worker and scheduler report healthy.
HEALTHCHECK --interval=30s --timeout=5s --start-period=90s --retries=3 \
  CMD [ "$CONTAINER_ROLE" != "all" ] && [ "$CONTAINER_ROLE" != "web" ] \
      || wget -qO- http://127.0.0.1:8080/api/health > /dev/null || exit 1
ENTRYPOINT ["docker-entrypoint"]
CMD []
