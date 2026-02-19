# Deploy Akaunting to Exabytes VPS

Step-by-step guide to deploy your Akaunting project on an Exabytes VPS (https://www.exabytes.co.id/).

## Prerequisites

- **Exabytes VPS** with root or sudo access (Linux – Ubuntu 22.04 LTS recommended)
- **Domain name** (optional but recommended for SSL)
- **SSH client** (Terminal on Mac/Linux, PuTTY on Windows)

---

## Step 1: Connect to Your VPS via SSH

1. Log in to the Exabytes Customer Portal and get your VPS IP and SSH credentials.
2. Connect via SSH:

```bash
ssh root@YOUR_VPS_IP
```

Replace `YOUR_VPS_IP` with your VPS IP address. Use the root password or key provided by Exabytes.

---

## Step 2: Update System and Install Dependencies

Run these commands on your VPS:

```bash
# Update system packages
apt update && apt upgrade -y

# Install essential tools
apt install -y software-properties-common curl git unzip

# Add PHP repository (for PHP 8.1+)
add-apt-repository -y ppa:ondrej/php
apt update

# Install PHP 8.1 and required extensions
apt install -y php8.1-fpm php8.1-cli php8.1-mysql php8.1-sqlite3 \
  php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl php8.1-gd \
  php8.1-intl php8.1-zip php8.1-dom php8.1-tokenizer php8.1-fileinfo

# Install MySQL (or MariaDB)
apt install -y mysql-server

# Install Nginx
apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js 18.x (for npm run production)
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
```

---

## Step 3: Configure MySQL and Create Database

```bash
# Secure MySQL installation (optional but recommended)
mysql_secure_installation
# Follow prompts: set root password, remove anonymous users, disallow remote root, remove test DB

# Log in to MySQL
mysql -u root -p

# In MySQL prompt, run:
CREATE DATABASE akaunting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'akaunting'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON akaunting.* TO 'akaunting'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Replace `YOUR_STRONG_PASSWORD` with a strong password.

---

## Step 4: Upload Your Project to the VPS

**Option A: Using Git (recommended if you have a repo)**

```bash
cd /var/www
git clone https://github.com/YOUR_USERNAME/akaunting.git
# Or your private repo URL
cd akaunting
```

**Option B: Using SCP/SFTP from your local machine**

From your **local Mac terminal** (not on VPS):

```bash
cd /Users/macos/Cursor/akaunting

# Create a tarball (excludes node_modules, vendor, .git)
tar --exclude='node_modules' --exclude='vendor' --exclude='.git' --exclude='database/database.sqlite' -czf akaunting-deploy.tar.gz .

# Upload to VPS
scp akaunting-deploy.tar.gz root@YOUR_VPS_IP:/var/www/

# On VPS, extract:
# ssh root@YOUR_VPS_IP
# mkdir -p /var/www/akaunting && cd /var/www/akaunting
# tar -xzf ../akaunting-deploy.tar.gz
# rm ../akaunting-deploy.tar.gz
```

**Option C: Using rsync**

```bash
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' --exclude 'database/database.sqlite' \
  /Users/macos/Cursor/akaunting/ root@YOUR_VPS_IP:/var/www/akaunting/
```

---

## Step 5: Install PHP and Node Dependencies

On the VPS, inside the project directory:

```bash
cd /var/www/akaunting

# Install PHP dependencies (production, no dev)
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm ci
npm run production
```

---

## Step 6: Configure Environment (.env)

```bash
cd /var/www/akaunting

# Copy example env if you don't have one
cp .env.example .env 2>/dev/null || true

# Edit .env (use nano or vim)
nano .env
```

Set these values in `.env`:

```env
APP_NAME=Akaunting
APP_ENV=production
APP_KEY=           # Will be generated in next step
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=akaunting
DB_USERNAME=akaunting
DB_PASSWORD=YOUR_STRONG_PASSWORD
DB_PREFIX=ak_

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Set to false in production
FIREWALL_ENABLED=false
```

Generate application key:

```bash
php artisan key:generate
```

---

## Step 7: Run Akaunting Installation or Migration

**If this is a fresh install:**

```bash
php artisan install \
  --db-name="akaunting" \
  --db-username="akaunting" \
  --db-password="YOUR_STRONG_PASSWORD" \
  --admin-email="admin@yourcompany.com" \
  --admin-password="YourSecureAdminPassword123"
```

**If you have existing SQLite/PostgreSQL data and want to migrate:**

1. Export your current database and import to MySQL (see migration section below).
2. Then run:

```bash
php artisan migrate --force
php artisan db:seed --class="Database\Seeds\IndonesiaSetup" --force   # If you use Indonesia setup
```

---

## Step 8: Set File Permissions

```bash
cd /var/www/akaunting

# Set ownership (nginx user may be 'nginx' or 'www-data' - check with: ps aux | grep nginx)
chown -R www-data:www-data /var/www/akaunting

# Set directory permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# Storage and cache must be writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Step 9: Configure Nginx

Create Nginx site configuration:

```bash
nano /etc/nginx/sites-available/akaunting
```

Paste (replace `yourdomain.com` with your domain or use your VPS IP):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/akaunting/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and reload Nginx:

```bash
ln -s /etc/nginx/sites-available/akaunting /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default  # Remove default site if desired
nginx -t
systemctl reload nginx
```

---

## Step 10: Set Up Laravel Scheduler (Cron)

Akaunting uses Laravel's scheduler for recurring tasks. Add this cron entry:

```bash
crontab -u www-data -e
```

Add this line (or use `root` crontab and change `www-data` if needed):

```
* * * * * cd /var/www/akaunting && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 11: (Optional) Enable SSL with Let's Encrypt

If you have a domain pointed to your VPS:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Follow prompts. Certbot will configure Nginx automatically. Renewal is automatic via cron.

---

## Step 12: Configure Firewall (Optional)

```bash
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw enable
```

---

## Verification

1. Open `http://YOUR_VPS_IP` or `https://yourdomain.com` in your browser.
2. You should see the Akaunting login page.
3. Log in with the admin email and password you set in Step 7.

---

## Migrating Existing Data (SQLite → MySQL)

If you have data in your local SQLite database and want to migrate:

### Option 1: Manual export/import

1. **Export from SQLite** (on your Mac):

```bash
cd /Users/macos/Cursor/akaunting
# Install sqlite3 if needed, then:
sqlite3 database/database.sqlite .dump > akaunting_dump.sql
```

2. **Convert SQLite dump for MySQL** (SQLite and MySQL syntax differ). You may need a converter or manual editing. A simple approach: use Akaunting's backup/restore if available, or re-enter critical data.

3. **Alternative**: Use a tool like [sqlite3-to-mysql](https://github.com/techouse/sqlite3-to-mysql) to migrate.

### Option 2: Fresh install + re-enter data

For a department store with 5 outlets, you can run the seeders on the new server:

```bash
php artisan db:seed --class="Database\Seeds\IndonesiaSetup" --force
php artisan db:seed --class="Database\Seeds\IndonesiaProducts" --force
```

Then configure companies, warehouses, and products via the web UI.

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 500 Internal Server Error | Check `storage/logs/laravel.log`, fix permissions on `storage` and `bootstrap/cache` |
| 502 Bad Gateway | Ensure PHP-FPM is running: `systemctl status php8.1-fpm` |
| Database connection refused | Verify MySQL is running, check `.env` DB credentials |
| Blank page | Set `APP_DEBUG=true` temporarily, check logs |
| Assets not loading | Run `npm run production` again, ensure `public/build` exists |

---

## Summary Checklist

- [ ] VPS updated, PHP 8.1+, MySQL, Nginx, Composer, Node.js installed
- [ ] MySQL database and user created
- [ ] Project uploaded to `/var/www/akaunting`
- [ ] `composer install --no-dev` and `npm run build` completed
- [ ] `.env` configured with production values and `APP_KEY` generated
- [ ] `php artisan install` or migrations run
- [ ] Permissions set (`www-data`, 775 on storage/cache)
- [ ] Nginx site enabled and reloaded
- [ ] Cron for scheduler configured
- [ ] SSL enabled (optional)
- [ ] Firewall configured (optional)

---

## References

- [Akaunting Requirements](https://akaunting.com/hc/docs/on-premise/requirements/)
- [Exabytes Indonesia](https://www.exabytes.co.id/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Akaunting Documentation](https://akaunting.com/hc/docs)
