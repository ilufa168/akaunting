# Deploy Akaunting to Railway

Deploy to Railway using your free trial. Railway supports Docker and PostgreSQL.

## Step 1: Create a New Project

1. Go to [railway.app](https://railway.app) and sign in.
2. Click **New Project**.
3. Select **Deploy from GitHub repo**.
4. Connect and choose your Akaunting repo.

## Step 2: Add PostgreSQL

1. In your project, click **+ New**.
2. Select **Database** → **PostgreSQL**.
3. Rename it to `akaunting-db` (or note the name for the variable reference).

## Step 3: Configure the Web Service

1. Click on your **web service** (the `akaunting` app).
2. Go to **Variables** → **RAW Editor** and paste the contents of `railway.env.txt`.
3. Edit `ADMIN_EMAIL` and `ADMIN_PASSWORD` to your chosen login credentials.
4. If your database service isn't named `akaunting-db`, change `${{akaunting-db.DATABASE_URL}}` to `${{YOUR_DB_SERVICE_NAME.DATABASE_URL}}`.
5. Go to **Settings** → **Networking** → **Generate Domain** (APP_URL is auto-detected from `RAILWAY_PUBLIC_DOMAIN`).

## Step 4: Deploy

1. Railway builds from the Dockerfile and deploys.
2. On first deploy, the startup script runs `php artisan install` and creates the admin user.
3. Log in at your Railway URL with `ADMIN_EMAIL` and `ADMIN_PASSWORD`.

**Note:** Ensure the Postgres database is created before the web service deploys. Railway handles this when both are in the same project.

## Optional: Sample Data

To seed demo data, use **Railway CLI** or the **Shell** in the dashboard:

```bash
php artisan sample-data:seed
```

## Troubleshooting

- **Build fails:** Ensure `node_modules` and `vendor` are in `.dockerignore` (they are).
- **Database connection:** Verify `DATABASE_URL` is set and the Postgres service is running.
- **Port:** Railway injects `PORT`; the startup script updates nginx to listen on it.
