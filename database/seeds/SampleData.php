<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Jobs\Banking\CreateTransfer;
use App\Jobs\Banking\CreateTransaction;
use App\Models\Banking\Account;
use App\Models\Banking\Reconciliation;
use App\Models\Banking\Transaction;
use App\Models\Banking\Transfer;
use App\Models\Common\Contact;
use App\Models\Common\Item;
use App\Models\Common\Recurring;
use App\Models\Document\Document;
use App\Models\Setting\Category;
use App\Models\Setting\Tax;
use App\Traits\Jobs;
use App\Utilities\Date;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleData extends Seeder
{
    use Jobs;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::reguard();

        config(['mail.default' => 'array']);

        $total_count = (int) $this->command->option('count');
        $company = (int) $this->command->option('company');

        // Distribute 1000 records across all features
        // Base counts
        $contact_count = min(150, $total_count); // Customers, vendors, employees
        $category_count = min(50, $total_count); // Income, expense, item, other categories
        $tax_count = min(15, $total_count); // Various tax types
        $item_count = min(100, $total_count); // Products/services
        $account_count = min(10, $total_count); // Bank accounts
        $invoice_count = min(200, $total_count); // Regular invoices
        $bill_count = min(200, $total_count); // Regular bills
        $recurring_invoice_count = min(30, $total_count); // Recurring invoices
        $recurring_bill_count = min(30, $total_count); // Recurring bills
        $income_transaction_count = min(100, $total_count); // Income transactions
        $expense_transaction_count = min(100, $total_count); // Expense transactions
        $transfer_count = min(20, $total_count); // Transfers between accounts
        $recurring_transaction_count = min(20, $total_count); // Recurring transactions
        $reconciliation_count = min(15, $total_count); // Account reconciliations

        $this->command->info('Creating comprehensive sample data covering all Akaunting features...');
        $this->command->info("Total records to create: ~{$total_count}");

        $bar = $this->command->getOutput()->createProgressBar(15);
        $bar->setFormat('verbose');

        $bar->start();

        // 1. Create Contacts (Customers, Vendors, Employees)
        $this->command->info("\n1. Creating contacts (customers, vendors, employees)...");
        $customers = Contact::factory()->company($company)->customer()->enabled()->count((int)($contact_count * 0.5))->create();
        $vendors = Contact::factory()->company($company)->vendor()->enabled()->count((int)($contact_count * 0.4))->create();
        $employees = Contact::factory()->company($company)->state(['type' => 'employee'])->enabled()->count((int)($contact_count * 0.1))->create();
        $all_contacts = $customers->merge($vendors)->merge($employees);
        $bar->advance();

        // 2. Create Categories (Income, Expense, Item, Other)
        $this->command->info("\n2. Creating categories (income, expense, item, other)...");
        $income_categories = Category::factory()->company($company)->income()->count((int)($category_count * 0.3))->create();
        $expense_categories = Category::factory()->company($company)->expense()->count((int)($category_count * 0.3))->create();
        $item_categories = Category::factory()->company($company)->item()->count((int)($category_count * 0.3))->create();
        $other_categories = Category::factory()->company($company)->other()->count((int)($category_count * 0.1))->create();
        $all_categories = $income_categories->merge($expense_categories)->merge($item_categories)->merge($other_categories);
        $bar->advance();

        // 3. Create Taxes (Normal, Fixed, Inclusive, Compound, Withholding)
        $this->command->info("\n3. Creating taxes (normal, fixed, inclusive, compound, withholding)...");
        $normal_taxes = Tax::factory()->company($company)->normal()->enabled()->count((int)($tax_count * 0.4))->create();
        $fixed_taxes = Tax::factory()->company($company)->fixed()->enabled()->count((int)($tax_count * 0.2))->create();
        $inclusive_taxes = Tax::factory()->company($company)->inclusive()->enabled()->count((int)($tax_count * 0.2))->create();
        $compound_taxes = Tax::factory()->company($company)->compound()->enabled()->count((int)($tax_count * 0.1))->create();
        $withholding_taxes = Tax::factory()->company($company)->withholding()->enabled()->count((int)($tax_count * 0.1))->create();
        $all_taxes = $normal_taxes->merge($fixed_taxes)->merge($inclusive_taxes)->merge($compound_taxes)->merge($withholding_taxes);
        $bar->advance();

        // 4. Create Items
        $this->command->info("\n4. Creating items (products/services)...");
        $items = Item::factory()->company($company)->count($item_count)->create();
        $bar->advance();

        // 5. Create Bank Accounts
        $this->command->info("\n5. Creating bank accounts...");
        $accounts = Account::factory()->company($company)->count($account_count)->create();
        $bar->advance();

        // 6. Create Regular Invoices (with various statuses)
        $this->command->info("\n6. Creating invoices (draft, sent, viewed, partial, paid, cancelled)...");
        $status_distribution = [
            'draft' => 0.15,
            'sent' => 0.20,
            'viewed' => 0.15,
            'partial' => 0.15,
            'paid' => 0.30,
            'cancelled' => 0.05,
        ];
        
        foreach ($status_distribution as $status => $percentage) {
            $count = (int)($invoice_count * $percentage);
            if ($count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $invoice = Document::factory()
                        ->company($company)
                        ->invoice()
                        ->create();
                    $invoice->status = $status;
                    $invoice->save();
                }
            }
        }
        $bar->advance();

        // 7. Create Regular Bills (with various statuses)
        $this->command->info("\n7. Creating bills (draft, received, partial, paid, cancelled)...");
        $bill_status_distribution = [
            'draft' => 0.15,
            'received' => 0.20,
            'partial' => 0.15,
            'paid' => 0.40,
            'cancelled' => 0.10,
        ];
        
        foreach ($bill_status_distribution as $status => $percentage) {
            $count = (int)($bill_count * $percentage);
            if ($count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $bill = Document::factory()
                        ->company($company)
                        ->bill()
                        ->create();
                    $bill->status = $status;
                    $bill->save();
                }
            }
        }
        $bar->advance();

        // 8. Create Recurring Invoices
        $this->command->info("\n8. Creating recurring invoices...");
        $recurring_frequencies = ['daily', 'weekly', 'monthly', 'yearly'];
        for ($i = 0; $i < $recurring_invoice_count; $i++) {
            $invoice = Document::factory()
                ->company($company)
                ->invoice()
                ->create();
            $invoice->type = Document::INVOICE_RECURRING_TYPE;
            $invoice->status = 'draft';
            $invoice->save();

            // Create recurring record
            Recurring::create([
                'company_id' => $company,
                'recurable_id' => $invoice->id,
                'recurable_type' => Document::class,
                'frequency' => $recurring_frequencies[array_rand($recurring_frequencies)],
                'interval' => 1,
                'started_at' => Date::now()->subDays(rand(1, 90)),
                'status' => Recurring::ACTIVE_STATUS,
                'limit_by' => 'count',
                'limit_count' => rand(5, 24),
                'auto_send' => rand(0, 1),
                'created_from' => 'core::seed',
            ]);
        }
        $bar->advance();

        // 9. Create Recurring Bills
        $this->command->info("\n9. Creating recurring bills...");
        for ($i = 0; $i < $recurring_bill_count; $i++) {
            $bill = Document::factory()
                ->company($company)
                ->bill()
                ->create();
            $bill->type = Document::BILL_RECURRING_TYPE;
            $bill->status = 'draft';
            $bill->save();

            // Create recurring record
            Recurring::create([
                'company_id' => $company,
                'recurable_id' => $bill->id,
                'recurable_type' => Document::class,
                'frequency' => $recurring_frequencies[array_rand($recurring_frequencies)],
                'interval' => 1,
                'started_at' => Date::now()->subDays(rand(1, 90)),
                'status' => Recurring::ACTIVE_STATUS,
                'limit_by' => 'count',
                'limit_count' => rand(5, 24),
                'auto_send' => rand(0, 1),
                'created_from' => 'core::seed',
            ]);
        }
        $bar->advance();

        // 10. Create Income Transactions (regular and from invoices)
        $this->command->info("\n10. Creating income transactions...");
        $invoices_with_payments = Document::where('company_id', $company)
            ->where('type', Document::INVOICE_TYPE)
            ->whereIn('status', ['partial', 'paid'])
            ->get()
            ->take((int)($income_transaction_count * 0.3));

        foreach ($invoices_with_payments as $invoice) {
            if ($invoice->amount > 0) {
                $this->dispatch(new CreateTransaction([
                    'company_id' => $company,
                    'type' => Transaction::INCOME_TYPE,
                    'account_id' => $accounts->random()->id,
                    'paid_at' => $invoice->issued_at,
                    'amount' => $invoice->status === 'paid' ? $invoice->amount : ($invoice->amount * 0.5),
                    'currency_code' => $invoice->currency_code,
                    'currency_rate' => $invoice->currency_rate,
                    'document_id' => $invoice->id,
                    'contact_id' => $invoice->contact_id,
                    'description' => 'Payment for ' . $invoice->document_number,
                    'category_id' => $invoice->category_id,
                    'payment_method' => setting('default.payment_method', 'offline-payments.cash.1'),
                    'created_from' => 'core::seed',
                ]));
            }
        }

        // Create standalone income transactions
        $remaining_income = $income_transaction_count - $invoices_with_payments->count();
        Transaction::factory()
            ->company($company)
            ->income()
            ->count($remaining_income)
            ->create();
        $bar->advance();

        // 11. Create Expense Transactions (regular and from bills)
        $this->command->info("\n11. Creating expense transactions...");
        $bills_with_payments = Document::where('company_id', $company)
            ->where('type', Document::BILL_TYPE)
            ->whereIn('status', ['partial', 'paid'])
            ->get()
            ->take((int)($expense_transaction_count * 0.3));

        foreach ($bills_with_payments as $bill) {
            if ($bill->amount > 0) {
                $this->dispatch(new CreateTransaction([
                    'company_id' => $company,
                    'type' => Transaction::EXPENSE_TYPE,
                    'account_id' => $accounts->random()->id,
                    'paid_at' => $bill->issued_at,
                    'amount' => $bill->status === 'paid' ? $bill->amount : ($bill->amount * 0.5),
                    'currency_code' => $bill->currency_code,
                    'currency_rate' => $bill->currency_rate,
                    'document_id' => $bill->id,
                    'contact_id' => $bill->contact_id,
                    'description' => 'Payment for ' . $bill->document_number,
                    'category_id' => $bill->category_id,
                    'payment_method' => setting('default.payment_method', 'offline-payments.cash.1'),
                    'created_from' => 'core::seed',
                ]));
            }
        }

        // Create standalone expense transactions
        $remaining_expense = $expense_transaction_count - $bills_with_payments->count();
        Transaction::factory()
            ->company($company)
            ->expense()
            ->count($remaining_expense)
            ->create();
        $bar->advance();

        // 12. Create Transfers between Accounts
        $this->command->info("\n12. Creating transfers between accounts...");
        for ($i = 0; $i < $transfer_count; $i++) {
            $from_account = $accounts->random();
            $to_account = $accounts->where('id', '!=', $from_account->id)->random();
            
            if (!$to_account) {
                continue;
            }

            $amount = rand(100, 10000);
            $transferred_at = Date::now()->subDays(rand(1, 180));

            $this->dispatch(new CreateTransfer([
                'company_id' => $company,
                'from_account_id' => $from_account->id,
                'from_account_currency_code' => $from_account->currency_code,
                'from_account_rate' => 1.0,
                'to_account_id' => $to_account->id,
                'to_account_currency_code' => $to_account->currency_code,
                'to_account_rate' => 1.0,
                'amount' => $amount,
                'transferred_at' => $transferred_at->format('Y-m-d'),
                'payment_method' => setting('default.payment_method', 'offline-payments.cash.1'),
                'description' => 'Transfer from ' . $from_account->name . ' to ' . $to_account->name,
                'reference' => 'TRF-' . strtoupper(uniqid()),
                'created_from' => 'core::seed',
            ]));
        }
        $bar->advance();

        // 13. Create Recurring Transactions
        $this->command->info("\n13. Creating recurring transactions...");
        for ($i = 0; $i < $recurring_transaction_count; $i++) {
            $is_income = rand(0, 1);
            $type = $is_income ? Transaction::INCOME_RECURRING_TYPE : Transaction::EXPENSE_RECURRING_TYPE;
            
            $transaction = Transaction::factory()
                ->company($company)
                ->create();
            $transaction->type = $type;
            $transaction->account_id = $accounts->random()->id;
            $transaction->save();

            // Create recurring record
            Recurring::create([
                'company_id' => $company,
                'recurable_id' => $transaction->id,
                'recurable_type' => Transaction::class,
                'frequency' => $recurring_frequencies[array_rand($recurring_frequencies)],
                'interval' => 1,
                'started_at' => Date::now()->subDays(rand(1, 90)),
                'status' => Recurring::ACTIVE_STATUS,
                'limit_by' => 'count',
                'limit_count' => rand(5, 24),
                'auto_send' => 0,
                'created_from' => 'core::seed',
            ]);
        }
        $bar->advance();

        // 14. Create Split Transactions
        $this->command->info("\n14. Creating split transactions...");
        $split_count = min(10, (int)($total_count * 0.01));
        for ($i = 0; $i < $split_count; $i++) {
            $is_income = rand(0, 1);
            $parent_type = $is_income ? Transaction::INCOME_SPLIT_TYPE : Transaction::EXPENSE_SPLIT_TYPE;
            $base_type = $is_income ? Transaction::INCOME_TYPE : Transaction::EXPENSE_TYPE;
            
            $parent = Transaction::factory()
                ->company($company)
                ->create();
            $parent->type = $parent_type;
            $parent->account_id = $accounts->random()->id;
            $parent->amount = rand(1000, 5000);
            $parent->save();

            // Create 2-4 split transactions
            $split_count_internal = rand(2, 4);
            $split_amount = $parent->amount / $split_count_internal;
            
            for ($j = 0; $j < $split_count_internal; $j++) {
                $split = Transaction::factory()
                    ->company($company)
                    ->create();
                $split->type = $base_type;
                $split->account_id = $accounts->random()->id;
                $split->amount = $split_amount;
                $split->split_id = $parent->id;
                $split->paid_at = $parent->paid_at;
                $split->save();
            }
        }
        $bar->advance();

        // 15. Create Reconciliations
        $this->command->info("\n15. Creating account reconciliations...");
        foreach ($accounts->take($reconciliation_count) as $account) {
            $start_date = Date::now()->subMonths(rand(1, 6))->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();
            
            $transactions = Transaction::where('company_id', $company)
                ->where('account_id', $account->id)
                ->whereBetween('paid_at', [$start_date, $end_date])
                ->get();

            Reconciliation::create([
                'company_id' => $company,
                'account_id' => $account->id,
                'started_at' => $start_date,
                'ended_at' => $end_date,
                'closing_balance' => $account->balance + $transactions->sum('amount'),
                'transactions' => $transactions->pluck('id')->toArray(),
                'reconciled' => rand(0, 1),
                'created_from' => 'core::seed',
            ]);
        }
        $bar->advance();

        $bar->finish();

        $this->command->info('');
        $this->command->info('');
        $this->command->info('✓ Sample data created successfully!');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info("  • Contacts: " . ($customers->count() + $vendors->count() + $employees->count()));
        $this->command->info("  • Categories: " . ($income_categories->count() + $expense_categories->count() + $item_categories->count() + $other_categories->count()));
        $this->command->info("  • Taxes: " . $all_taxes->count());
        $this->command->info("  • Items: " . $items->count());
        $this->command->info("  • Accounts: " . $accounts->count());
        $this->command->info("  • Invoices: " . $invoice_count);
        $this->command->info("  • Bills: " . $bill_count);
        $this->command->info("  • Recurring Invoices: " . $recurring_invoice_count);
        $this->command->info("  • Recurring Bills: " . $recurring_bill_count);
        $this->command->info("  • Income Transactions: " . $income_transaction_count);
        $this->command->info("  • Expense Transactions: " . $expense_transaction_count);
        $this->command->info("  • Transfers: " . $transfer_count);
        $this->command->info("  • Recurring Transactions: " . $recurring_transaction_count);
        $this->command->info("  • Split Transactions: " . $split_count);
        $this->command->info("  • Reconciliations: " . $reconciliation_count);
        $this->command->info('');
        $this->command->info('All features have been populated with sample data!');

        Model::unguard();
    }
}
