<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Models\Setting\Tax;
use App\Models\Setting\Currency;
use Illuminate\Database\Seeder;

/**
 * Indonesia Department Store Setup - Phase 2
 * Configures: Indonesian tax rates (PPN), locale, currency
 */
class IndonesiaSetup extends Seeder
{
    public function run()
    {
        Model::unguard();

        $company_id = 1;
        company($company_id)->makeCurrent();

        // Ensure IDR currency exists and is default
        $idr = Currency::firstOrCreate(
            [
                'company_id' => $company_id,
                'code' => 'IDR',
            ],
            [
                'name' => 'Indonesian Rupiah',
                'rate' => 1,
                'enabled' => 1,
                'created_from' => 'core::seed',
            ]
        );
        $idr->rate = 1;
        $idr->enabled = 1;
        $idr->save();

        // Add Indonesian PPN tax rates
        $taxes = [
            ['name' => 'PPN 11%', 'rate' => 11, 'type' => 'normal'],
            ['name' => 'PPN 12%', 'rate' => 12, 'type' => 'normal'],
            ['name' => 'PPN 0%', 'rate' => 0, 'type' => 'normal'],
        ];

        foreach ($taxes as $taxData) {
            Tax::firstOrCreate(
                [
                    'company_id' => $company_id,
                    'name' => $taxData['name'],
                ],
                array_merge($taxData, [
                    'enabled' => 1,
                    'created_from' => 'core::seed',
                ])
            );
        }

        // Update company settings for Indonesia
        setting()->set([
            'default.currency' => 'IDR',
            'default.locale' => 'id',
            'company.country' => 'ID',
        ]);
        setting()->save();

        Model::reguard();
    }
}
