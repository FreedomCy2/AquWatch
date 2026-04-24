# AquWatch VPS Handover Checklist

This checklist is for deploying the existing local code to VPS safely.

## 1) What does NOT need to be redone

- Feature code already built locally (alerts, plans, AI chat, dual-flow support) does not need to be rebuilt on VPS.
- You only need to deploy the same code and set production environment values.

## 2) VPS prerequisites

- PHP 8.2+ with required extensions for Laravel.
- Composer installed.
- MySQL or MariaDB database created.
- Web server (Nginx or Apache) pointing to `public/`.
- HTTPS enabled (recommended before putting sensor endpoints online).

## 3) Deploy code to VPS

Use your normal git flow (clone/pull) to place this project on VPS.

Then run in project root:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
```

If frontend assets are used in production:

```bash
npm ci
npm run build
```

## 4) Production `.env` values

Create production `.env` from `.env.example` and set at least:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain`
- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SENSOR_INGEST_TOKEN=<strong-random-token>`
- `SENSOR_ACCEPT_LEGACY_TOKEN=true` (set to `false` only after migrating all sensors to per-sensor token flow)
- Optional AI config:
  - `AI_PROVIDER=openai`
  - `OPENAI_API_KEY=...`
  - `OPENAI_MODEL=gpt-4o-mini`

## 5) Database and app bootstrap

Run once after deploy:

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Security and retention now implemented in code:

- Sensor ingest API is rate-limited (`throttle:sensor-ingest`).
- Old sensor readings are pruned daily by scheduled command `sensors:prune-readings`.

## 6) Queue and scheduler

If queue/scheduler are used, ensure they run under process manager (systemd/supervisor) and cron.

Minimum scheduler cron entry:

```bash
* * * * * cd /path/to/AquWatch && php artisan schedule:run >> /dev/null 2>&1
```

Optional one-off manual prune check:

```bash
php artisan sensors:prune-readings --days=30
```

## 7) ESP32 switch to VPS endpoint

Update API URLs in these files before uploading sketches:

- `docs/esp32-flow-http.ino`
- `docs/esp32-rain-http.ino`
- `docs/esp32-flood-http.ino`

Change `API_URL` to your public VPS domain, for example:

- `https://your-domain/api/ingest/flow`
- `https://your-domain/api/ingest/rain`
- `https://your-domain/api/ingest/flood`

Use the same `SENSOR_INGEST_TOKEN` value configured in VPS `.env`.

## 8) Smoke test after deploy

- Open app login and dashboard.
- Verify latest flow/rain/flood cards update.
- Check notifications page and history page.
- Check active sensor count behaves correctly with dual-flow sensors.
- Confirm ESP32 serial monitor shows HTTP status 200.

## 9) Safe rollback plan

- Keep previous release directory or previous git tag.
- If deploy fails, switch web root back to previous release.
- Restore previous `.env` and DB snapshot if schema changes caused issues.

## 10) Quick reality check

Local setup is still useful for development.
Production shared access always needs online hosting (VPS or managed platform) plus one central production database.
