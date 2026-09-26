<?php

namespace Database\Factories;

use App\Models\PlantingSeed;
use App\Models\PlantingStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlantingStep>
 */
class PlantingStepFactory extends Factory
{
    protected $model = PlantingStep::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'planting_seed_id' => PlantingSeed::factory(),
            'nomor' => (string) $this->faker->numberBetween(1, 10),
            'urutan' => $this->faker->numberBetween(1, 10),
            'judul' => $this->faker->sentence(4),
            'waktu' => 'H-'.$this->faker->numberBetween(1, 30).' Sebelum Tanam',
            'deskripsi' => $this->faker->paragraph(),
            'tips' => $this->faker->sentence(),
            'foto' => null,
        ];
    }
}
