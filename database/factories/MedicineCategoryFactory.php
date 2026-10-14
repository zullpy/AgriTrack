<?php

namespace Database\Factories;

use App\Models\MedicineCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicineCategory>
 */
class MedicineCategoryFactory extends Factory
{
    protected $model = MedicineCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = ['purple', 'rose', 'emerald', 'teal', 'lime', 'amber', 'blue', 'indigo', 'cyan', 'orange'];

        return [
            'nama' => fake()->unique()->words(2, true),
            'warna' => fake()->randomElement($colors),
            'deskripsi' => fake()->sentence(),
            'urutan' => fake()->numberBetween(1, 20),
        ];
    }
}
