<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StepController extends Controller
{
    public function index(): View
    {
        return view('steps.index');
    }

    public function pengolahanTanah(): View
    {
        return view('steps.pengolahan-tanah');
    }

    public function penanamanBibit(): View
    {
        return view('steps.penanaman-bibit');
    }
}
