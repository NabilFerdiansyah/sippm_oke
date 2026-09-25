<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'laporan_baru' => Laporan::where('status', 'menunggu_validasi')->count(),
            'sedang_ditangani' => Laporan::whereIn('status', ['menunggu_penugasan', 'ditugaskan', 'dikerjakan'])->count(),
            'menunggu_validasi_akhir' => Laporan::where('status', 'menunggu_validasi_akhir')->count(),
            'selesai_bulan_ini' => Laporan::where('status', 'selesai')
                ->whereMonth('final_validated_at', now()->month)
                ->whereYear('final_validated_at', now()->year)
                ->count(),
            'prioritas_tinggi' => Laporan::where('urgency', 'tinggi')
                ->whereNotIn('status', ['selesai', 'ditolak'])
                ->count(),
        ];

        $query = Laporan::with(['operator'])->latest();

        if ($mesin = $request->query('mesin')) {
            $query->where('machine', $mesin);
        }

        if ($area = $request->query('area')) {
            $query->where('station', $area);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($urgensi = $request->query('urgensi')) {
            $query->where('urgency', $urgensi);
        }

        if ($tanggal = $request->query('tanggal')) {
            $query->whereDate('incident_date', $tanggal);
        }

        $laporan = $query->paginate(10)->withQueryString();

        return view('manager.dashboard', [
            'stats' => $stats,
            'laporan' => $laporan,
            'stationMachines' => config('sippm.station_machines'),
            'stationLabels' => config('sippm.station_labels'),
            'statusOptions' => Laporan::statusOptions(),
        ]);
    }
}
