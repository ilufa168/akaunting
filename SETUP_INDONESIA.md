# Akaunting Setup Guide: Indonesia Department Store (5 Outlets)

## Current Status

- **Phase 1**: Complete - Akaunting is installed locally with SQLite
- **Phase 2**: Partially complete - Company, IDR currency, PPN tax rates (11%, 12%, 0%), Indonesian locale configured via seeder
- **Phases 3-5**: Require running the app and completing steps in the web UI

## Login Credentials

- **URL**: http://127.0.0.1:8080 (server runs on 8080 by default; port 8000 may be in use)
- **Email**: admin@gmail.com
- **Password**: 12345678

## Starting the Server

```bash
cd /Users/macos/Cursor/akaunting
npm run start
# or: php artisan serve --port=8080
```

Then open http://127.0.0.1:8080 in your browser.

---

## Phase 2: Remaining Steps (via Web UI)

1. **Settings > Company**: Update company name, address, logo for your department store
2. **Settings > Localization**: Confirm locale is "id" (Bahasa Indonesia) and currency is IDR
3. **Settings > Taxes**: PPN 11%, 12%, 0% are pre-configured
4. **Install Double-Entry app**: Go to Apps > App Store > search "Double-Entry" > Install
5. **Install Inventory app**: Go to Apps > App Store > search "Inventory" > Install
6. **Create 5 warehouses**: Inventory > Warehouses > Add Outlet 1 through Outlet 5 (name, address, phone per outlet)

---

## Phase 3: Products and Categories

1. **Inventory > Categories**: Create categories (e.g., T-Shirts, Pants, Dresses)
2. **Inventory > Groups**: Create item groups (product families)
3. **Inventory > Items**: Add items with variants (size: S,M,L,XL; color: Black, White, etc.)
4. **SKUs**: Define per variant when creating items
5. **Initial stock**: Set stock per warehouse (outlet) for each item
6. **Barcodes** (optional): Enable in Inventory settings

---

## Phase 4: Day-to-Day Operations

1. **Settings > Banking**: Create bank accounts
2. **Sales > Invoices**: Create test sales invoices (select warehouse for outlet)
3. **Purchases > Bills**: Record test purchases
4. **Inventory > Transfer Orders**: Test inter-outlet stock transfers
5. **Expenses**: Record expenses (assign to outlet where possible)
6. **Verify PPN**: Check invoice tax lines use 11% or 12% as appropriate

---

## Phase 5: Users and Production

1. **Settings > Users**: Create user accounts per role (Admin, Manager, Cashier)
2. **Routes > Roles**: Assign permissions per role
3. **Double-Entry > Reports**: Review P&L, Balance Sheet
4. **Inventory > Reports**: Review stock levels, movement
5. **Production**: Deploy to VPS/hosting with MySQL, SSL
6. **Backups**: Set up daily database and file backups (cron)

---

## Database

- **Local**: SQLite at `database/database.sqlite`
- **Production**: Use MySQL or MariaDB; update `.env` accordingly

## Seeders Created

Run these to apply or re-apply configuration:

```bash
# Phase 2: Indonesian tax rates (PPN 11%, 12%, 0%), IDR, locale
php artisan db:seed --class="Database\Seeds\IndonesiaSetup" --force

# Phase 3: Product categories (T-Shirts, Pants, Dresses, Jackets) and 5 sample items
php artisan db:seed --class="Database\Seeds\IndonesiaProducts" --force
```
