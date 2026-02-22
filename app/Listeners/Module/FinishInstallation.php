<?php

namespace App\Listeners\Module;

use App\Events\Module\Installed as Event;
use App\Traits\Permissions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class FinishInstallation
{
    use Permissions;

    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $alias = (string) ($event->alias ?? '');

        if ($alias === '') {
            return;
        }

        $module = module($alias);

        if (!$module) {
            Log::warning('Skipping module migration/permissions; module not discoverable by repository.', [
                'alias' => $alias,
                'expected_manifest' => rtrim(module()->getModulePath($alias), '/\\') . DIRECTORY_SEPARATOR . 'module.json',
            ]);

            return;
        }

        try {
            Artisan::call('module:migrate', ['alias' => $alias, '--force' => true]);
        } catch (Throwable $e) {
            Log::warning('Module migration failed during install; continuing without it.', [
                'alias' => $alias,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $this->attachDefaultModulePermissions($module);
    }
}
