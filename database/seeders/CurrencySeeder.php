<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

/**
 * Currency Seeder
 * 
 * Seeds the database with common currencies.
 */
class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'US Dollar',
                'symbol' => 'USD',
                'exchange_rate' => 1.000000, // Base currency
            ],
            [
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 0.920000, // Approximate rate
            ],
            [
                'name' => 'Guatemalan Quetzal',
                'symbol' => 'GTQ',
                'exchange_rate' => 7.850000, // Approximate rate
            ],
            [
                'name' => 'Mexican Peso',
                'symbol' => 'MXN',
                'exchange_rate' => 17.250000, // Approximate rate
            ],
            [
                'name' => 'British Pound',
                'symbol' => 'GBP',
                'exchange_rate' => 0.790000, // Approximate rate
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }

        $this->command->info('Currencies seeded successfully!');
    }
}
