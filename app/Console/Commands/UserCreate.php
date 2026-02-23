<?php

namespace App\Console\Commands;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Common\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UserCreate extends Command
{
    protected $signature = 'user:create
                            {--name= : Full name of the user}
                            {--email= : Email address}
                            {--password= : Password}
                            {--company=1 : Company ID to associate with}
                            {--locale=en-GB : Locale}';

    protected $description = 'Create a user with admin role and all permissions';

    public function handle(): int
    {
        $name     = $this->option('name')     ?? $this->ask('Full name');
        $email    = $this->option('email')    ?? $this->ask('Email address');
        $password = $this->option('password') ?? $this->secret('Password');
        $companyId = (int) $this->option('company');
        $locale   = $this->option('locale');

        $userClass = user_model_class();

        if ($userClass::where('email', $email)->exists()) {
            $this->error("A user with email '{$email}' already exists.");
            return self::FAILURE;
        }

        $company = Company::find($companyId);
        if (! $company) {
            $this->error("Company with ID {$companyId} not found.");
            return self::FAILURE;
        }

        $companyName = $company->name;

        DB::transaction(function () use ($name, $email, $password, $companyName, $companyId, $locale, $userClass) {
            $user = $userClass::create([
                'name'         => $name,
                'email'        => $email,
                'password'     => $password,
                'locale'       => $locale,
                'enabled'      => 1,
                'created_from' => 'user:create',
            ]);

            $user->companies()->attach($companyId);

            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $user->roles()->attach($adminRole->id);
            }

            $allPermissionIds = Permission::pluck('id')->toArray();
            if ($allPermissionIds) {
                $user->permissions()->attach($allPermissionIds);
            }

            Artisan::call('user:seed', [
                'user'    => $user->id,
                'company' => $companyId,
            ]);

            $this->info('User created successfully.');
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID',          $user->id],
                    ['Name',        $user->name],
                    ['Email',       $user->email],
                    ['Company',     "{$companyName} (ID: {$companyId})"],
                    ['Role',        $adminRole ? $adminRole->display_name : '(none — admin role not found)'],
                    ['Permissions', count($allPermissionIds) . ' permissions assigned directly'],
                ]
            );
        });

        return self::SUCCESS;
    }
}
