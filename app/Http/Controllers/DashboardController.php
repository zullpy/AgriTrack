<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\PlantCatalog;
use App\Models\PlantCatalogGuide;
use App\Models\PlantingSeed;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Ambil tanaman yang saat ini aktif (ditanam maupun disemai)
        $activeCrops = Crop::active()->orderBy('id')->get();

        // Ambil semua data bibit beserta langkah persemaian (untuk panduan semai)
        $plantingSeeds = PlantingSeed::with('steps')->get();

        // Semua catalog untuk lookup emoji/tema/panduan
        $catalogsAll = PlantCatalog::with('guides')->get()->keyBy('key');

        // Daftar tanaman aktif unik berdasarkan kombinasi nama_tanaman dan tipe (hst vs hss)
        $uniqueCrops = $activeCrops->unique(function (Crop $crop) {
            return strtolower($crop->nama_tanaman).'___'.($crop->tipe ?? 'hst');
        })->values();

        // Fallback themes agar card berganti warna bila > 1 tanaman tanpa catalog
        $fallbackThemes = ['emerald', 'rose', 'amber', 'sky', 'lime', 'teal', 'yellow'];

        $plantsCatalog = $uniqueCrops->map(function (Crop $activeCrop, int $idx) use (
            $catalogsAll,
            $plantingSeeds,
            $fallbackThemes,
        ) {
            $name = $activeCrop->nama_tanaman;

            // Cocokkan ke plant_catalog berdasarkan keyword
            $matched = $catalogsAll->first(function (PlantCatalog $catalog) use ($name) {
                foreach ($catalog->keywordsArray as $keyword) {
                    if (str_contains(strtolower($name), $keyword)) {
                        return true;
                    }
                }

                return false;
            });

            // Cocokkan ke planting_seeds (panduan semai) berdasarkan nama
            $matchedSeed = $plantingSeeds->first(function (PlantingSeed $s) use ($name) {
                $seedName = strtolower($s->nama_bibit);
                $cropName = strtolower($name);

                return str_contains($seedName, $cropName)
                    || str_contains($cropName, str_replace('bibit ', '', $seedName));
            });

            $cropEmoji = $activeCrop->emoji ?: Crop::getEmojiForName($name);
            $emoji = (! empty($activeCrop->attributes['emoji']))
                ? $activeCrop->attributes['emoji']
                : (($matched && ! empty($matched->emoji) && $matched->emoji !== '🌱')
                    ? $matched->emoji
                    : $cropEmoji);

            $theme = $matched
                ? $matched->theme
                : (str_contains(strtolower($name), 'tomat') ? 'rose' : $fallbackThemes[$idx % count($fallbackThemes)]);

            $cycle = $matched?->cycle ?: ($activeCrop->is_hss ? 'Tanaman Semai' : 'Tanaman Aktif');

            $key = ($matched ? $matched->key : \Str::slug($name, '_')).($activeCrop->is_hss ? '_hss_'.$activeCrop->id : '_'.$activeCrop->id);

            return [
                'catalog_id' => $matched?->id,
                'key' => $key,
                'name' => $name,
                'emoji' => $emoji,
                'cycle' => $cycle,
                'theme' => $theme,
                'badge_color' => $matched ? $matched->badge_color : 'bg-gray-100 text-gray-700 border-gray-200',
                'active_crop' => $activeCrop,
                'is_semai' => (bool) $activeCrop->is_hss,
                'planting_seed' => $matchedSeed ? [
                    'id' => $matchedSeed->id,
                    'nama_bibit' => $matchedSeed->nama_bibit,
                    'varietas' => $matchedSeed->varietas,
                    'deskripsi' => $matchedSeed->deskripsi,
                    'steps_count' => $matchedSeed->steps->count(),
                    'steps' => $matchedSeed->steps->map(fn ($st) => [
                        'id' => $st->id,
                        'nomor' => $st->nomor,
                        'urutan' => $st->urutan,
                        'judul' => $st->judul,
                        'waktu' => $st->waktu,
                        'deskripsi' => $st->deskripsi,
                        'tips' => $st->tips,
                        'foto' => $st->foto ?? [],
                    ])->values()->all(),
                ] : null,
                'guides' => $matched
                    ? $matched->guides->map(fn (PlantCatalogGuide $g) => [
                        'id' => "guide-{$matched->key}-{$g->id}",
                        'phase' => $g->phase,
                        'hst' => $g->hst,
                        'focus' => $g->focus,
                        'nutrients' => $g->nutrients ?? [],
                        'dosis' => $g->dosis,
                        'metode' => $g->metode,
                        'tips' => $g->tips,
                    ])->values()->all()
                    : [],
            ];
        })->values();

        return view('dashboard.index', compact('plantsCatalog', 'activeCrops'));
    }
}
