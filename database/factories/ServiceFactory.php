<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return ['title' => $title, 'excerpt' => fake()->sentence(), 'body' => '<p>'.fake()->paragraph().'</p>', 'status' => 'draft'];
    }
}
