<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function show(Laporan $laporan): View
    {
        $laporan->load([
            'operator',
            'manager',
            'teknisi',
            'activities.user',
        ]);

        return view('manager.detail_laporan', [
            'laporan' => $laporan,
        ]);
    }
}
