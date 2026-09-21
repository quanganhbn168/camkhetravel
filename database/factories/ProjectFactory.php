<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return ['title' => $title, 'excerpt' => fake()->sentence(), 'body' => '<p>'.fake()->paragraph().'</p>', 'status' => 'draft'];
    }
}
