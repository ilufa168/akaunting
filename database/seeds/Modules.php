<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class Modules extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->create();

        Model::reguard();
    }

    private function create()
    {
        $company_id = $this->command->argument('company');

        $locale = session('locale', company($company_id)->locale);

        $this->installIfAvailable('offline-payments', $company_id, $locale);
        $this->installIfAvailable('paypal-standard', $company_id, $locale);
    }

    private function installIfAvailable(string $alias, $company_id, string $locale): void
    {
        $manifest = rtrim(module()->getModulePath($alias), '/\\') . DIRECTORY_SEPARATOR . 'module.json';

        if (!is_file($manifest)) {
            Log::warning('Skipping module install during company seeding; module manifest not found.', [
                'alias' => $alias,
                'manifest' => $manifest,
            ]);

            return;
        }

        try {
            Artisan::call('module:install', [
                'alias'     => $alias,
                'company'   => $company_id,
                'locale'    => $locale,
            ]);
        } catch (Throwable $e) {
            Log::warning('Module install failed during company seeding; continuing without it.', [
                'alias' => $alias,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
