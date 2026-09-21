<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return ['title' => $title, 'sku' => fake()->unique()->bothify('SKU-####'), 'excerpt' => fake()->sentence(), 'body' => '<p>'.fake()->paragraph().'</p>', 'status' => 'draft'];
    }
}
