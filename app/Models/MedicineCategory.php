<?php

namespace App\Models;

use Database\Factories\MedicineCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MedicineCategory extends Model
{
    /** @use HasFactory<MedicineCategoryFactory> */
    use HasFactory;

    protected static bool $tableChecked = false;

    protected $fillable = [
        'id',
        'nama',
        'warna',
        'deskripsi',
        'urutan',
    ];

    protected $appends = ['theme_config'];

    /**
     * Boot the model.
     * Pastikan id terisi otomatis dengan max(id) + 1 jika MySQL tidak memiliki AUTO_INCREMENT pada kolom id.
     */
    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (empty($category->id)) {
                $maxId = (int) DB::table('medicine_categories')->max('id');
                $category->id = $maxId + 1;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    /**
     * Relasi ke data obat yang menggunakan kategori ini.
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class, 'jenis', 'nama');
    }

    /**
     * Daftar preset warna yang tersedia untuk dipilih pengguna.
     *
     * @return array<string, array<string, string>>
     */
    public static function colorPresets(): array
    {
        return [
            'purple' => [
                'name' => 'Ungu',
                'bg_sample' => 'bg-purple-500',
                'badge_bg' => 'bg-purple-100',
                'badge_text' => 'text-purple-800',
                'border_color' => 'border-purple-500',
                'ring_color' => 'ring-purple-400/25',
                'hover_card' => 'hover:border-purple-300 hover:bg-purple-50/50 hover:text-purple-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-purple-400 group-hover:scale-125',
                'checked_card' => 'border-purple-500 bg-purple-50/90 text-purple-900 font-bold ring-2 ring-purple-400/25 shadow-sm shadow-purple-500/10',
                'checked_dot' => 'bg-purple-600 ring-2 ring-purple-200 scale-110',
                'badge_icon' => 'text-purple-600',
            ],
            'rose' => [
                'name' => 'Merah Mawar',
                'bg_sample' => 'bg-rose-500',
                'badge_bg' => 'bg-rose-100',
                'badge_text' => 'text-rose-800',
                'border_color' => 'border-rose-500',
                'ring_color' => 'ring-rose-400/25',
                'hover_card' => 'hover:border-rose-300 hover:bg-rose-50/50 hover:text-rose-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-rose-400 group-hover:scale-125',
                'checked_card' => 'border-rose-500 bg-rose-50/90 text-rose-900 font-bold ring-2 ring-rose-400/25 shadow-sm shadow-rose-500/10',
                'checked_dot' => 'bg-rose-600 ring-2 ring-rose-200 scale-110',
                'badge_icon' => 'text-rose-600',
            ],
            'emerald' => [
                'name' => 'Hijau Zamrud',
                'bg_sample' => 'bg-emerald-500',
                'badge_bg' => 'bg-emerald-100',
                'badge_text' => 'text-emerald-800',
                'border_color' => 'border-emerald-500',
                'ring_color' => 'ring-emerald-400/25',
                'hover_card' => 'hover:border-emerald-300 hover:bg-emerald-50/50 hover:text-emerald-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-emerald-400 group-hover:scale-125',
                'checked_card' => 'border-emerald-500 bg-emerald-50/90 text-emerald-900 font-bold ring-2 ring-emerald-400/25 shadow-sm shadow-emerald-500/10',
                'checked_dot' => 'bg-emerald-600 ring-2 ring-emerald-200 scale-110',
                'badge_icon' => 'text-emerald-600',
            ],
            'teal' => [
                'name' => 'Teal / Toska',
                'bg_sample' => 'bg-teal-500',
                'badge_bg' => 'bg-teal-100',
                'badge_text' => 'text-teal-800',
                'border_color' => 'border-teal-500',
                'ring_color' => 'ring-teal-400/25',
                'hover_card' => 'hover:border-teal-300 hover:bg-teal-50/50 hover:text-teal-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-teal-400 group-hover:scale-125',
                'checked_card' => 'border-teal-500 bg-teal-50/90 text-teal-900 font-bold ring-2 ring-teal-400/25 shadow-sm shadow-teal-500/10',
                'checked_dot' => 'bg-teal-600 ring-2 ring-teal-200 scale-110',
                'badge_icon' => 'text-teal-600',
            ],
            'lime' => [
                'name' => 'Hijau Lime',
                'bg_sample' => 'bg-lime-500',
                'badge_bg' => 'bg-lime-100',
                'badge_text' => 'text-lime-800',
                'border_color' => 'border-lime-500',
                'ring_color' => 'ring-lime-400/25',
                'hover_card' => 'hover:border-lime-300 hover:bg-lime-50/50 hover:text-lime-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-lime-400 group-hover:scale-125',
                'checked_card' => 'border-lime-500 bg-lime-50/90 text-lime-900 font-bold ring-2 ring-lime-400/25 shadow-sm shadow-lime-500/10',
                'checked_dot' => 'bg-lime-600 ring-2 ring-lime-200 scale-110',
                'badge_icon' => 'text-lime-600',
            ],
            'amber' => [
                'name' => 'Kuning Amber',
                'bg_sample' => 'bg-amber-500',
                'badge_bg' => 'bg-amber-100',
                'badge_text' => 'text-amber-800',
                'border_color' => 'border-amber-500',
                'ring_color' => 'ring-amber-400/25',
                'hover_card' => 'hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-amber-400 group-hover:scale-125',
                'checked_card' => 'border-amber-500 bg-amber-50/90 text-amber-900 font-bold ring-2 ring-amber-400/25 shadow-sm shadow-amber-500/10',
                'checked_dot' => 'bg-amber-600 ring-2 ring-amber-200 scale-110',
                'badge_icon' => 'text-amber-600',
            ],
            'blue' => [
                'name' => 'Biru',
                'bg_sample' => 'bg-blue-500',
                'badge_bg' => 'bg-blue-100',
                'badge_text' => 'text-blue-800',
                'border_color' => 'border-blue-500',
                'ring_color' => 'ring-blue-400/25',
                'hover_card' => 'hover:border-blue-300 hover:bg-blue-50/50 hover:text-blue-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-blue-400 group-hover:scale-125',
                'checked_card' => 'border-blue-500 bg-blue-50/90 text-blue-900 font-bold ring-2 ring-blue-400/25 shadow-sm shadow-blue-500/10',
                'checked_dot' => 'bg-blue-600 ring-2 ring-blue-200 scale-110',
                'badge_icon' => 'text-blue-600',
            ],
            'indigo' => [
                'name' => 'Indigo',
                'bg_sample' => 'bg-indigo-500',
                'badge_bg' => 'bg-indigo-100',
                'badge_text' => 'text-indigo-800',
                'border_color' => 'border-indigo-500',
                'ring_color' => 'ring-indigo-400/25',
                'hover_card' => 'hover:border-indigo-300 hover:bg-indigo-50/50 hover:text-indigo-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-indigo-400 group-hover:scale-125',
                'checked_card' => 'border-indigo-500 bg-indigo-50/90 text-indigo-900 font-bold ring-2 ring-indigo-400/25 shadow-sm shadow-indigo-500/10',
                'checked_dot' => 'bg-indigo-600 ring-2 ring-indigo-200 scale-110',
                'badge_icon' => 'text-indigo-600',
            ],
            'cyan' => [
                'name' => 'Cyan',
                'bg_sample' => 'bg-cyan-500',
                'badge_bg' => 'bg-cyan-100',
                'badge_text' => 'text-cyan-800',
                'border_color' => 'border-cyan-500',
                'ring_color' => 'ring-cyan-400/25',
                'hover_card' => 'hover:border-cyan-300 hover:bg-cyan-50/50 hover:text-cyan-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-cyan-400 group-hover:scale-125',
                'checked_card' => 'border-cyan-500 bg-cyan-50/90 text-cyan-900 font-bold ring-2 ring-cyan-400/25 shadow-sm shadow-cyan-500/10',
                'checked_dot' => 'bg-cyan-600 ring-2 ring-cyan-200 scale-110',
                'badge_icon' => 'text-cyan-600',
            ],
            'orange' => [
                'name' => 'Oranye',
                'bg_sample' => 'bg-orange-500',
                'badge_bg' => 'bg-orange-100',
                'badge_text' => 'text-orange-800',
                'border_color' => 'border-orange-500',
                'ring_color' => 'ring-orange-400/25',
                'hover_card' => 'hover:border-orange-300 hover:bg-orange-50/50 hover:text-orange-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-orange-400 group-hover:scale-125',
                'checked_card' => 'border-orange-500 bg-orange-50/90 text-orange-900 font-bold ring-2 ring-orange-400/25 shadow-sm shadow-orange-500/10',
                'checked_dot' => 'bg-orange-600 ring-2 ring-orange-200 scale-110',
                'badge_icon' => 'text-orange-600',
            ],
            'red' => [
                'name' => 'Merah',
                'bg_sample' => 'bg-red-500',
                'badge_bg' => 'bg-red-100',
                'badge_text' => 'text-red-800',
                'border_color' => 'border-red-500',
                'ring_color' => 'ring-red-400/25',
                'hover_card' => 'hover:border-red-300 hover:bg-red-50/50 hover:text-red-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-red-400 group-hover:scale-125',
                'checked_card' => 'border-red-500 bg-red-50/90 text-red-900 font-bold ring-2 ring-red-400/25 shadow-sm shadow-red-500/10',
                'checked_dot' => 'bg-red-600 ring-2 ring-red-200 scale-110',
                'badge_icon' => 'text-red-600',
            ],
            'violet' => [
                'name' => 'Violet',
                'bg_sample' => 'bg-violet-500',
                'badge_bg' => 'bg-violet-100',
                'badge_text' => 'text-violet-800',
                'border_color' => 'border-violet-500',
                'ring_color' => 'ring-violet-400/25',
                'hover_card' => 'hover:border-violet-300 hover:bg-violet-50/50 hover:text-violet-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-violet-400 group-hover:scale-125',
                'checked_card' => 'border-violet-500 bg-violet-50/90 text-violet-900 font-bold ring-2 ring-violet-400/25 shadow-sm shadow-violet-500/10',
                'checked_dot' => 'bg-violet-600 ring-2 ring-violet-200 scale-110',
                'badge_icon' => 'text-violet-600',
            ],
            'fuchsia' => [
                'name' => 'Fuchsia',
                'bg_sample' => 'bg-fuchsia-500',
                'badge_bg' => 'bg-fuchsia-100',
                'badge_text' => 'text-fuchsia-800',
                'border_color' => 'border-fuchsia-500',
                'ring_color' => 'ring-fuchsia-400/25',
                'hover_card' => 'hover:border-fuchsia-300 hover:bg-fuchsia-50/50 hover:text-fuchsia-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-fuchsia-400 group-hover:scale-125',
                'checked_card' => 'border-fuchsia-500 bg-fuchsia-50/90 text-fuchsia-900 font-bold ring-2 ring-fuchsia-400/25 shadow-sm shadow-fuchsia-500/10',
                'checked_dot' => 'bg-fuchsia-600 ring-2 ring-fuchsia-200 scale-110',
                'badge_icon' => 'text-fuchsia-600',
            ],
            'pink' => [
                'name' => 'Pink',
                'bg_sample' => 'bg-pink-500',
                'badge_bg' => 'bg-pink-100',
                'badge_text' => 'text-pink-800',
                'border_color' => 'border-pink-500',
                'ring_color' => 'ring-pink-400/25',
                'hover_card' => 'hover:border-pink-300 hover:bg-pink-50/50 hover:text-pink-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-pink-400 group-hover:scale-125',
                'checked_card' => 'border-pink-500 bg-pink-50/90 text-pink-900 font-bold ring-2 ring-pink-400/25 shadow-sm shadow-pink-500/10',
                'checked_dot' => 'bg-pink-600 ring-2 ring-pink-200 scale-110',
                'badge_icon' => 'text-pink-600',
            ],
            'slate' => [
                'name' => 'Abu-abu / Slate',
                'bg_sample' => 'bg-slate-500',
                'badge_bg' => 'bg-slate-100',
                'badge_text' => 'text-slate-800',
                'border_color' => 'border-slate-500',
                'ring_color' => 'ring-slate-400/25',
                'hover_card' => 'hover:border-slate-300 hover:bg-slate-50/50 hover:text-slate-900 hover:shadow-md hover:-translate-y-0.5',
                'hover_dot' => 'group-hover:bg-slate-400 group-hover:scale-125',
                'checked_card' => 'border-slate-500 bg-slate-50/90 text-slate-900 font-bold ring-2 ring-slate-400/25 shadow-sm shadow-slate-500/10',
                'checked_dot' => 'bg-slate-600 ring-2 ring-slate-200 scale-110',
                'badge_icon' => 'text-slate-600',
            ],
        ];
    }

    /**
     * Dapatkan konfigurasi tema warna lengkap berdasarkan nama theme.
     *
     * @return array<string, string>
     */
    public static function getThemeConfig(?string $theme = null): array
    {
        $theme = strtolower(trim((string) $theme)) ?: 'purple';
        $presets = self::colorPresets();

        if (isset($presets[$theme])) {
            return array_merge(['theme' => $theme], $presets[$theme]);
        }

        return array_merge(['theme' => 'purple'], $presets['purple']);
    }

    /**
     * Pastikan tabel medicine_categories beserta semua kolom penting telah tersedia di database (self-healing).
     * Sangat krusial untuk hosting tanpa akses shell artisan / migrasi manual (InfinityFree dll).
     */
    public static function ensureTableExists(): void
    {
        if (static::$tableChecked) {
            return;
        }

        try {
            if (! Schema::hasTable('medicine_categories')) {
                Schema::create('medicine_categories', function (Blueprint $table) {
                    $table->id();
                    $table->string('nama', 100)->unique();
                    $table->string('warna', 50)->default('purple');
                    $table->string('deskripsi', 255)->nullable();
                    $table->integer('urutan')->default(0);
                    $table->timestamps();
                });

                $now = now();
                $defaults = [
                    ['nama' => 'Fungisida', 'warna' => 'purple', 'deskripsi' => 'Pencegah & pengendali jamur / fungi', 'urutan' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['nama' => 'Insektisida', 'warna' => 'rose', 'deskripsi' => 'Pengendali hama serangga & ulat', 'urutan' => 2, 'created_at' => $now, 'updated_at' => $now],
                    ['nama' => 'Pupuk', 'warna' => 'emerald', 'deskripsi' => 'Nutrisi makro & mikro tanaman', 'urutan' => 3, 'created_at' => $now, 'updated_at' => $now],
                    ['nama' => 'Vitamin', 'warna' => 'teal', 'deskripsi' => 'Suplemen tanaman & anti-stres', 'urutan' => 4, 'created_at' => $now, 'updated_at' => $now],
                    ['nama' => 'Bibit', 'warna' => 'lime', 'deskripsi' => 'Benih & bibit tanaman', 'urutan' => 5, 'created_at' => $now, 'updated_at' => $now],
                    ['nama' => 'Perlengkapan', 'warna' => 'amber', 'deskripsi' => 'Mulsa, ajir, tali, selang, perekat dll', 'urutan' => 6, 'created_at' => $now, 'updated_at' => $now],
                    ['nama' => 'Peralatan', 'warna' => 'blue', 'deskripsi' => 'Tangki semprot, gunting, koret, cangkul dll', 'urutan' => 7, 'created_at' => $now, 'updated_at' => $now],
                ];

                DB::table('medicine_categories')->insert($defaults);
            } else {
                if (! Schema::hasColumn('medicine_categories', 'warna')) {
                    Schema::table('medicine_categories', function (Blueprint $table) {
                        $table->string('warna', 50)->default('purple')->after('nama');
                    });
                }
                if (! Schema::hasColumn('medicine_categories', 'deskripsi')) {
                    Schema::table('medicine_categories', function (Blueprint $table) {
                        $table->string('deskripsi', 255)->nullable()->after('warna');
                    });
                }
                if (! Schema::hasColumn('medicine_categories', 'urutan')) {
                    Schema::table('medicine_categories', function (Blueprint $table) {
                        $table->integer('urutan')->default(0)->after('deskripsi');
                    });
                }

                if (DB::table('medicine_categories')->count() === 0) {
                    $now = now();
                    $defaults = [
                        ['nama' => 'Fungisida', 'warna' => 'purple', 'deskripsi' => 'Pencegah & pengendali jamur / fungi', 'urutan' => 1, 'created_at' => $now, 'updated_at' => $now],
                        ['nama' => 'Insektisida', 'warna' => 'rose', 'deskripsi' => 'Pengendali hama serangga & ulat', 'urutan' => 2, 'created_at' => $now, 'updated_at' => $now],
                        ['nama' => 'Pupuk', 'warna' => 'emerald', 'deskripsi' => 'Nutrisi makro & mikro tanaman', 'urutan' => 3, 'created_at' => $now, 'updated_at' => $now],
                        ['nama' => 'Vitamin', 'warna' => 'teal', 'deskripsi' => 'Suplemen tanaman & anti-stres', 'urutan' => 4, 'created_at' => $now, 'updated_at' => $now],
                        ['nama' => 'Bibit', 'warna' => 'lime', 'deskripsi' => 'Benih & bibit tanaman', 'urutan' => 5, 'created_at' => $now, 'updated_at' => $now],
                        ['nama' => 'Perlengkapan', 'warna' => 'amber', 'deskripsi' => 'Mulsa, ajir, tali, selang, perekat dll', 'urutan' => 6, 'created_at' => $now, 'updated_at' => $now],
                        ['nama' => 'Peralatan', 'warna' => 'blue', 'deskripsi' => 'Tangki semprot, gunting, koret, cangkul dll', 'urutan' => 7, 'created_at' => $now, 'updated_at' => $now],
                    ];
                    DB::table('medicine_categories')->insert($defaults);
                }

                // Perbaiki auto_increment pada kolom id jika di MySQL belum ada
                if (DB::getDriverName() === 'mysql') {
                    try {
                        $col = DB::selectOne("
                            SELECT EXTRA, COLUMN_KEY 
                            FROM information_schema.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'medicine_categories' 
                              AND COLUMN_NAME = 'id'
                        ");

                        if ($col && stripos($col->EXTRA ?? '', 'auto_increment') === false) {
                            if (empty($col->COLUMN_KEY) || strtoupper($col->COLUMN_KEY) !== 'PRI') {
                                DB::statement('ALTER TABLE `medicine_categories` ADD PRIMARY KEY (`id`)');
                            }
                            DB::statement('ALTER TABLE `medicine_categories` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                            Log::info('Berhasil memperbaiki auto_increment pada medicine_categories');
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Gagal alter auto_increment medicine_categories: '.$e->getMessage());
                    }
                }
            }

            // Sync jenis unik dari tabel medicines jika belum tercatat di medicine_categories
            if (Schema::hasTable('medicines')) {
                $existingJenis = DB::table('medicines')
                    ->whereNotNull('jenis')
                    ->where('jenis', '!=', '')
                    ->distinct()
                    ->pluck('jenis');

                $order = 10;
                $now = now();
                foreach ($existingJenis as $jenis) {
                    $trimJenis = trim($jenis);
                    if ($trimJenis !== '' && ! DB::table('medicine_categories')->where('nama', $trimJenis)->exists()) {
                        DB::table('medicine_categories')->insert([
                            'nama' => $trimJenis,
                            'warna' => self::getNextAvailableColor(),
                            'deskripsi' => null,
                            'urutan' => $order++,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            static::$tableChecked = true;
        } catch (\Throwable $e) {
            Log::warning('Gagal auto-heal medicine_categories: '.$e->getMessage());
        }
    }

    /**
     * Tentukan warna berikutnya secara otomatis agar setiap kategori memiliki warna yang berbeda.
     * Mengambil warna yang belum pernah digunakan, atau warna yang paling jarang dipakai jika semua warna sudah terpakai.
     *
     * @param  int|null  $excludeId  ID kategori yang sedang diedit (agar tidak dihitung sebagai warna terpakai)
     */
    public static function getNextAvailableColor(?int $excludeId = null): string
    {
        $colors = array_keys(self::colorPresets());

        try {
            $query = self::query();
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $usedColors = $query->pluck('warna')->filter()->toArray();

            // 1. Cari warna pertama yang belum pernah dipakai
            foreach ($colors as $c) {
                if (! in_array($c, $usedColors, true)) {
                    return $c;
                }
            }

            // 2. Jika seluruh warna sudah terpakai, pilih warna yang paling sedikit digunakan
            $colorCounts = array_count_values($usedColors);
            $leastUsed = $colors[0];
            $minCount = PHP_INT_MAX;

            foreach ($colors as $c) {
                $count = $colorCounts[$c] ?? 0;
                if ($count < $minCount) {
                    $minCount = $count;
                    $leastUsed = $c;
                }
            }

            return $leastUsed;
        } catch (\Throwable $e) {
            return $colors[0] ?? 'purple';
        }
    }

    /**
     * Accessor atribut theme_config.
     *
     * @return array<string, string>
     */
    public function getThemeConfigAttribute(): array
    {
        return self::getThemeConfig($this->warna);
    }
}
