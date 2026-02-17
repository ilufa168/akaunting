# Deploy Akaunting to Railway

Deploy to Railway using your free trial. Railway supports Docker and PostgreSQL.

## Step 1: Create a New Project

1. Go to [railway.app](https://railway.app) and sign in.
2. Click **New Project**.
3. Select **Deploy from GitHub repo**.
4. Connect and choose `moolbneid-org/akaunting`.

## Step 2: Add PostgreSQL

1. In your project, click **+ New**.
2. Select **Database** → **PostgreSQL**.
3. Railway will create a Postgres instance and set `DATABASE_URL` automatically.

## Step 3: Configure the Web Service

1. Click on your **web service** (the one from the GitHub repo).
2. Go to **Variables** and add (or confirm):

   | Variable        | Value                          |
   |----------------|---------------------------------|
   | `DATABASE_URL` | `${{Postgres.DATABASE_URL}}`    |
   | `DB_CONNECTION`| `pgsql`                         |
   | `APP_KEY`      | Run `php artisan key:generate --show` locally |
   | `APP_URL`      | Your Railway URL (set after deploy) |
   | `ADMIN_EMAIL`  | Your email                      |
   | `ADMIN_PASSWORD` | Your password                 |

   **Tip:** Use `${{Postgres.DATABASE_URL}}` to reference the Postgres service. Replace `Postgres` with your database service name if different.

## Step 4: Deploy

1. Railway will build from the `Dockerfile` and deploy.
2. Go to **Settings** → **Networking** → **Generate Domain** to get a public URL.
3. Set `APP_URL` to that URL (e.g. `https://akaunting-production.up.railway.app`).
4. Redeploy so the app picks up `APP_URL`.

## Step 5: First-Time Setup

On first deploy, the startup script runs `php artisan install` using your env vars and creates the admin user. Log in at your `APP_URL` with `ADMIN_EMAIL` and `ADMIN_PASSWORD`.

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
