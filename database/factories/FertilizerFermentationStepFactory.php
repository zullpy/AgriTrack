<?php

namespace Database\Factories;

use App\Models\FertilizerFermentationStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FertilizerFermentationStep>
 */
class FertilizerFermentationStepFactory extends Factory
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
            'waktu' => 'Hari ke-'.fake()->numberBetween(1, 21),
            'deskripsi' => fake()->paragraph(),
            'tips' => fake()->sentence(),
            'spesifikasi' => null,
            'foto' => null,
        ];
    }
}
