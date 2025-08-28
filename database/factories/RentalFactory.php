<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ArticleInStore;
use App\Models\Rental;
use App\Models\User;

class RentalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rental::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'article_in_store_id' => ArticleInStore::factory(),
            'user_id' => User::factory(),
            'rented_at' => fake()->dateTime(),
            'returned_at' => fake()->dateTime(),
            'notes' => fake()->text(),
        ];
    }
}
