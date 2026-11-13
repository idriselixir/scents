<?php

namespace Database\Factories;

use App\Enums\FragranceStatus;
use App\Enums\PackshotTone;
use App\Models\Fragrance;
use App\Models\ScentFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fragrance>
 */
class FragranceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scent_family_id' => ScentFamily::factory(),
            'slug' => fake()->unique()->slug(2),
            'house' => fake()->randomElement(['Afnan', 'Lattafa']),
            'name' => fake()->unique()->words(2, true),
            'tone' => fake()->randomElement(PackshotTone::cases()),
            'size' => '100 mL',
            'concentration' => 'Eau de Parfum',
            'price' => fake()->numberBetween(30, 70) * 100,
            'caption' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'opening_notes' => implode(', ', fake()->words(3)),
            'heart_notes' => implode(', ', fake()->words(3)),
            'dry_down_notes' => implode(', ', fake()->words(3)),
            'source_url' => fake()->url(),
            'is_finder_pick' => false,
            'position' => fake()->numberBetween(1, 100),
            'status' => FragranceStatus::LISTED,
        ];
    }

    public function hidden(): static
    {
        return $this->state(['status' => FragranceStatus::HIDDEN]);
    }

    public function finderPick(): static
    {
        return $this->state(['is_finder_pick' => true]);
    }
}
