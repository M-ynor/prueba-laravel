<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'US Dollar',
                'symbol' => 'USD',
                'exchange_rate' => 1.000000,
            ],
            [
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 0.920000,
            ],
            [
                'name' => 'Guatemalan Quetzal',
                'symbol' => 'GTQ',
                'exchange_rate' => 7.850000,
            ],
            [
                'name' => 'Mexican Peso',
                'symbol' => 'MXN',
                'exchange_rate' => 17.250000,
            ],
            [
                'name' => 'British Pound',
                'symbol' => 'GBP',
                'exchange_rate' => 0.790000,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }

        $this->command->info('Currencies seeded successfully!');
    }
}
