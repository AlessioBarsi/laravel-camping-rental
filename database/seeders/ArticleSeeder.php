<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use Faker\Factory as Faker;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $categories = Category::pluck('id')->toArray();

        foreach (range(1, 20) as $i) {
            Article::create([
                'title'       => $faker->unique()->sentence(3),  // Random title
                'description' => $faker->paragraph(3),           // Random text
                'category_id' => $faker->randomElement($categories),
                'price'       => $faker->randomFloat(2, 1, 1000), // Between 1.00 and 1000.00
            ]);
        }
    }
}
