<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(Request $request): View
    {
        $laporan = $request->user()->laporanDitugaskan()
            ->where('status', 'selesai')
            ->latest('final_validated_at')
            ->paginate(10);

        return view('teknisi.riwayat', [
            'laporan' => $laporan,
        ]);
    }
}
