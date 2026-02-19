# Migrate Local Data to Railway

Copy your local Akaunting SQLite data to Railway's PostgreSQL.

## Prerequisites

1. **Railway schema** – Complete the web installer on Railway once (Language → Database → Admin) so the schema exists. Or the `akaunting` service has already run migrations.

2. **Public database URL** – Railway's PostgreSQL must be reachable from your machine:
   - Railway → `akaunting-db` → Variables
   - Copy `DATABASE_PUBLIC_URL` (use this, not `DATABASE_URL`)

## Steps

1. Add to your local `.env`:

   ```
   RAILWAY_DATABASE_URL=postgresql://user:password@host:port/database
   ```

   Use the value from `DATABASE_PUBLIC_URL` in Railway.

2. Run the migration:

   ```bash
   php artisan migrate:to-railway
   ```

3. Ensure Railway has `APP_INSTALLED=true` (the deploy script sets this automatically after the first successful install).

4. Visit your Railway URL – you should see your local data.

## Security

- `RAILWAY_DATABASE_URL` contains your database password. Add it only when migrating; remove it from `.env` afterward if you prefer.
