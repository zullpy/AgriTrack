<?php

namespace Database\Factories;

use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinancialTransaction>
 */
class FinancialTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crop_id' => null,
            'tipe' => fake()->randomElement(['pemasukan', 'pengeluaran']),
            'kategori' => fake()->randomElement(['Upah Tenaga Kerja', 'Benih / Bibit', 'Pupuk Tambahan', 'BBM & Mesin', 'Penjualan Hasil Tani Lain']),
            'judul' => fake()->sentence(3),
            'nominal' => fake()->numberBetween(10000, 5000000),
            'tanggal' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'keterangan' => fake()->optional()->sentence(),
            'foto_nota' => null,
        ];
    }
}
