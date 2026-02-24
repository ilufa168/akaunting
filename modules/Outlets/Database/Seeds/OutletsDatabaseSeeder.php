<?php

namespace Modules\Outlets\Database\Seeds;

use App\Abstracts\Model;
use Illuminate\Database\Seeder;
use Modules\Outlets\Models\Outlet;

class OutletsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $company_id = $this->command->argument('company') ?? 1;

        $outlets = [
            ['name' => 'Outlet 1', 'address' => '', 'enabled' => 1],
            ['name' => 'Outlet 2', 'address' => '', 'enabled' => 1],
            ['name' => 'Outlet 3', 'address' => '', 'enabled' => 1],
            ['name' => 'Outlet 4', 'address' => '', 'enabled' => 1],
            ['name' => 'Outlet 5', 'address' => '', 'enabled' => 1],
        ];

        foreach ($outlets as $outlet) {
            Outlet::firstOrCreate(
                [
                    'company_id' => $company_id,
                    'name' => $outlet['name'],
                ],
                array_merge($outlet, [
                    'company_id' => $company_id,
                    'created_from' => 'core::seed',
                ])
            );
        }

        Model::reguard();
    }
}
