<?php

namespace Database\Factories;

use App\Models\StepNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StepNote>
 */
class StepNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(4),
            'catatan' => fake()->paragraph(),
        ];
    }
}
