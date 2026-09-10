<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\CropActivity;
use App\Models\Medicine;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KalenderHstController extends Controller
{
    public function index(Request $request): View
    {
        // Parameter Bulan & Tahun
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $currentMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Kalender grid dimulai Senin (Carbon::MONDAY)
        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // Ambil tanaman aktif beserta kegiatannya
        $activeCrops = Crop::active()->with('activities')->get();

        // Ambil riwayat tanaman sudah dipanen
        $harvestedCrops = Crop::harvested()
            ->with('activities')
            ->latest('tanggal_panen')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        // Batas maksimal HST tanaman aktif yang ditampilkan harian di kalender adalah sampai besok
        $maxDisplayDate = now()->addDay()->endOfDay();

        // Buat struktur grid hari kalender
        $calendarDays = [];
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dayDate = $cursor->copy();
            $dateString = $dayDate->toDateString();

            // Kumpulkan tanaman yang berjalan pada tanggal ini (hanya sampai besok)
            $dayCrops = [];
            if ($dayDate->lte($maxDisplayDate)) {
                foreach ($activeCrops as $crop) {
                    $hstOnDay = $crop->getHstOnDate($dayDate);
                    if ($hstOnDay !== null) {
                        $dayCrops[] = [
                            'crop' => $crop,
                            'hst' => $hstOnDay,
                        ];
                    }
                }
            }

            // Kumpulkan kegiatan yang jatuh tempo pada tanggal ini
            $dayActivities = [];
            foreach ($activeCrops as $crop) {
                foreach ($crop->activities as $act) {
                    $targetDate = $act->target_date;
                    if ($targetDate && $targetDate->toDateString() === $dateString) {
                        $dayActivities[] = [
                            'activity' => $act,
                            'crop' => $crop,
                        ];
                    }
                }
            }

            $calendarDays[] = [
                'date' => $dayDate,
                'dayNumber' => $dayDate->day,
                'isCurrentMonth' => $dayDate->month === $month,
                'isToday' => $dayDate->isToday(),
                'isTomorrow' => $dayDate->isTomorrow(),
                'crops' => $dayCrops,
                'activities' => $dayActivities,
            ];

            $cursor->addDay();
        }

        // Pecah jadi baris-baris minggu
        $calendarWeeks = array_chunk($calendarDays, 7);

        // Hitung kegiatan jatuh tempo minggu ini (HST <= saat ini dan status belum)
        $dueThisWeekCount = 0;
        foreach ($activeCrops as $crop) {
            foreach ($crop->activities as $act) {
                if ($act->status === 'Belum' && $crop->current_hst >= $act->target_hst) {
                    $dueThisWeekCount++;
                }
            }
        }

        return view('kalender-hst.index', compact(
            'calendarWeeks',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'activeCrops',
            'harvestedCrops',
            'dueThisWeekCount'
        ));
    }

    public function storeCrop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_tanaman' => 'required|string|max:255',
            'varietas' => 'nullable|string|max:255',
            'populasi' => 'nullable|string|max:255',
            'tanggal_tanam' => 'required|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $validated['status'] = 'Sedang Ditanam';

        Crop::create($validated);

        return redirect('/kalender-hst?tab=aktif')->with('success', 'Data tanaman baru berhasil ditambahkan.');
    }

    public function updateCrop(Request $request, Crop $crop): RedirectResponse
    {
        $validated = $request->validate([
            'nama_tanaman' => 'required|string|max:255',
            'varietas' => 'nullable|string|max:255',
            'populasi' => 'nullable|string|max:255',
            'tanggal_tanam' => 'required|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $crop->update($validated);

        return redirect()->back()->with('success', 'Informasi tanaman berhasil diperbarui.');
    }

    public function destroyCrop(Crop $crop): RedirectResponse
    {
        $nama = $crop->nama_tanaman;
        $crop->delete();

        return redirect('/kalender-hst')->with('success', "Tanaman {$nama} berhasil dihapus.");
    }

    public function markAsHarvested(Request $request, Crop $crop): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_panen' => 'required|date|after_or_equal:' . $crop->tanggal_tanam->toDateString(),
            'catatan' => 'nullable|string|max:1000',
        ]);

        $plantDate = Carbon::parse($crop->tanggal_tanam)->startOfDay();
        $harvestDate = Carbon::parse($validated['tanggal_panen'])->startOfDay();
        $totalHst = (int) $plantDate->diffInDays($harvestDate);

        $crop->update([
            'status' => 'Sudah Dipanen',
            'tanggal_panen' => $validated['tanggal_panen'],
            'total_hst_panen' => $totalHst,
            'catatan' => $validated['catatan'] ? ($crop->catatan . "\n[Panen]: " . $validated['catatan']) : $crop->catatan,
        ]);

        return redirect('/kalender-hst?tab=riwayat')->with('success', "Tanaman {$crop->nama_tanaman} telah ditandai sudah dipanen (HST {$totalHst}).");
    }

    public function showCrop(Request $request, Crop $crop): View
    {
        $crop->load(['activities' => function ($query) {
            $query->orderBy('target_hst')->orderBy('id');
        }]);

        $currentHst = $crop->current_hst;
        $plantDate = Carbon::parse($crop->tanggal_tanam)->startOfDay();

        // Ambil data obat untuk kemudahan input aplikasi obat
        $medicines = Medicine::orderBy('nama')->get(['id', 'nama', 'jenis', 'dosis_anjuran', 'sasaran_obat']);

        $activitiesByHst = $crop->activities->groupBy('target_hst');
        $maxActivityHst = (int) ($crop->activities->max('target_hst') ?? 0);

        // Tentukan batas atas HST yang ditampilkan dalam tabel
        if ($crop->status === 'Sudah Dipanen') {
            $harvestHst = (int) ($crop->total_hst_panen ?? 0);
            $maxRowHst = max($harvestHst, $maxActivityHst);
        } else {
            $requestedLimit = $request->input('limit_hst');
            if ($requestedLimit !== null && is_numeric($requestedLimit)) {
                $maxRowHst = max(0, (int) $requestedLimit);
            } else {
                // Sesuai instruksi: Tampilkan baris HST hanya sampai besok
                $maxRowHst = $currentHst + 1;
            }
        }

        $tableRows = [];
        for ($hst = 0; $hst <= $maxRowHst; $hst++) {
            $dayDate = $plantDate->copy()->addDays($hst);
            $isToday = $crop->status === 'Sedang Ditanam' && $dayDate->isToday();
            $isTomorrow = $crop->status === 'Sedang Ditanam' && $dayDate->isTomorrow();
            $isHarvestDay = ($crop->status === 'Sudah Dipanen' && $hst === (int) $crop->total_hst_panen);
            $isPast = $crop->status === 'Sedang Ditanam' ? ($dayDate->lt(now()->startOfDay())) : true;

            $tableRows[] = [
                'hst' => $hst,
                'date' => $dayDate,
                'is_today' => $isToday,
                'is_tomorrow' => $isTomorrow,
                'is_past' => $isPast,
                'is_harvest' => $isHarvestDay,
                'is_planting_day' => ($hst === 0),
                'activities' => $activitiesByHst->get($hst, collect()),
            ];
        }

        $totalActivities = $crop->activities->count();
        $completedActivities = $crop->activities->where('status', 'Selesai')->count();
        $pendingActivities = $totalActivities - $completedActivities;
        $completionRate = $totalActivities > 0 ? (int) round(($completedActivities / $totalActivities) * 100) : 0;

        return view('kalender-hst.show', compact(
            'crop',
            'currentHst',
            'maxRowHst',
            'tableRows',
            'medicines',
            'totalActivities',
            'completedActivities',
            'pendingActivities',
            'completionRate'
        ));
    }

    public function storeActivity(Request $request, Crop $crop): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'aplikasi_obat' => 'nullable|string|max:2000',
            'sasaran' => 'nullable|string|max:500',
            'keterangan' => 'nullable|string|max:1000',
            'target_hst' => 'nullable|integer|min:0',
            'tanggal_kegiatan' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $plantDate = Carbon::parse($crop->tanggal_tanam)->startOfDay();

        // Hitung target_hst otomatis jika tanggal_kegiatan yang diisi, atau gunakan target_hst
        if ($request->filled('target_hst')) {
            $targetHst = (int) $request->input('target_hst');
        } elseif ($request->filled('tanggal_kegiatan')) {
            $activityDate = Carbon::parse($request->input('tanggal_kegiatan'))->startOfDay();
            $targetHst = max(0, (int) $plantDate->diffInDays($activityDate, false));
        } else {
            $targetHst = $crop->current_hst;
        }

        $keteranganFinal = $validated['keterangan'] ?? $validated['catatan'] ?? null;

        $crop->activities()->create([
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'aplikasi_obat' => $validated['aplikasi_obat'] ?? null,
            'sasaran' => $validated['sasaran'] ?? null,
            'target_hst' => $targetHst,
            'status' => 'Belum',
            'keterangan' => $keteranganFinal,
            'catatan' => $keteranganFinal,
        ]);

        $msg = "Kegiatan '{$validated['nama_kegiatan']}' berhasil dicatat pada HST {$targetHst}.";

        if ($request->input('redirect_to') === 'show') {
            return redirect('/kalender-hst/tanaman/' . $crop->id)->with('success', $msg);
        }

        return redirect()->back(fallback: '/kalender-hst?tab=aktif')->with('success', $msg);
    }

    public function updateActivity(Request $request, CropActivity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'aplikasi_obat' => 'nullable|string|max:2000',
            'sasaran' => 'nullable|string|max:500',
            'keterangan' => 'nullable|string|max:1000',
            'target_hst' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $keteranganFinal = $validated['keterangan'] ?? $validated['catatan'] ?? null;

        $activity->update([
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'aplikasi_obat' => $validated['aplikasi_obat'] ?? null,
            'sasaran' => $validated['sasaran'] ?? null,
            'target_hst' => (int) $validated['target_hst'],
            'keterangan' => $keteranganFinal,
            'catatan' => $keteranganFinal,
        ]);

        return redirect()->back()->with('success', 'Data kegiatan perawatan berhasil diperbarui.');
    }

    public function toggleActivityStatus(Request $request, CropActivity $activity)
    {
        $newStatus = $activity->status === 'Selesai' ? 'Belum' : 'Selesai';
        $activity->update([
            'status' => $newStatus,
            'tanggal_selesai' => $newStatus === 'Selesai' ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Status kegiatan berhasil diperbarui.',
            ]);
        }

        return redirect()->back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function destroyActivity(CropActivity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->back()->with('success', 'Kegiatan perawatan berhasil dihapus.');
    }
}
