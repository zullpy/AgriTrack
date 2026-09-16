<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\PlantCatalog;
use App\Models\PlantCatalogGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlantCatalogController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function create(Request $request): View
    {
        $name = (string) $request->query('name', '');
        $emoji = (string) $request->query('emoji', $name ? Crop::getEmojiForName($name) : '🌱');
        $theme = (string) $request->query('theme', str_contains(strtolower($name), 'tomat') ? 'rose' : 'emerald');
        $cycle = (string) $request->query('cycle', '60 – 80 HST');

        if ($name !== '') {
            $existing = PlantCatalog::where('name', $name)
                ->orWhere('key', \Str::slug($name, '_'))
                ->first();

            if ($existing) {
                return view('plant-catalog.edit', ['catalog' => $existing->load('guides')]);
            }
        }

        $catalog = new PlantCatalog([
            'name' => $name,
            'key' => $name !== '' ? \Str::slug($name, '_') : '',
            'emoji' => $emoji,
            'cycle' => $cycle,
            'theme' => $theme,
            'urutan' => (PlantCatalog::max('urutan') ?? 0) + 1,
            'aktif' => true,
        ]);

        return view('plant-catalog.create', compact('catalog'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('key') && $request->filled('name')) {
            $request->merge(['key' => \Str::slug($request->string('name'), '_')]);
        } elseif ($request->filled('key')) {
            $request->merge(['key' => strtolower(\Str::slug($request->string('key'), '_'))]);
        }

        $existing = PlantCatalog::where('key', $request->input('key'))->first();

        $validated = $request->validate([
            'key' => 'required|string|max:50|alpha_dash'.($existing ? '' : '|unique:plant_catalogs,key'),
            'name' => 'required|string|max:100',
            'emoji' => 'required|string|max:20',
            'cycle' => 'required|string|max:80',
            'theme' => 'required|in:emerald,rose,amber,yellow,sky,lime,teal',
            'keywords' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1|max:99',
            'aktif' => 'boolean',
            'guides' => 'nullable|array',
            'guides.*.phase' => 'required|string|max:100',
            'guides.*.hst' => 'required|string|max:80',
            'guides.*.focus' => 'required|string|max:255',
            'guides.*.nutrients' => 'required|string',
            'guides.*.dosis' => 'required|string',
            'guides.*.metode' => 'required|string',
            'guides.*.tips' => 'nullable|string',
        ]);

        if ($existing) {
            $existing->update([
                'name' => $validated['name'],
                'emoji' => $validated['emoji'],
                'cycle' => $validated['cycle'],
                'theme' => $validated['theme'],
                'keywords' => $validated['keywords'] ?? strtolower($validated['name']),
                'urutan' => $validated['urutan'],
                'aktif' => $request->boolean('aktif', true),
            ]);
            $this->syncGuides($existing, $validated['guides'] ?? []);

            return redirect()->route('dashboard')
                ->with('success', "Panduan untuk tanaman {$existing->name} berhasil diperbarui.");
        }

        $catalog = PlantCatalog::create([
            'key' => $validated['key'],
            'name' => $validated['name'],
            'emoji' => $validated['emoji'],
            'cycle' => $validated['cycle'],
            'theme' => $validated['theme'],
            'keywords' => $validated['keywords'] ?? strtolower($validated['name']),
            'urutan' => $validated['urutan'],
            'aktif' => $request->boolean('aktif', true),
        ]);

        $this->syncGuides($catalog, $validated['guides'] ?? []);

        return redirect()->route('dashboard')
            ->with('success', "Panduan untuk tanaman {$catalog->name} berhasil ditambahkan.");
    }

    public function edit(PlantCatalog $tanamanKatalog): View
    {
        $tanamanKatalog->load('guides');

        return view('plant-catalog.edit', ['catalog' => $tanamanKatalog]);
    }

    public function update(Request $request, PlantCatalog $tanamanKatalog): RedirectResponse
    {
        $validated = $request->validate([
            'key' => "required|string|max:50|alpha_dash|unique:plant_catalogs,key,{$tanamanKatalog->id}",
            'name' => 'required|string|max:100',
            'emoji' => 'required|string|max:20',
            'cycle' => 'required|string|max:80',
            'theme' => 'required|in:emerald,rose,amber,yellow,sky,lime,teal',
            'keywords' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1|max:99',
            'aktif' => 'boolean',
            'guides' => 'nullable|array',
            'guides.*.phase' => 'required|string|max:100',
            'guides.*.hst' => 'required|string|max:80',
            'guides.*.focus' => 'required|string|max:255',
            'guides.*.nutrients' => 'required|string',
            'guides.*.dosis' => 'required|string',
            'guides.*.metode' => 'required|string',
            'guides.*.tips' => 'nullable|string',
        ]);

        $tanamanKatalog->update([
            'key' => $validated['key'],
            'name' => $validated['name'],
            'emoji' => $validated['emoji'],
            'cycle' => $validated['cycle'],
            'theme' => $validated['theme'],
            'keywords' => $validated['keywords'] ?? null,
            'urutan' => $validated['urutan'],
            'aktif' => $request->boolean('aktif'),
        ]);

        $this->syncGuides($tanamanKatalog, $validated['guides'] ?? []);

        return redirect()->route('dashboard')
            ->with('success', "Tanaman {$tanamanKatalog->name} berhasil diperbarui.");
    }

    /**
     * Hapus semua guides lama lalu buat ulang dari input form.
     *
     * @param  array<int, array<string, mixed>>  $guidesInput
     */
    private function syncGuides(PlantCatalog $catalog, array $guidesInput): void
    {
        $catalog->guides()->delete();

        foreach ($guidesInput as $index => $guideData) {
            // nutrients dikirim sebagai string textarea (satu baris = satu item)
            $nutrients = array_filter(
                array_map('trim', explode("\n", $guideData['nutrients'])),
            );

            PlantCatalogGuide::create([
                'plant_catalog_id' => $catalog->id,
                'phase' => $guideData['phase'],
                'hst' => $guideData['hst'],
                'focus' => $guideData['focus'],
                'nutrients' => array_values($nutrients),
                'dosis' => $guideData['dosis'],
                'metode' => $guideData['metode'],
                'tips' => $guideData['tips'] ?? null,
                'urutan' => $index + 1,
            ]);
        }
    }
}
