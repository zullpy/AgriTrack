<?php

namespace App\Http\Controllers;

use App\Models\PlantCatalog;
use Database\Seeders\PlantCatalogSeeder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
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

        return view('dashboard.index', compact('catalogs'));
    }
}
