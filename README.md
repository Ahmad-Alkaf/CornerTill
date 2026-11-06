# Laptop Point of Sale

This is a simple Point of Sale (POS) application.

# Main Pages 

### Inventory Page

![Inventory page](public/assets/screenshots/2023-11-26%2009_43_52-Inventory - Laptop POS%20-%20Brave.png)

### Checkout Page

![Checkout page](public/assets/screenshots/2023-11-26%2010_05_34-Checkout - Laptop POS%20-%20Brave.png)

### Dashboard Page

![Dashboard page](public/assets/screenshots/2023-11-26%2010_43_36-Dashboard - Laptop POS%20-%20Brave.png)


# Workflow

- All commits should be pushed to the `dev` branch.
- When the `dev` branch becomes stable it will be merged to the `staging` branch.
- So that the `staging` branch can be deployed and viewed by the client.
- After that, if the client approves the staging, then it will be merged to `production`.
- The `production` branch is the production environment on the server.


# Development Environment

Clone the repo to your local computer:
```shell
git clone https://github.com/Ahmad-Alkaf/Laptop-POS
```
Navigate to the cloned project folder:
```shell
cd laptop-pos
```
Install the dependencies:
```shell
composer install
```
copy `.env.example` file and rename the copy to `.env`. This file is not in the repo because it is sensitive:
```shell
cp .env.example .env
```

Configure the database information in the `.env` file (`DB_*`).
```js
DB_DATABASE=yourdatabase_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Sets the `APP_KEY` value in your `.env` file:
```shell
php artisan key:generate
```

Create the `database/migrations` schema:
```shell
php artisan migrate
//OR to drop all existing tables
php artisan migrate:fresh
```

Seed the database with fake data. 
Note: there will be a user with email of `asdf@asdf.asdf` and password of `asdfasdf`, you can login directly with it. 
```bash
php artisan db:seed
```

Generate a link folder (shortcut) on the public directory to serve the client with files that located on a private directory.
```bash
php artisan storage:link
```

Install all dependencies:
```shell
npm install
```

Run Vite, to hot module reloading (HMR):
```shell
npm run dev
```

In a separate terminal run the Laravel app:
```shell
php artisan serve
```

# Powered by

- **Laravel** as a backend.
- **Inertia** as a glow between the backend and frontend.
- **React** as a frontend (Typescript).
- **Tailwind** as a salt and black pepper for React.
- **Breeze** to create a starting scaffold of the above (Welcome, Login, Register, ...etc) pages.
- **React Icons** to use icons from different libraries in React.

# Launch switches

`config/features.php` reads these variables. They hide features; they do not remove code.

| Variable | Default | Effect |
|---|---|---|
| `PAYMENTS_ENABLED` | `false` | `false`: free beta. Paddle plans, pricing, subscription routes, the payment policy page, the Paddle webhook, and Paddle.js are off (routes return 404). The pages show "Free during beta". Every business uses the beta limits. `true`: the Paddle subscription flow works as before. |
| `BETA_MAX_ACCOUNTS`, `BETA_MAX_PRODUCTS`, `BETA_MAX_BILLS_PER_MONTH` | `2`, `500`, `15000` | Beta limits for each business (same as the Basic plan). |
| `SOCIAL_GOOGLE_ENABLED` | `true` | Google login button and `/auth/google/*` routes. |
| `SOCIAL_GITHUB_ENABLED` | `true` | GitHub login button and `/auth/github/*` routes. |
| `SOCIAL_X_ENABLED` | `false` | X login button and `/auth/x/*` routes. |

A disabled provider hides its button, and its routes return 404.

# Language

The app detects the browser language. Arabic browsers get Arabic; all other browsers get English (the default). The EN/AR switch in the guest header and the app header changes the language. The selected language is kept in `localStorage` (`i18nextLng`) and a cookie (`i18next`). Pages set `dir` (rtl for Arabic) on each section; `<html dir>` stays `ltr`. Translations are in `public/locales/<lang>/translation.json`.

# Deploy on Coolify

Build pack: **Dockerfile** (the repository `Dockerfile`). The image builds the Composer and Vite assets. No build-time variable is necessary.

- **Port:** `8080`.
- **Health check:** `GET /api/health`. It returns HTTP 200 `{"status":"ok","database":"ok"}` when Laravel runs and MySQL answers, and HTTP 503 when MySQL fails. The route has no session and no auth. The database check waits at most about 2 × `DB_HEALTH_TIMEOUT` (default 2 seconds); when the MySQL host name does not resolve, the DNS lookup can add about 10 seconds. In Coolify, set the health check path to `/api/health` and the port to `8080`.
- **Start:** the container caches config, routes, views, and events, then runs `php artisan migrate --force` (it tries again for about 60 seconds while MySQL starts). Set `RUN_MIGRATIONS=false` to skip it.
- **Persistent storage:** add a Coolify volume mount at `/var/www/html/storage`. It holds the uploads (`storage/app/public/products-images` and `storage/app/public/businesses-logo`), sessions, and the framework cache. The image already links `public/products-images` and `public/businesses-logo` to these folders (the same result as `php artisan storage:link`). Use a volume, not a root-owned bind mount: the app runs as `www-data`. For S3-compatible storage, set `FILESYSTEM_DISK=s3` and the `AWS_*` values instead.
- **Queue worker and scheduler:** with the default `CONTAINER_ROLE=all`, supervisord runs nginx, PHP-FPM, `php artisan queue:work`, and `php artisan schedule:work` (it runs `schedule:run` every minute) in the same container. To run them as separate Coolify services, use the same image with `CONTAINER_ROLE=web` for the app, `CONTAINER_ROLE=worker`, and `CONTAINER_ROLE=scheduler`.

Required environment variables:

| Variable | Value |
|---|---|
| `APP_KEY` | Output of `php artisan key:generate --show`. The container does not start without it. |
| `APP_URL` | `https://laptop-pos.com` |
| `APP_ENV`, `APP_DEBUG` | `production`, `false` (image defaults) |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | The dedicated MySQL database |
| `QUEUE_CONNECTION` | `database` (image default; the `jobs` table comes from the migrations) |
| `FILESYSTEM_DISK` | `local` (image default; uploads on the volume) or `s3`. Do not leave it empty: `config/filesystems.php` then selects the S3 disks. |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION` | SMTP account |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | `noreply@kaflabs.com`, `Laptop POS` |
| `ADMIN_REPORT_EMAIL` | Receiver of the daily database table counts email (scheduled task). Empty: no email; the counts go only to the log. |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` | OAuth apps, or turn the provider off with its `SOCIAL_*_ENABLED` switch |
| `PAYMENTS_ENABLED` | `false` for the free beta |

Logs go to the container output (`LOG_CHANNEL=stderr`).

Local test of the image:

```shell
docker build -t laptop-pos .
docker run --rm -p 8080:8080 -e APP_KEY=base64:... -e DB_HOST=... -e DB_DATABASE=... \
  -e DB_USERNAME=... -e DB_PASSWORD=... -v laptop-pos-storage:/var/www/html/storage laptop-pos
curl -i http://localhost:8080/api/health
```

# License

The source is public under [PolyForm Noncommercial 1.0.0](LICENSE). See the license for permitted uses and conditions.
