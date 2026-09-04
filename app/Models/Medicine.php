<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'jenis', 'tanaman_sasaran', 'dosis_anjuran', 'interval_aplikasi', 'catatan_keamanan', 'stok'])]
class Medicine extends Model
{
    protected function casts(): array
    {
        return [
            'stok' => 'integer',
        ];
    }
}
