<?php

namespace Database\Factories;

use App\Models\LandPreparationStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandPreparationStep>
 */
class LandPreparationStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor' => (string) fake()->numberBetween(1, 99),
            'urutan' => fake()->numberBetween(1, 99),
            'judul' => fake()->sentence(3),
            'waktu' => 'H-'.fake()->numberBetween(10, 30).' Sebelum Tanam',
            'deskripsi' => fake()->paragraph(),
            'tips' => fake()->sentence(),
            'spesifikasi' => null,
            'foto' => null,
        ];
    }
}
