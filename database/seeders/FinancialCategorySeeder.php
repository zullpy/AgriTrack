<?php

namespace Database\Seeders;

use App\Models\FinancialCategory;
use Illuminate\Database\Seeder;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialCategory::query()->delete();

        $categories = [
            [
                'nama' => 'Persiapan / Pra Olah Lahan',
                'tipe' => 'pengeluaran',
                'urutan' => 1,
            ],
            [
                'nama' => 'Olah Lahan Garapan',
                'tipe' => 'pengeluaran',
                'urutan' => 2,
                'sub' => [
                    [
                        'nama' => 'Pupuk Dasar - Organik',
                        'sub' => [
                            'HOK Pemberian Pupuk',
                            'Mamin',
                        ],
                    ],
                    [
                        'nama' => 'Pupuk Dasar - Kimia',
                        'sub' => [
                            'HOK Pemberian Pupuk',
                            'Mamin',
                        ],
                    ],
                    [
                        'nama' => 'Finishing Olah Lahan',
                        'sub' => [
                            'HOK Pekerja',
                            'Mamin',
                        ],
                    ],
                ],
            ],
            [
                'nama' => 'Penanaman',
                'tipe' => 'pengeluaran',
                'urutan' => 3,
                'sub' => [
                    'Bibit',
                    'Gok Penanaman',
                    'Mamin',
                ],
            ],
            [
                'nama' => 'Pemeliharaan',
                'tipe' => 'pengeluaran',
                'urutan' => 4,
                'sub' => [
                    [
                        'nama' => 'Nyemprot',
                        'sub' => [
                            'Obat',
                            'HOK Penyemprotan',
                        ],
                    ],
                    [
                        'nama' => 'Pemupukan',
                        'sub' => [
                            'Pupuk',
                            'HOK Pemupukan',
                        ],
                    ],
                ],
            ],
            [
                'nama' => 'Panen',
                'tipe' => 'keduanya',
                'urutan' => 5,
                'sub' => [
                    [
                        'nama' => 'BOP Panen',
                        'tipe' => 'pengeluaran',
                        'sub' => [
                            'HOK Panen',
                            'Mamin',
                            'Hasil Panen',
                        ],
                    ],
                    [
                        'nama' => 'Hasil Panen',
                        'tipe' => 'pemasukan',
                    ],
                ],
            ],
            [
                'nama' => 'Perlengkapan',
                'tipe' => 'pengeluaran',
                'urutan' => 6,
            ],
            [
                'nama' => 'Peralatan',
                'tipe' => 'pengeluaran',
                'urutan' => 7,
            ],
        ];

        foreach ($categories as $catData) {
            $subList = $catData['sub'] ?? [];
            unset($catData['sub']);

            $parent = FinancialCategory::create([
                'parent_id' => null,
                'nama' => $catData['nama'],
                'tipe' => $catData['tipe'],
                'urutan' => $catData['urutan'],
            ]);

            foreach ($subList as $subIndex => $subItem) {
                if (is_array($subItem)) {
                    $subSubList = $subItem['sub'] ?? [];
                    $subCategory = FinancialCategory::create([
                        'parent_id' => $parent->id,
                        'nama' => $subItem['nama'],
                        'tipe' => $subItem['tipe'] ?? $parent->tipe,
                        'urutan' => $subIndex + 1,
                    ]);

                    foreach ($subSubList as $subSubIndex => $subSubName) {
                        FinancialCategory::create([
                            'parent_id' => $subCategory->id,
                            'nama' => is_array($subSubName) ? $subSubName['nama'] : $subSubName,
                            'tipe' => $subCategory->tipe,
                            'urutan' => $subSubIndex + 1,
                        ]);
                    }
                } else {
                    FinancialCategory::create([
                        'parent_id' => $parent->id,
                        'nama' => $subItem,
                        'tipe' => $parent->tipe,
                        'urutan' => $subIndex + 1,
                    ]);
                }
            }
        }
    }
}
