<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->sentence(rand(5, 7));
        $datetime = fake()->dateTimeBetween('-1 month', 'now');

        $content = '';
        for ($i = 0; $i < 5; $i++) {
            $content .= '<p class="mb-4">'.fake()->sentences(rand(5, 10), true).'</p>';
        }

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.rand(1111, 9999),
            'company' => fake()->company(),
            'location' => fake()->country(),
            //'logo' => basename($this->faker->image(storage_path('app/public'))),
            'logo' => fake()->imageUrl(),
            'is_highlighted' => (rand(1, 9) > 7),
            'is_active' => true,
            'content' => $content,
            'apply_link' => fake()->url(),
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ];
    }
}
