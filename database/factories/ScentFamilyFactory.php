<?php

namespace Database\Factories;

use App\Models\ScentFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScentFamily>
 */
class ScentFamilyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'label' => fake()->words(2, true),
            'mood' => fake()->words(3, true),
            'example_notes' => implode(' · ', fake()->words(3)),
            'position' => fake()->numberBetween(1, 100),
        ];
    }
}
