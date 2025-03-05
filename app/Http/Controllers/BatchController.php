<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function fix()
    {
        $batches = Batch::all();
        return view('exams.batch.fix', compact('batches'));
    }
}
