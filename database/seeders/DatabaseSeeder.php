<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MedicineCategorySeeder::class,
            MedicineSeeder::class,
            FinancialCategorySeeder::class,
            StepNoteSeeder::class,
            PlantCatalogSeeder::class,
            CropSeeder::class,
            LandPreparationStepSeeder::class,
            PlantingSeedSeeder::class,
            PlantingStepSeeder::class,
            FertilizerFermentationStepSeeder::class,
            FinancialTransactionSeeder::class,
        ]);
    }
}
