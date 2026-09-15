<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StepController extends Controller
{
    public function index(): View
    {
        return view('steps.index');
    }
}
