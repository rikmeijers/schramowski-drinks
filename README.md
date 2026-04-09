# Schramowski Getränke – Drink Reservations

Internal Laravel app to manage **drink reservations**.

> Laravel lives in `secure/`, but the **web root is `public/`**. `public/index.php` bootstraps the app from `secure/`.

---

## Repository structure

- `public/` – **web root** (entrypoint `public/index.php`) + static assets (`public/assets/*`)
- `secure/` – Laravel application (routes/controllers/views/migrations, `artisan`, `vendor/`, storage)
- `sql/` – optional database dump (`schramowski-getranke.sql`)

---

## Requirements

- PHP **>= 8.4**
- Composer
- MySQL / MariaDB
- Web server (Apache/Nginx) with document root set to `public/`
- Node.js/npm: **optional** (there is no build pipeline; `package.json` is mainly for dependency tracking)

---

## Local setup

### 1) Backend dependencies

```bash
cd secure
composer install
```

### 2) Environment

```bash
cd secure
cp .env.example .env
php artisan key:generate
```

### 3) Database

Create a database (e.g. `schramowski-getranke`) and configure your `DB_*` values in `secure/.env`.

Then run:

```bash
cd secure
php artisan migrate
```

> Default drivers use **database** for sessions/cache/queue (`SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`). Make sure migrations run successfully.

---

## Running locally

### Option A (recommended): web server with docroot `public/`

- Point your vhost/document root to:
  - `{repo}/public`
- Ensure `secure/storage` and `secure/bootstrap/cache` are writable.

### Option B: quick dev server

You can also run Laravel from `secure/`:

```bash
cd secure
php artisan serve
```

Note: this repository has a separate top-level `public/` folder. In production (and usually locally) the most “realistic” setup is to point your web server directly at `public/`.

---

## Configuration (.env)

Key environment variables (see `secure/.env.example`):

### App

- `APP_NAME`
- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `APP_KEY`
- `APP_LOCALE` / `APP_FALLBACK_LOCALE`
- `APP_TIMEZONE` (default: `Europe/Berlin`)
- `APP_VERSION` (used in the UI/footer)

### Database

- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

### Session / Cache / Queue

- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`

### Mail (SMTP)

- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
- `MAIL_REPLY_TO_ADDRESS`, `MAIL_REPLY_TO_NAME`

---

## Front-end / assets

Static assets are served directly from `public/assets/`:

- Global JS: `public/assets/js/global.js`
- Flatpickr (site-wide init): `public/assets/js/flatpickr-global.js`
- Reservation form logic: `public/assets/js/reservations-form.js`

### Flatpickr via CDN

Flatpickr is loaded via CDN in the shared layout (`secure/resources/views/shared/layout.blade.php`):

- CSS: `https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css`
- JS: `https://cdn.jsdelivr.net/npm/flatpickr`

The scripts expect `window.flatpickr` to be available **before** `flatpickr-global.js` and `reservations-form.js`.

---

## Important routes

- `/` → redirects to login or dashboard
- `/dashboard` → dashboard
- `/trailers` → trailer overview
- `/reservations/create` → create reservation
- `/reservations/{id}/edit` → edit reservation
- `/trailers/{trailerId}/blocked-dates` → JSON endpoint for blocked dates per trailer

---

## Roles & access

- **employee**
  - Dashboard, Trailers, Reservations, Print overviews
- **admin**
  - Everything above + user management

Public registration is disabled; admins add users via the admin routes.

---

## Maintenance

If changes are not visible (cached config/views):

```bash
cd secure
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

Logs:
- `secure/storage/logs/`

---

## Troubleshooting

### ENV values appear missing in Blade

In Blade, prefer:
- `config('app.name')`

Instead of:
- `env('APP_NAME')`

Then clear config cache:

```bash
cd secure
php artisan config:clear
```

---

## License

Internal use. See [LICENSE](LICENSE).
