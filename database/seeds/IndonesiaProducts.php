<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Models\Common\Item;
use App\Models\Common\ItemTax;
use App\Models\Setting\Category;
use App\Models\Setting\Tax;
use Illuminate\Database\Seeder;

/**
 * Phase 3: Product categories and sample items for clothing department store
 * Uses core Akaunting (no Inventory app required for basic items)
 * Note: Variants (size, color) require the Inventory app - add those via UI after installing it
 */
class IndonesiaProducts extends Seeder
{
    public function run()
    {
        Model::unguard();

        company(1)->makeCurrent();

        $company_id = 1;

        // Create item categories for clothing
        $categories = [
            ['name' => 'T-Shirts', 'type' => 'item', 'color' => '#3B82F6'],
            ['name' => 'Pants', 'type' => 'item', 'color' => '#10B981'],
            ['name' => 'Dresses', 'type' => 'item', 'color' => '#EC4899'],
            ['name' => 'Jackets', 'type' => 'item', 'color' => '#8B5CF6'],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::create(array_merge($cat, [
                'company_id' => $company_id,
                'enabled' => 1,
                'created_from' => 'core::seed',
            ]));
            $categoryIds[$cat['name']] = $category->id;
        }

        // Get PPN 11% tax
        $tax = Tax::where('company_id', $company_id)->where('name', 'PPN 11%')->first();

        // Create sample items (basic - variants need Inventory app)
        $items = [
            ['name' => 'Kaos Polos Hitam', 'category_id' => $categoryIds['T-Shirts'], 'sale_price' => 99000, 'purchase_price' => 45000, 'description' => 'T-shirt polos hitam'],
            ['name' => 'Kaos Polos Putih', 'category_id' => $categoryIds['T-Shirts'], 'sale_price' => 99000, 'purchase_price' => 45000, 'description' => 'T-shirt polos putih'],
            ['name' => 'Celana Jeans Slim', 'category_id' => $categoryIds['Pants'], 'sale_price' => 249000, 'purchase_price' => 120000, 'description' => 'Celana jeans slim fit'],
            ['name' => 'Dress Casual', 'category_id' => $categoryIds['Dresses'], 'sale_price' => 199000, 'purchase_price' => 95000, 'description' => 'Dress casual sehari-hari'],
            ['name' => 'Jaket Denim', 'category_id' => $categoryIds['Jackets'], 'sale_price' => 349000, 'purchase_price' => 175000, 'description' => 'Jaket denim klasik'],
        ];

        foreach ($items as $itemData) {
            $item = Item::create(array_merge($itemData, [
                'company_id' => $company_id,
                'type' => 'product',
                'enabled' => 1,
                'created_from' => 'core::seed',
            ]));

            if ($tax) {
                ItemTax::create([
                    'company_id' => $company_id,
                    'item_id' => $item->id,
                    'tax_id' => $tax->id,
                    'created_from' => 'core::seed',
                ]);
            }
        }

        Model::reguard();
    }
}
