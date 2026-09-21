<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return ['title' => $title, 'excerpt' => fake()->sentence(), 'body' => '<p>'.fake()->paragraph().'</p>', 'status' => 'draft'];
    }
}
