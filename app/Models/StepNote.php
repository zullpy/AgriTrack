<?php

namespace App\Models;

use Database\Factories\StepNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepNote extends Model
{
    /** @use HasFactory<StepNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'judul',
        'catatan',
    ];
}
