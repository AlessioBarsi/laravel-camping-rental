<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Rental;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'total_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'rental_id' => Rental::factory(),
            'issued_at' => fake()->dateTime(),
            'payment_status' => fake()->randomElement(["pending","paid","cancelled"]),
            'payment_method' => fake()->randomElement(["credit_card","paypal","cash"]),
        ];
    }
}
