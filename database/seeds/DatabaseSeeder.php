<?php

use App\Model\Quote;
use App\Model\QuoteItem;
use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $quote = Quote::create([
            'client_name' => 'Test Client',
            'client_email' => 'client@example.com',
            'quote_date' => now()->toDateString(),
            'status' => 'pending',
            'subtotal' => 1000.00,
            'vat' => 150.00,
            'total' => 1150.00,
            'notes' => 'This is a test quote created from a seeder.'
        ]);

        $quote->items()->createMany([
            [
                'description' => 'Design Work',
                'quantity' => 5,
                'unit_price' => 100.00,
                'total' => 500.00
            ],
            [
                'description' => 'Development',
                'quantity' => 10,
                'unit_price' => 50.00,
                'total' => 500.00
            ]
        ]);
    }
}
