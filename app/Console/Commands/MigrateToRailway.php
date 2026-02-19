<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateToRailway extends Command
{
    protected $signature = 'migrate:to-railway
                            {--truncate : Truncate Railway tables before copying (default: yes for empty check)}';

    protected $description = 'Copy local SQLite data to Railway PostgreSQL';

    protected $tables = [
        'accounts', 'categories', 'companies', 'contact_persons', 'contacts',
        'currencies', 'dashboards', 'document_histories', 'document_item_taxes',
        'document_items', 'document_totals', 'documents', 'email_templates',
        'failed_jobs', 'firewall_ips', 'firewall_logs', 'item_taxes', 'items',
        'jobs', 'media', 'mediables', 'migrations', 'module_histories', 'modules',
        'notifications', 'outlets', 'password_resets', 'permissions',
        'personal_access_tokens', 'reconciliations', 'recurring', 'reports',
        'role_permissions', 'roles', 'sessions', 'settings', 'taxes',
        'transaction_taxes', 'transactions', 'transfers', 'user_companies',
        'user_dashboards', 'user_invitations', 'user_permissions', 'user_roles',
        'users', 'widgets',
    ];

    public function handle()
    {
        if (config('database.default') !== 'sqlite') {
            $this->error('Local database must be SQLite. Current: ' . config('database.default'));
            return 1;
        }

        if (empty(env('RAILWAY_DATABASE_URL'))) {
            $this->error('Set RAILWAY_DATABASE_URL in .env with Railway PostgreSQL public URL.');
            $this->line('Get it from: Railway → akaunting-db → Variables → Copy DATABASE_URL or DATABASE_PUBLIC_URL');
            $this->line('If URL uses .railway.internal, use the Public TCP URL from Connect tab instead.');
            return 1;
        }

        try {
            DB::connection('railway')->getPdo();
        } catch (\Exception $e) {
            $this->error('Cannot connect to Railway: ' . $e->getMessage());
            $this->line('Ensure RAILWAY_DATABASE_URL uses the *public* URL (not .railway.internal).');
            return 1;
        }

        $prefix = config('database.connections.railway.prefix', 'ak_');
        $this->info('Clearing Railway tables (to avoid duplicates)...');
        DB::connection('railway')->statement('SET session_replication_role = replica');
        foreach (array_reverse($this->tables) as $table) {
            try {
                DB::connection('railway')->table($table)->delete();
            } catch (\Exception $e) {
                // Table may not exist
            }
        }
        DB::connection('railway')->statement('SET session_replication_role = DEFAULT');

        $this->info('Copying local SQLite data to Railway PostgreSQL...');
        DB::connection('railway')->statement('SET session_replication_role = replica');

        $copied = 0;
        $errors = [];

        foreach ($this->tables as $table) {
            try {
                $count = DB::connection('sqlite')->table($table)->count();
                if ($count === 0) {
                    continue;
                }

                $this->line("  Copying {$table} ({$count} rows)...");

                $rows = DB::connection('sqlite')->table($table)->get();
                foreach ($rows->chunk(100) as $chunk) {
                    $inserts = $chunk->map(fn ($r) => (array) $r)->toArray();
                    DB::connection('railway')->table($table)->insert($inserts);
                    $copied += count($inserts);
                }

                $this->info("    ✓ {$table}");
            } catch (\Exception $e) {
                $errors[$table] = $e->getMessage();
                $this->warn("    ✗ {$table}: " . $e->getMessage());
            }
        }

        DB::connection('railway')->statement('SET session_replication_role = DEFAULT');

        $this->newLine();
        $this->info("Done. Copied {$copied} total rows.");

        if (!empty($errors)) {
            $this->warn('Some tables had errors:');
            foreach ($errors as $t => $msg) {
                $this->line("  - {$t}: {$msg}");
            }
            return 1;
        }

        return 0;
    }
}
