<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoriController extends Controller
{
    public function index(Request $request): View
    {
        $query = Laporan::with(['operator', 'teknisi'])
            ->where('status', 'selesai')
            ->latest('final_validated_at');

        if ($mesin = $request->query('mesin')) {
            $query->where('machine', $mesin);
        }

        if ($cari = $request->query('cari')) {
            $query->where('kode', 'like', "%{$cari}%");
        }

        if ($bulan = $request->query('bulan')) {
            $query->whereMonth('final_validated_at', $bulan);
        }

        $laporan = $query->paginate(10)->withQueryString();

        return view('manager.histori', [
            'laporan' => $laporan,
            'stationMachines' => config('sippm.station_machines'),
        ]);
    }
}
