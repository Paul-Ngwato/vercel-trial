# Deploying to Vercel

This repo includes `vercel.json` and `api/index.php`, which run the Laravel app on Vercel through the
community `vercel-php` runtime (`vercel-php@0.9.0` = PHP 8.5; your `composer.lock` needs PHP 8.4+).

Vercel's filesystem is read-only and has no database, so three things must live elsewhere:
the database, uploaded photos, and the built CSS/JS. Do these once.

## 1. Database (SQLite does not work on Vercel)

Create a free hosted database (e.g. Neon Postgres, or TiDB / Aiven MySQL), then run the migrations
**from your computer**, pointing at it:

```bash
DB_CONNECTION=pgsql DB_HOST=... DB_PORT=5432 DB_DATABASE=... DB_USERNAME=... DB_PASSWORD=... DB_SSLMODE=require \
  php artisan migrate --force
```

- `php artisan db:seed` creates the demo wedding and an admin `admin@wedding.com` / `password`.
  Change that password in `database/seeders/WeddingSeeder.php` **before** seeding a public site.
- If you already have real content in a local SQLite file, import that instead of seeding.

## 2. Uploaded photos (object storage)

Install the S3 driver and commit the updated `composer.json` and `composer.lock`:

```bash
composer require league/flysystem-aws-s3-v3
```

Create a public bucket (Cloudflare R2, Supabase Storage, AWS S3, ...). Allow `GET` from your site's
domain in the bucket's CORS settings (the photo cropper loads images from it). Photo links on the site keep
working: `/storage/...` redirects to the bucket.

## 3. Build the CSS/JS and commit it

The Vercel build step is skipped (`vercel.json`), so the compiled assets must be in the repo:

```bash
npm install
npm run build
git add public/build
```

Re-run this and commit whenever you change CSS, JS or Tailwind classes in the templates.

## 4. Vercel project settings

Application Preset: **Other**. Leave Build/Output/Install commands as they are (`vercel.json` sets them).

Environment variables:

| Variable | Value |
| --- | --- |
| `APP_KEY` | output of `php artisan key:generate --show` |
| `APP_URL` | `https://your-project.vercel.app` (or your custom domain) |
| `APP_NAME` | your site name |
| `DB_CONNECTION` | `pgsql` or `mysql` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | from your database provider |
| `DB_SSLMODE` | `require` (Postgres) |
| `MYSQL_ATTR_SSL_CA` | `/etc/pki/tls/certs/ca-bundle.crt` (MySQL providers that require TLS) |
| `PUBLIC_DISK` | `s3` |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET` | from your storage provider |
| `AWS_DEFAULT_REGION` | your region (`auto` for R2) |
| `AWS_URL` | the bucket's public base URL |
| `AWS_ENDPOINT` | the S3 endpoint (R2 / Supabase; leave empty for AWS S3) |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` for R2 / Supabase |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | SMTP details; without these, admin notification emails are only logged |

`api/index.php` already defaults `APP_ENV=production`, `APP_DEBUG=false`, `LOG_CHANNEL=stderr`,
`SESSION_DRIVER=cookie`, `CACHE_STORE=array` and `QUEUE_CONNECTION=sync`. Anything you set in the dashboard wins.

## 5. Check it

Open `/up` (should return OK), then `/`, `/gallery` and `/admin/login`. Errors show up in
Vercel → your project → Logs.

## Limits to know about

- Vercel rejects request bodies over about 4.5 MB, so a single photo larger than that fails to upload
  (guest gallery and admin). Phone photos are often bigger. If that matters, a host that runs PHP normally
  (Render, Railway, Fly.io, cPanel) avoids it.
- Cold starts add about a second to the first request after idle time.
