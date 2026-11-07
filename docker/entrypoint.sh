#!/bin/sh
# CornerTill container start. See the Dockerfile header for CONTAINER_ROLE.
set -eu
cd /var/www/html

# A command was given (for example `php artisan tinker`): run only that.
if [ "$#" -gt 0 ]; then
  exec "$@"
fi

role="${CONTAINER_ROLE:-all}"
case "$role" in
  all|web|worker|scheduler) ;;
  *) echo "Unknown CONTAINER_ROLE '$role'. Use all, web, worker, or scheduler." >&2; exit 1 ;;
esac

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is not set. Make one with: php artisan key:generate --show" >&2
  exit 1
fi

# The volume at /var/www/html/storage must be writable by www-data.
if [ ! -w storage ]; then
  echo "storage/ is not writable by $(id -un). Use a Coolify volume mount (not a bind mount owned by root)." >&2
  exit 1
fi
# An empty volume needs the Laravel folders.
mkdir -p storage/app/public/products-images storage/app/public/businesses-logo \
  storage/framework/cache/data storage/framework/sessions storage/framework/views \
  storage/logs

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

if [ "$role" = "all" ] || [ "$role" = "web" ]; then
  if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    tries=0
    until php artisan migrate --force --no-interaction; do
      tries=$((tries + 1))
      if [ "$tries" -ge "${MIGRATE_TRIES:-30}" ]; then
        echo "Migrations failed $tries times. Stop." >&2
        exit 1
      fi
      echo "Migrations failed (is MySQL ready?). Try again in 2 seconds ($tries)." >&2
      sleep 2
    done
  fi
fi

case "$role" in
  all)
    export RUN_QUEUE_WORKER=true RUN_SCHEDULER=true
    exec supervisord -c /etc/supervisord.conf
    ;;
  web)
    export RUN_QUEUE_WORKER=false RUN_SCHEDULER=false
    exec supervisord -c /etc/supervisord.conf
    ;;
  worker)
    exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600
    ;;
  scheduler)
    exec php artisan schedule:work
    ;;
esac
