# Deploy Akaunting to Render

This guide walks you through deploying this Akaunting instance to Render.

## Prerequisites

- A [GitHub](https://github.com) account
- A [Render](https://render.com) account (free tier works)

## Step 1: Push to GitHub

1. Create a new repository on GitHub (e.g. `akaunting-demo`).

2. From your project directory, run:

   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
   git add .
   git commit -m "Add Render deployment config"
   git branch -M main
   git push -u origin main
   ```

   **Note:** The `.env` file is in `.gitignore` and won't be pushed—Render uses its own environment variables.

## Step 2: Deploy on Render

1. Go to [dashboard.render.com](https://dashboard.render.com).

2. Click **New** → **Blueprint**.

3. Connect your GitHub account if needed, then select your repository.

4. Render will detect `render.yaml` and create:
   - A **PostgreSQL database** (free tier)
   - A **Web Service** running the Docker build

5. Click **Apply** to create the resources.

## Step 3: Configure Environment Variables

After the first deploy, set these in **Dashboard → Your Service → Environment**:

| Variable         | Value                        | Notes                                   |
|------------------|------------------------------|-----------------------------------------|
| `APP_URL`        | `https://YOUR-SERVICE.onrender.com` | Replace with your actual Render URL     |
| `ADMIN_EMAIL`    | Your email                   | Used for first-time install             |
| `ADMIN_PASSWORD` | Your password                | Choose a strong password                |
| `COMPANY_EMAIL`  | Your company email           | Optional                                |

The database connection (`DATABASE_URL`) is set automatically by the Blueprint.

## Step 4: Add Sample Data (Optional)

To seed demo data after the first successful deploy:

1. Go to your service's **Shell** tab in the Render dashboard.
2. Run: `php artisan sample-data:seed`
3. Refresh the app.

## Troubleshooting

- **Build fails on npm:** The Dockerfile runs `npm run production`—if your Mix config differs, it may fall back gracefully.
- **Database connection errors:** Ensure the PostgreSQL database is fully provisioned before the web service deploys (Blueprint handles this).
- **Asset/cache issues:** Set `ASSET_URL` to your full `https://` URL if styles or scripts don’t load correctly.
