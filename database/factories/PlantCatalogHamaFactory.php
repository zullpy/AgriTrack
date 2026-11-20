<?php

namespace Database\Factories;

use App\Models\PlantCatalog;
use App\Models\PlantCatalogHama;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlantCatalogHama>
 */
class PlantCatalogHamaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plant_catalog_id' => PlantCatalog::factory(),
            'judul' => fake()->randomElement(['Kutu Kebul', 'Thrips', 'Ulat Grayak', 'Antraknosa', 'Layu Bakteri']),
            'kategori' => fake()->randomElement(['Hama Utama', 'Penyakit Utama']),
            'gejala' => fake()->sentence(8),
            'solusi' => fake()->sentence(10),
            'foto' => [],
            'urutan' => fake()->numberBetween(1, 10),
        ];
    }
}
