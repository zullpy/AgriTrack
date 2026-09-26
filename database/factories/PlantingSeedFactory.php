<?php

namespace Database\Factories;

use App\Models\PlantingSeed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlantingSeed>
 */
class PlantingSeedFactory extends Factory
{
    protected $model = PlantingSeed::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_bibit' => 'Bibit '.$this->faker->words(2, true),
            'varietas' => $this->faker->word().' F1',
            'deskripsi' => $this->faker->paragraph(),
            'urutan' => $this->faker->numberBetween(1, 10),
        ];
    }
}
