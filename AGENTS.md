Before work in this repository, read `../AGENTS.md` for the shared organization and shell rules. If this repository is checked out alone, report that the shared file is unavailable. Hosting, deploy, backup, and monitoring facts are in `../kaflabs/DEPLOY-PLAN.md`; do not repeat them here.

## Architecture

**CornerTill** (https://cornertill.com) is a point of sale web app for small shops: inventory, checkout, bills, and a dashboard. Laravel 12 on the server, React 18 with TypeScript in the browser, and Inertia between them (no SSR). Styles: Tailwind CSS 3 and Material Tailwind.

- **Laravel 10 structure.** `bootstrap/app.php` binds `App\Http\Kernel` (middleware), `App\Console\Kernel` (the schedule is in its `schedule()` method, not in `routes/console.php`), and `App\Exceptions\Handler`. Commands in `app/Console/Commands/` load automatically.
- **Pages.** `routes/web.php` renders the Inertia pages of `resources/js/Pages/`: `Guest/` (home page), `Anyone/` (privacy and terms), `Authenticating/` (login and registration), and `Authenticated/` (the app). `resources/views/app.blade.php` is the only HTML template for all pages.
- **Inertia** packages are pinned to 1.0.15 (`@inertiajs/core` and `@inertiajs/react` must have the same version). The browser calls the global `route()` function of Ziggy (`@routes` in `app.blade.php`).
- **Launch switches.** `config/features.php` (README, "Launch switches"). `PAYMENTS_ENABLED=false` is the free beta. The legal pages must change before a switch goes on, and legal text waits for Ahmed.
- **Sentry.** Server and browser reports are off without a DSN. `App\Support\SentryScrubber` and `resources/js/sentry.ts` keep personal data out (privacy policy, section 7). A change to what is sent is a change to data collection, and it waits for Ahmed.
- **Container.** `Dockerfile` and `docker/` (nginx, PHP-FPM, supervisord, `entrypoint.sh`). The entrypoint caches the config, runs the migrations (roles `all` and `web`), and starts the processes of `CONTAINER_ROLE`. README, "Deploy on Coolify", has the environment variables.

## Languages

English and Arabic (right to left). README, "Language", has the rules for the layout classes.

- Browser text: `t("English text")` from `react-i18next`. The English text is the key. Add each new key with its Arabic text to `public/locales/ar/translation.json`. `public/locales/en/translation.json` stays empty.
- Server text (validation, flash messages, emails, error pages): `__('English text')`. The Arabic text is in `lang/ar.json` and `lang/ar/*.php`.

## Branches

- Work on a branch from `dev` and open the pull request to `dev`. Merge pull requests with `--merge` (merge commits), not `--squash`.
- The live site deploys from `production`. Only Ahmed merges `dev` to `production`.

## Checks

- `npm run build` (`tsc`, then `vite build`). There is no lint script.
- `php artisan test` (PHPUnit, `tests/Feature/`). The devbox has no PHP: run the tests in Docker with the `php-base` stage of the `Dockerfile` (PHP 8.2, the same as CI):

  ```shell
  docker build --target php-base -t cornertill-php-base .
  docker run --rm --user "$(id -u):$(id -g)" -v "$PWD":/var/www/html -e COMPOSER_HOME=/tmp/composer cornertill-php-base composer install --no-interaction
  docker run --rm --user "$(id -u):$(id -g)" -v "$PWD":/var/www/html -v "$PWD/.env.example":/var/www/html/.env:ro \
    -e APP_KEY="base64:$(head -c 32 /dev/urandom | base64)" -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: \
    cornertill-php-base php artisan test
  ```

- GitHub Actions (`.github/workflows/`) runs the build and the PHP tests on each pull request to `dev`, `staging`, and `production`.
- For a visible change, check the page in English and in Arabic, at desktop and phone width.
