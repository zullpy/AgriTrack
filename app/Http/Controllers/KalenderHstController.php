<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\CropActivity;
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
            'tanggal_tanam' => 'required|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $crop->update($validated);

        return redirect('/kalender-hst?tab=aktif')->with('success', 'Informasi tanaman berhasil diperbarui.');
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

        // Tentukan batas atas HST yang ditampilkan:
        if ($crop->status === 'Sudah Dipanen') {
            $harvestHst = (int) ($crop->total_hst_panen ?? 0);
            $maxHst = $harvestHst;

            // Kelompokkan kegiatan per HST
            $activitiesByHst = $crop->activities->groupBy('target_hst');

            // Untuk tanaman yang sudah dipanen, tampilkan hari-hari penting (Hari Tanam, semua kegiatan, dan hari panen)
            $meaningfulHsts = collect([0, $harvestHst])
                ->merge($crop->activities->pluck('target_hst'))
                ->unique()
                ->sort();

            $timelineDays = [];
            foreach ($meaningfulHsts as $hst) {
                $dayDate = $plantDate->copy()->addDays($hst);
                $isHarvestDay = ($hst === $harvestHst);

                $timelineDays[] = [
                    'hst' => $hst,
                    'date' => $dayDate,
                    'is_today' => false,
                    'is_past' => true,
                    'is_future' => false,
                    'is_harvest' => $isHarvestDay,
                    'activities' => $activitiesByHst->get($hst, collect()),
                ];
            }
        } else {
            $maxHst = $currentHst + 1;

            // Kelompokkan kegiatan per HST
            $activitiesByHst = $crop->activities->groupBy('target_hst');

            // Buat daftar timeline per HST dari HST 0 sampai BESOK
            $timelineDays = [];
            for ($hst = 0; $hst <= $maxHst; $hst++) {
                $dayDate = $plantDate->copy()->addDays($hst);
                $isToday = $crop->status === 'Sedang Ditanam' && $dayDate->isToday();
                $isPast = $crop->status === 'Sedang Ditanam' ? ($dayDate->isPast() && !$dayDate->isToday()) : true;
                $isFuture = $crop->status === 'Sedang Ditanam' && $dayDate->isFuture();

                $timelineDays[] = [
                    'hst' => $hst,
                    'date' => $dayDate,
                    'is_today' => $isToday,
                    'is_past' => $isPast,
                    'is_future' => $isFuture,
                    'is_harvest' => false,
                    'activities' => $activitiesByHst->get($hst, collect()),
                ];
            }

            // Jika ada jadwal kegiatan manual yang pernah diinput lebih dari besok (HST > maxHst),
            // tampilkan hanya hari-hari tersebut tanpa membuat puluhan kartu kosong di antaranya
            $futureActivityHsts = $crop->activities
                ->pluck('target_hst')
                ->filter(fn ($h) => $h > $maxHst)
                ->unique()
                ->sort();

            foreach ($futureActivityHsts as $fHst) {
                $dayDate = $plantDate->copy()->addDays($fHst);
                $timelineDays[] = [
                    'hst' => $fHst,
                    'date' => $dayDate,
                    'is_today' => false,
                    'is_past' => false,
                    'is_future' => true,
                    'is_harvest' => false,
                    'activities' => $activitiesByHst->get($fHst, collect()),
                ];
            }
        }

        $totalActivities = $crop->activities->count();
        $completedActivities = $crop->activities->where('status', 'Selesai')->count();
        $pendingActivities = $totalActivities - $completedActivities;
        $completionRate = $totalActivities > 0 ? (int) round(($completedActivities / $totalActivities) * 100) : 0;

        return view('kalender-hst.show', compact(
            'crop',
            'currentHst',
            'maxHst',
            'timelineDays',
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

        $crop->activities()->create([
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'target_hst' => $targetHst,
            'status' => 'Belum',
            'catatan' => $validated['catatan'] ?? null,
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
            'target_hst' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $activity->update($validated);

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
