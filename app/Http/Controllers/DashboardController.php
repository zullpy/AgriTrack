<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\PlantCatalog;
use App\Models\PlantCatalogGuide;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Ambil HANYA tanaman yang saat ini aktif ditanam
        $activeCrops = Crop::where('status', 'Sedang Ditanam')->get();

        // Semua catalog untuk lookup emoji/tema/panduan
        $catalogsAll = PlantCatalog::with('guides')->get()->keyBy('key');

        // Daftar nama_tanaman unik dari tanaman aktif
        $uniqueNames = $activeCrops
            ->pluck('nama_tanaman')
            ->unique()
            ->filter()
            ->values();

        // Fallback themes agar card berganti warna bila > 1 tanaman tanpa catalog
        $fallbackThemes = ['emerald', 'rose', 'amber', 'sky', 'lime', 'teal', 'yellow'];

        $plantsCatalog = $uniqueNames->map(function (string $name, int $idx) use (
            $activeCrops,
            $catalogsAll,
            $fallbackThemes,
        ) {
            // Cocokkan ke plant_catalog berdasarkan keyword
            $matched = $catalogsAll->first(function (PlantCatalog $catalog) use ($name) {
                foreach ($catalog->keywordsArray as $keyword) {
                    if (str_contains(strtolower($name), $keyword)) {
                        return true;
                    }
                }

                return false;
            });

            // Crop aktif yang namanya cocok
            $activeCrop = $activeCrops->first(
                fn (Crop $c) => strtolower($c->nama_tanaman) === strtolower($name),
            );

            $cropEmoji = $activeCrop ? $activeCrop->emoji : Crop::getEmojiForName($name);
            $emoji = ($matched && ! empty($matched->emoji) && $matched->emoji !== '🌱')
                ? $matched->emoji
                : $cropEmoji;

            $theme = $matched
                ? $matched->theme
                : (str_contains(strtolower($name), 'tomat') ? 'rose' : $fallbackThemes[$idx % count($fallbackThemes)]);

            $cycle = $matched?->cycle ?: ($activeCrop ? 'Tanaman Aktif' : 'Komoditas');

            return [
                'catalog_id' => $matched?->id,
                'key' => $matched ? $matched->key : \Str::slug($name, '_'),
                'name' => $name,
                'emoji' => $emoji,
                'cycle' => $cycle,
                'theme' => $theme,
                'badge_color' => $matched ? $matched->badge_color : 'bg-gray-100 text-gray-700 border-gray-200',
                'active_crop' => $activeCrop,
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
