<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\PlantCatalog;
use App\Models\PlantCatalogGuide;
use App\Models\PlantCatalogHama;
use App\Services\CloudinaryService;
use App\Services\ImageCompressionService;
use Database\Seeders\PlantCatalogSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PlantCatalogController extends Controller
{
    public function __construct(
        protected ImageCompressionService $compressionService,
        protected CloudinaryService $cloudinaryService
    ) {}

    public function index(Request $request): View
    {
        if (PlantCatalog::count() === 0) {
            (new PlantCatalogSeeder)->run();
        }

        $query = PlantCatalog::query();

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('varietas', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        $catalogs = $query->orderBy('urutan')->orderBy('name')->get();

        return view('plant-catalog.index', compact('catalogs'));
    }

    public function show(PlantCatalog $tanamanKatalog): View
    {
        $tanamanKatalog->load(['guides', 'hamas']);

        return view('plant-catalog.show', ['catalog' => $tanamanKatalog]);
    }

    public function create(Request $request): View
    {
        $name = (string) $request->query('name', '');
        $emoji = (string) $request->query('emoji', $name ? Crop::getEmojiForName($name) : '🌱');
        $theme = (string) $request->query('theme', str_contains(strtolower($name), 'tomat') ? 'rose' : 'emerald');
        $cycle = (string) $request->query('cycle', '35 – 45 Hari');

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
            'varietas' => 'nullable|string|max:150',
            'emoji' => 'required|string|max:20',
            'cycle' => 'nullable|string|max:80',
            'deskripsi' => 'nullable|string',
            'theme' => 'nullable|in:emerald,rose,amber,yellow,sky,lime,teal',
            'keywords' => 'nullable|string|max:255',
            'tahap_semai' => 'nullable|string',
            'tahap_penanaman' => 'nullable|string',
            'tahap_pemeliharaan' => 'nullable|string',
            'tips_penting' => 'nullable|string',
            'hama_penyakit' => 'nullable|string',
            'urutan' => 'nullable|integer|min:1|max:99',
            'aktif' => 'boolean',
            'semai_steps' => 'nullable|array',
            'semai_steps.*.nomor' => 'nullable|string|max:20',
            'semai_steps.*.judul' => 'nullable|string|max:255',
            'semai_steps.*.deskripsi' => 'nullable|string',
            'penanaman_steps' => 'nullable|array',
            'penanaman_steps.*.nomor' => 'nullable|string|max:20',
            'penanaman_steps.*.judul' => 'nullable|string|max:255',
            'penanaman_steps.*.deskripsi' => 'nullable|string',
            'pemeliharaan_steps' => 'nullable|array',
            'pemeliharaan_steps.*.nomor' => 'nullable|string|max:20',
            'pemeliharaan_steps.*.judul' => 'nullable|string|max:255',
            'pemeliharaan_steps.*.deskripsi' => 'nullable|string',
            'tips_items' => 'nullable|array',
            'tips_items.*.judul' => 'nullable|string|max:255',
            'tips_items.*.content' => 'nullable|string',
            'hama_items' => 'nullable|array',
            'hama_items.*.id' => 'nullable|integer',
            'hama_items.*.judul' => 'nullable|string|max:255',
            'hama_items.*.kategori' => 'nullable|string|max:100',
            'hama_items.*.gejala' => 'nullable|string',
            'hama_items.*.solusi' => 'nullable|string',
            'guides' => 'nullable|array',
            'guides.*.phase' => 'required|string|max:100',
            'guides.*.hst' => 'required|string|max:80',
            'guides.*.focus' => 'required|string|max:255',
            'guides.*.nutrients' => 'required|string',
            'guides.*.dosis' => 'required|string',
            'guides.*.metode' => 'required|string',
            'guides.*.tips' => 'nullable|string',
        ]);

        $attributes = [
            'name' => $validated['name'],
            'varietas' => $validated['varietas'] ?? null,
            'emoji' => $validated['emoji'],
            'cycle' => ! empty($validated['cycle']) ? $validated['cycle'] : '—',
            'deskripsi' => $validated['deskripsi'] ?? null,
            'theme' => ! empty($validated['theme']) ? $validated['theme'] : 'emerald',
            'keywords' => $validated['keywords'] ?? strtolower($validated['name']),
            'tahap_semai' => $validated['tahap_semai'] ?? null,
            'tahap_penanaman' => $validated['tahap_penanaman'] ?? null,
            'tahap_pemeliharaan' => $validated['tahap_pemeliharaan'] ?? null,
            'tips_penting' => $validated['tips_penting'] ?? null,
            'hama_penyakit' => $validated['hama_penyakit'] ?? null,
            'urutan' => $validated['urutan'] ?? ((PlantCatalog::max('urutan') ?? 0) + 1),
            'aktif' => $request->boolean('aktif', true),
        ];

        if ($request->has('semai_steps') && is_array($request->input('semai_steps'))) {
            $attributes['tahap_semai'] = PlantCatalog::compileStepsToText($request->input('semai_steps'));
        }
        if ($request->has('penanaman_steps') && is_array($request->input('penanaman_steps'))) {
            $attributes['tahap_penanaman'] = PlantCatalog::compileStepsToText($request->input('penanaman_steps'));
        }
        if ($request->has('pemeliharaan_steps') && is_array($request->input('pemeliharaan_steps'))) {
            $attributes['tahap_pemeliharaan'] = PlantCatalog::compileStepsToText($request->input('pemeliharaan_steps'));
        }
        if ($request->has('tips_items') && is_array($request->input('tips_items'))) {
            $attributes['tips_penting'] = PlantCatalog::compileTipsToText($request->input('tips_items'));
        }
        if ($request->has('hama_items') && is_array($request->input('hama_items'))) {
            $attributes['hama_penyakit'] = PlantCatalog::compileHamaToText($request->input('hama_items'));
        }

        if ($existing) {
            $existing->update($attributes);
            if (isset($validated['guides'])) {
                $this->syncGuides($existing, $validated['guides']);
            }
            if ($request->has('hama_items') && is_array($request->input('hama_items'))) {
                $this->syncHamaItems($existing, $request->input('hama_items'));
            }
            $target = $existing;
        } else {
            $attributes['key'] = $validated['key'];
            $target = PlantCatalog::create($attributes);
            if (! empty($validated['guides'])) {
                $this->syncGuides($target, $validated['guides']);
            }
            if ($request->has('hama_items') && is_array($request->input('hama_items'))) {
                $this->syncHamaItems($target, $request->input('hama_items'));
            }
        }

        $redirect = $request->input('redirect_to');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tanaman {$target->name} berhasil disimpan ke katalog.",
                'catalog' => $target->fresh(),
            ], 201);
        }

        if ($redirect === 'katalog') {
            return redirect()->route('tanaman-katalog.show', $target)
                ->with('success', "Tanaman {$target->name} berhasil disimpan ke katalog.");
        } elseif ($redirect === 'index') {
            return redirect()->route('tanaman-katalog.index')
                ->with('success', "Tanaman {$target->name} berhasil disimpan ke katalog.");
        }

        return redirect()->route('dashboard')
            ->with('success', "Panduan untuk tanaman {$target->name} berhasil ditambahkan.");
    }

    public function edit(PlantCatalog $tanamanKatalog): View
    {
        $tanamanKatalog->load(['guides', 'hamas']);

        return view('plant-catalog.edit', ['catalog' => $tanamanKatalog]);
    }

    public function update(Request $request, PlantCatalog $tanamanKatalog): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'key' => "required|string|max:50|alpha_dash|unique:plant_catalogs,key,{$tanamanKatalog->id}",
            'name' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:150',
            'emoji' => 'required|string|max:20',
            'cycle' => 'nullable|string|max:80',
            'deskripsi' => 'nullable|string',
            'theme' => 'nullable|in:emerald,rose,amber,yellow,sky,lime,teal',
            'keywords' => 'nullable|string|max:255',
            'tahap_semai' => 'nullable|string',
            'tahap_penanaman' => 'nullable|string',
            'tahap_pemeliharaan' => 'nullable|string',
            'tips_penting' => 'nullable|string',
            'hama_penyakit' => 'nullable|string',
            'urutan' => 'nullable|integer|min:1|max:99',
            'aktif' => 'boolean',
            'semai_steps' => 'nullable|array',
            'semai_steps.*.nomor' => 'nullable|string|max:20',
            'semai_steps.*.judul' => 'nullable|string|max:255',
            'semai_steps.*.deskripsi' => 'nullable|string',
            'penanaman_steps' => 'nullable|array',
            'penanaman_steps.*.nomor' => 'nullable|string|max:20',
            'penanaman_steps.*.judul' => 'nullable|string|max:255',
            'penanaman_steps.*.deskripsi' => 'nullable|string',
            'pemeliharaan_steps' => 'nullable|array',
            'pemeliharaan_steps.*.nomor' => 'nullable|string|max:20',
            'pemeliharaan_steps.*.judul' => 'nullable|string|max:255',
            'pemeliharaan_steps.*.deskripsi' => 'nullable|string',
            'tips_items' => 'nullable|array',
            'tips_items.*.judul' => 'nullable|string|max:255',
            'tips_items.*.content' => 'nullable|string',
            'hama_items' => 'nullable|array',
            'hama_items.*.id' => 'nullable|integer',
            'hama_items.*.judul' => 'nullable|string|max:255',
            'hama_items.*.kategori' => 'nullable|string|max:100',
            'hama_items.*.gejala' => 'nullable|string',
            'hama_items.*.solusi' => 'nullable|string',
            'guides' => 'nullable|array',
            'guides.*.phase' => 'required|string|max:100',
            'guides.*.hst' => 'required|string|max:80',
            'guides.*.focus' => 'required|string|max:255',
            'guides.*.nutrients' => 'required|string',
            'guides.*.dosis' => 'required|string',
            'guides.*.metode' => 'required|string',
            'guides.*.tips' => 'nullable|string',
        ]);

        $updateData = [
            'key' => $validated['key'],
            'name' => $validated['name'],
            'varietas' => $validated['varietas'] ?? null,
            'emoji' => $validated['emoji'],
            'cycle' => ! empty($validated['cycle']) ? $validated['cycle'] : ($tanamanKatalog->cycle ?? '—'),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'theme' => ! empty($validated['theme']) ? $validated['theme'] : ($tanamanKatalog->theme ?? 'emerald'),
            'keywords' => $validated['keywords'] ?? null,
            'tahap_semai' => $validated['tahap_semai'] ?? null,
            'tahap_penanaman' => $validated['tahap_penanaman'] ?? null,
            'tahap_pemeliharaan' => $validated['tahap_pemeliharaan'] ?? null,
            'tips_penting' => $validated['tips_penting'] ?? null,
            'hama_penyakit' => $validated['hama_penyakit'] ?? null,
            'urutan' => $validated['urutan'] ?? $tanamanKatalog->urutan,
            'aktif' => $request->boolean('aktif', true),
        ];

        if ($request->has('semai_steps') && is_array($request->input('semai_steps'))) {
            $updateData['tahap_semai'] = PlantCatalog::compileStepsToText($request->input('semai_steps'));
        }
        if ($request->has('penanaman_steps') && is_array($request->input('penanaman_steps'))) {
            $updateData['tahap_penanaman'] = PlantCatalog::compileStepsToText($request->input('penanaman_steps'));
        }
        if ($request->has('pemeliharaan_steps') && is_array($request->input('pemeliharaan_steps'))) {
            $updateData['tahap_pemeliharaan'] = PlantCatalog::compileStepsToText($request->input('pemeliharaan_steps'));
        }
        if ($request->has('tips_items') && is_array($request->input('tips_items'))) {
            $updateData['tips_penting'] = PlantCatalog::compileTipsToText($request->input('tips_items'));
        }
        if ($request->has('hama_items') && is_array($request->input('hama_items'))) {
            $updateData['hama_penyakit'] = PlantCatalog::compileHamaToText($request->input('hama_items'));
        }

        $tanamanKatalog->update($updateData);

        if (isset($validated['guides'])) {
            $this->syncGuides($tanamanKatalog, $validated['guides']);
        }

        if ($request->has('hama_items') && is_array($request->input('hama_items'))) {
            $this->syncHamaItems($tanamanKatalog, $request->input('hama_items'));
        }

        $redirect = $request->input('redirect_to');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tanaman {$tanamanKatalog->name} berhasil diperbarui.",
                'catalog' => $tanamanKatalog->fresh(),
            ]);
        }

        if ($redirect === 'katalog') {
            return redirect()->route('tanaman-katalog.show', $tanamanKatalog)
                ->with('success', "Tanaman {$tanamanKatalog->name} berhasil diperbarui.");
        } elseif ($redirect === 'index') {
            return redirect()->route('tanaman-katalog.index')
                ->with('success', "Tanaman {$tanamanKatalog->name} berhasil diperbarui.");
        }

        return redirect()->route('dashboard')
            ->with('success', "Tanaman {$tanamanKatalog->name} berhasil diperbarui.");
    }

    public function updateStage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'catalog_id' => 'nullable|integer',
            'plant_name' => 'nullable|string|max:100',
            'stage_type' => 'required|in:semai,penanaman,pemeliharaan,tips,hama',
            'content' => 'nullable|string',
            'emoji' => 'nullable|string|max:20',
        ]);

        $stageColumnMap = [
            'semai' => 'tahap_semai',
            'penanaman' => 'tahap_penanaman',
            'pemeliharaan' => 'tahap_pemeliharaan',
            'tips' => 'tips_penting',
            'hama' => 'hama_penyakit',
        ];

        $column = $stageColumnMap[$validated['stage_type']];
        $content = isset($validated['content']) ? trim((string) $validated['content']) : null;

        $catalog = null;
        if (! empty($validated['catalog_id'])) {
            $catalog = PlantCatalog::find($validated['catalog_id']);
        }

        if (! $catalog && ! empty($validated['plant_name'])) {
            $name = trim((string) $validated['plant_name']);
            $key = \Str::slug($name, '_');

            $catalog = PlantCatalog::where('name', $name)
                ->orWhere('key', $key)
                ->first();

            if (! $catalog) {
                $uniqueKey = $key !== '' ? $key : 'tanaman_'.time();
                if (PlantCatalog::where('key', $uniqueKey)->exists()) {
                    $uniqueKey .= '_'.time();
                }

                $catalog = PlantCatalog::create([
                    'name' => $name,
                    'key' => $uniqueKey,
                    'emoji' => $validated['emoji'] ?? Crop::getEmojiForName($name),
                    'cycle' => '35 – 45 Hari',
                    'theme' => str_contains(strtolower($name), 'tomat') ? 'rose' : 'emerald',
                    'keywords' => strtolower($name),
                    'urutan' => (PlantCatalog::max('urutan') ?? 0) + 1,
                    'aktif' => true,
                ]);
            }
        }

        if (! $catalog) {
            return response()->json([
                'success' => false,
                'message' => 'Tanaman tidak ditemukan di katalog.',
            ], 404);
        }

        $catalog->update([$column => $content]);

        $stageLabels = [
            'semai' => 'Tahap Semai',
            'penanaman' => 'Tahap Penanaman',
            'pemeliharaan' => 'Tahap Pemeliharaan',
            'tips' => 'Tips Penting',
            'hama' => 'Problem & Solusi Hama',
        ];

        return response()->json([
            'success' => true,
            'message' => "{$stageLabels[$validated['stage_type']]} untuk {$catalog->name} berhasil diperbarui!",
            'catalog_id' => $catalog->id,
            'catalog_url' => route('tanaman-katalog.show', $catalog),
            'stage_type' => $validated['stage_type'],
            'content' => $content,
            'tahap_semai' => $catalog->tahap_semai,
            'tahap_penanaman' => $catalog->tahap_penanaman,
            'tahap_pemeliharaan' => $catalog->tahap_pemeliharaan,
            'tips_penting' => $catalog->tips_penting,
            'hama_penyakit' => $catalog->hama_penyakit,
        ]);
    }

    public function saveStep(Request $request, PlantCatalog $tanamanKatalog): JsonResponse
    {
        $validated = $request->validate([
            'stage' => 'required|in:semai,penanaman,pemeliharaan',
            'index' => 'nullable|integer',
            'step_index' => 'nullable|integer',
            'nomor' => 'nullable|string|max:20',
            'judul' => 'required|string|max:255',
            'waktu' => 'nullable|string|max:100',
            'deskripsi' => 'required|string',
        ]);

        $stage = $validated['stage'];
        $column = match ($stage) {
            'semai' => 'tahap_semai',
            'penanaman' => 'tahap_penanaman',
            'pemeliharaan' => 'tahap_pemeliharaan',
        };

        $currentSteps = match ($stage) {
            'semai' => $tanamanKatalog->structured_semai,
            'penanaman' => $tanamanKatalog->structured_penanaman,
            'pemeliharaan' => $tanamanKatalog->structured_pemeliharaan,
        };

        $rawIndex = $request->input('index') ?? $request->input('step_index');
        $index = ($rawIndex !== null && $rawIndex !== '') ? (int) $rawIndex : null;
        $nomor = ! empty($validated['nomor']) ? trim($validated['nomor']) : ($index !== null ? (string) ($index + 1) : (string) (count($currentSteps) + 1));

        $deskripsi = trim($validated['deskripsi']);
        $waktu = ! empty($validated['waktu']) ? trim($validated['waktu']) : null;
        if ($waktu && ! preg_match('/(?:waktu|durasi|umur|interval|fase)\s*:/i', $deskripsi)) {
            $deskripsi = "Waktu: {$waktu}\n".$deskripsi;
        }

        $stepData = [
            'nomor' => $nomor,
            'judul' => trim($validated['judul']),
            'waktu' => $waktu,
            'deskripsi' => $deskripsi,
        ];

        if ($index !== null && isset($currentSteps[$index])) {
            $currentSteps[$index] = $stepData;
        } else {
            $currentSteps[] = $stepData;
        }

        $text = PlantCatalog::compileStepsToText($currentSteps);
        $tanamanKatalog->update([$column => $text]);

        $stageLabels = [
            'semai' => 'Tahap Semai',
            'penanaman' => 'Tahap Penanaman',
            'pemeliharaan' => 'Tahap Pemeliharaan',
        ];

        return response()->json([
            'success' => true,
            'message' => "Langkah {$nomor} {$stageLabels[$stage]} berhasil disimpan.",
            'steps' => $tanamanKatalog->fresh()->{"structured_{$stage}"},
            'stage' => $stage,
        ]);
    }

    public function deleteStep(Request $request, PlantCatalog $tanamanKatalog): JsonResponse
    {
        $validated = $request->validate([
            'stage' => 'required|in:semai,penanaman,pemeliharaan',
            'index' => 'nullable|integer',
            'step_index' => 'nullable|integer',
        ]);

        $stage = $validated['stage'];
        $column = match ($stage) {
            'semai' => 'tahap_semai',
            'penanaman' => 'tahap_penanaman',
            'pemeliharaan' => 'tahap_pemeliharaan',
        };

        $currentSteps = match ($stage) {
            'semai' => $tanamanKatalog->structured_semai,
            'penanaman' => $tanamanKatalog->structured_penanaman,
            'pemeliharaan' => $tanamanKatalog->structured_pemeliharaan,
        };

        $rawIndex = $request->input('index') ?? $request->input('step_index');
        if ($rawIndex === null || $rawIndex === '') {
            return response()->json([
                'success' => false,
                'message' => 'Index langkah wajib disertakan.',
            ], 422);
        }
        $index = (int) $rawIndex;
        if (isset($currentSteps[$index])) {
            $deletedTitle = $currentSteps[$index]['judul'] ?? ('Langkah '.($index + 1));
            array_splice($currentSteps, $index, 1);

            foreach ($currentSteps as $i => &$step) {
                $step['nomor'] = (string) ($i + 1);
            }
            unset($step);

            $text = PlantCatalog::compileStepsToText($currentSteps);
            $tanamanKatalog->update([$column => $text]);

            return response()->json([
                'success' => true,
                'message' => "Langkah '{$deletedTitle}' berhasil dihapus.",
                'steps' => $tanamanKatalog->fresh()->{"structured_{$stage}"},
                'stage' => $stage,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Langkah tidak ditemukan.',
        ], 404);
    }

    public function saveTip(Request $request, PlantCatalog $tanamanKatalog): JsonResponse
    {
        $judul = trim((string) ($request->input('judul') ?: $request->input('title')));
        if ($judul === '') {
            return response()->json([
                'success' => false,
                'message' => 'Judul tips wajib diisi.',
                'errors' => ['title' => ['Judul tips wajib diisi.']],
            ], 422);
        }

        $validated = $request->validate([
            'index' => 'nullable|integer',
            'tip_index' => 'nullable|integer',
            'content' => 'required|string',
            'subtitle' => 'nullable|string|max:255',
        ]);

        $currentTips = $tanamanKatalog->structured_tips;
        $rawIndex = $request->input('index') ?? $request->input('tip_index');
        $index = ($rawIndex !== null && $rawIndex !== '') ? (int) $rawIndex : null;

        $tipData = [
            'judul' => $judul,
            'title' => $judul,
            'subtitle' => trim($validated['subtitle'] ?? ''),
            'content' => trim($validated['content']),
        ];

        if ($index !== null && isset($currentTips[$index])) {
            $currentTips[$index] = $tipData;
        } else {
            $currentTips[] = $tipData;
        }

        $text = PlantCatalog::compileTipsToText($currentTips);
        $tanamanKatalog->update(['tips_penting' => $text]);

        return response()->json([
            'success' => true,
            'message' => "Tips '{$tipData['judul']}' berhasil disimpan.",
            'tips' => $tanamanKatalog->fresh()->structured_tips,
        ]);
    }

    public function deleteTip(Request $request, PlantCatalog $tanamanKatalog): JsonResponse
    {
        $validated = $request->validate([
            'index' => 'nullable|integer',
            'tip_index' => 'nullable|integer',
        ]);

        $currentTips = $tanamanKatalog->structured_tips;
        $rawIndex = $request->input('index') ?? $request->input('tip_index');
        if ($rawIndex === null || $rawIndex === '') {
            return response()->json([
                'success' => false,
                'message' => 'Index tips wajib disertakan.',
            ], 422);
        }
        $index = (int) $rawIndex;

        if (isset($currentTips[$index])) {
            $deletedTitle = $currentTips[$index]['title'] ?? $currentTips[$index]['judul'] ?? ('Tips '.($index + 1));
            array_splice($currentTips, $index, 1);

            $text = PlantCatalog::compileTipsToText($currentTips);
            $tanamanKatalog->update(['tips_penting' => $text]);

            return response()->json([
                'success' => true,
                'message' => "Tips '{$deletedTitle}' berhasil dihapus.",
                'tips' => $tanamanKatalog->fresh()->structured_tips,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tips tidak ditemukan.',
        ], 404);
    }

    public function destroy(PlantCatalog $tanamanKatalog, Request $request): RedirectResponse|JsonResponse
    {
        $id = $tanamanKatalog->id;
        $nama = $tanamanKatalog->name;
        $tanamanKatalog->guides()->delete();
        $tanamanKatalog->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tanaman {$nama} berhasil dihapus dari katalog.",
                'id' => $id,
            ]);
        }

        $redirect = $request->input('redirect_to');
        if ($redirect === 'index' || $redirect === 'katalog') {
            return redirect()->route('tanaman-katalog.index')
                ->with('success', "Tanaman {$nama} berhasil dihapus dari katalog.");
        }

        return redirect()->route('dashboard')
            ->with('success', "Tanaman {$nama} berhasil dihapus dari katalog.");
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

    /**
     * Sinkronisasi item problem & solusi hama dari form katalog.
     *
     * @param  array<int, array{id?: ?int, judul?: string, kategori?: string, gejala?: string, solusi?: string}>  $items
     */
    protected function syncHamaItems(PlantCatalog $catalog, array $items): void
    {
        $retainedIds = [];
        $order = 1;

        foreach ($items as $item) {
            $judul = trim((string) ($item['judul'] ?? ($item['title'] ?? '')));
            $gejala = trim((string) ($item['gejala'] ?? ''));
            $solusi = trim((string) ($item['solusi'] ?? ''));
            $kategori = trim((string) ($item['kategori'] ?? 'Hama Utama'));
            $id = ! empty($item['id']) ? (int) $item['id'] : null;

            if ($judul === '' && $gejala === '' && $solusi === '') {
                continue;
            }

            if ($judul === '') {
                $judul = 'Problem #'.$order;
            }

            if ($id) {
                $hama = PlantCatalogHama::where('plant_catalog_id', $catalog->id)->where('id', $id)->first();
                if ($hama) {
                    $hama->update([
                        'judul' => $judul,
                        'kategori' => $kategori ?: 'Hama Utama',
                        'gejala' => $gejala ?: null,
                        'solusi' => $solusi ?: null,
                        'urutan' => $order,
                    ]);
                    $retainedIds[] = $hama->id;
                    $order++;

                    continue;
                }
            }

            $newHama = PlantCatalogHama::create([
                'plant_catalog_id' => $catalog->id,
                'judul' => $judul,
                'kategori' => $kategori ?: 'Hama Utama',
                'gejala' => $gejala ?: null,
                'solusi' => $solusi ?: null,
                'foto' => [],
                'urutan' => $order,
            ]);
            $retainedIds[] = $newHama->id;
            $order++;
        }

        if (! empty($retainedIds)) {
            PlantCatalogHama::where('plant_catalog_id', $catalog->id)
                ->whereNotIn('id', $retainedIds)
                ->delete();
        }
    }

    /**
     * Unggah file foto galeri / kamera HP dan kompres.
     *
     * @return array<int, array{url: string, public_id: string, storage_type: string}>
     */
    protected function handleUploadedFiles(Request $request, string $folder = 'pertanian/hama_penyakit'): array
    {
        $files = [];

        if ($request->hasFile('foto')) {
            $galleryFiles = $request->file('foto');
            $files = array_merge($files, is_array($galleryFiles) ? $galleryFiles : [$galleryFiles]);
        }

        if ($request->hasFile('foto_kamera')) {
            $cameraFiles = $request->file('foto_kamera');
            $files = array_merge($files, is_array($cameraFiles) ? $cameraFiles : [$cameraFiles]);
        }

        $uploaded = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $compressed = $this->compressionService->compress($file);
                $res = $this->cloudinaryService->upload($compressed['path'], $folder);

                if (file_exists($compressed['path'])) {
                    @unlink($compressed['path']);
                }

                $uploaded[] = [
                    'url' => $res['url'],
                    'public_id' => $res['public_id'],
                    'storage_type' => $res['storage_type'],
                ];
            }
        }

        return $uploaded;
    }

    public function storeHama(Request $request, PlantCatalog $tanamanKatalog): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'gejala' => 'nullable|string',
            'solusi' => 'nullable|string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotos = $this->handleUploadedFiles($request);
        $urutan = ($tanamanKatalog->hamas()->max('urutan') ?? 0) + 1;

        $hama = $tanamanKatalog->hamas()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?? 'Hama Utama',
            'gejala' => $validated['gejala'] ?? null,
            'solusi' => $validated['solusi'] ?? null,
            'foto' => $uploadedPhotos,
            'urutan' => $urutan,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Problem & solusi hama '{$hama->judul}' berhasil ditambahkan.",
                'hama' => $hama,
            ]);
        }

        return redirect()->route('tanaman-katalog.show', ['tanaman_katalog' => $tanamanKatalog, 'tab' => 'hama'])
            ->with('success', "Problem & solusi hama '{$hama->judul}' berhasil ditambahkan.");
    }

    public function updateHama(Request $request, PlantCatalogHama $hama): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'gejala' => 'nullable|string',
            'solusi' => 'nullable|string',
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'nullable|string',
        ]);

        $existingPhotos = is_array($hama->foto) ? $hama->foto : [];
        if (! empty($validated['delete_photos'])) {
            $deleteUrls = $validated['delete_photos'];
            $remainingPhotos = [];
            foreach ($existingPhotos as $photoItem) {
                $url = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['path'] ?? null) : $photoItem;
                if ($url && in_array($url, $deleteUrls, true)) {
                    try {
                        $this->cloudinaryService->deleteImage($url);
                    } catch (Throwable) {
                        // Safe deletion
                    }
                } else {
                    $remainingPhotos[] = $photoItem;
                }
            }
            $existingPhotos = $remainingPhotos;
        }

        $newPhotos = $this->handleUploadedFiles($request);
        $finalPhotos = array_merge($existingPhotos, $newPhotos);

        $hama->update([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?? $hama->kategori,
            'gejala' => $validated['gejala'] ?? null,
            'solusi' => $validated['solusi'] ?? null,
            'foto' => $finalPhotos,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Problem & solusi hama '{$hama->judul}' berhasil diperbarui.",
                'hama' => $hama->fresh(),
            ]);
        }

        return redirect()->route('tanaman-katalog.show', ['tanaman_katalog' => $hama->plant_catalog_id, 'tab' => 'hama'])
            ->with('success', "Problem & solusi hama '{$hama->judul}' berhasil diperbarui.");
    }

    public function destroyHama(Request $request, PlantCatalogHama $hama): RedirectResponse|JsonResponse
    {
        $judul = $hama->judul;
        $catalogId = $hama->plant_catalog_id;
        $hama->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Problem & solusi hama '{$judul}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('tanaman-katalog.show', ['tanaman_katalog' => $catalogId, 'tab' => 'hama'])
            ->with('success', "Problem & solusi hama '{$judul}' berhasil dihapus.");
    }

    public function uploadPhotoHama(Request $request, PlantCatalogHama $hama): RedirectResponse|JsonResponse
    {
        $request->validate([
            'foto' => 'nullable',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto_kamera' => 'nullable',
            'foto_kamera.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $uploadedPhotos = $this->handleUploadedFiles($request);

        if (empty($uploadedPhotos)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada foto yang dipilih atau dijepret.'], 422);
            }

            return redirect()->back()->with('error', 'Tidak ada foto yang dipilih atau dijepret.');
        }

        $existingPhotos = is_array($hama->foto) ? $hama->foto : [];
        $hama->update([
            'foto' => array_merge($existingPhotos, $uploadedPhotos),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos)." foto berhasil ditambahkan ke '{$hama->judul}'.",
                'hama' => $hama->fresh(),
            ]);
        }

        return redirect()->route('tanaman-katalog.show', ['tanaman_katalog' => $hama->plant_catalog_id, 'tab' => 'hama'])
            ->with('success', count($uploadedPhotos)." foto berhasil ditambahkan ke '{$hama->judul}'.");
    }

    public function destroyPhotoHama(Request $request, PlantCatalogHama $hama): RedirectResponse|JsonResponse
    {
        try {
            $targetUrl = trim((string) $request->input('photo_url'));
            if ($targetUrl === '') {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Target foto tidak valid.'], 400);
                }

                return redirect()->back();
            }

            $existingPhotos = is_array($hama->foto) ? $hama->foto : [];
            $remainingPhotos = [];
            $photoDeleted = false;

            foreach ($existingPhotos as $photoItem) {
                $url = is_array($photoItem) ? ($photoItem['url'] ?? $photoItem['path'] ?? null) : $photoItem;
                if ($url && $url === $targetUrl) {
                    $photoDeleted = true;
                    try {
                        $this->cloudinaryService->deleteImage($url);
                    } catch (Throwable) {
                        // Safe deletion
                    }
                } else {
                    $remainingPhotos[] = $photoItem;
                }
            }

            if ($photoDeleted) {
                $hama->update(['foto' => $remainingPhotos]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto dokumentasi hama berhasil dihapus.',
                    'remaining_count' => count($remainingPhotos),
                    'hama' => $hama->fresh(),
                ]);
            }

            return redirect()->route('tanaman-katalog.show', ['tanaman_katalog' => $hama->plant_catalog_id, 'tab' => 'hama'])
                ->with('success', 'Foto dokumentasi hama berhasil dihapus.');
        } catch (Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus foto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus foto: '.$e->getMessage());
        }
    }
}
