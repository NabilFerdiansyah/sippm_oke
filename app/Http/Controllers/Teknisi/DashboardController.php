<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $base = $user->laporanDitugaskan();

        $stats = [
            'tugas_aktif' => (clone $base)->whereIn('status', ['ditugaskan', 'dikerjakan'])->count(),
            'menunggu_validasi' => (clone $base)->where('status', 'menunggu_validasi_akhir')->count(),
            'selesai_bulan_ini' => (clone $base)->where('status', 'selesai')
                ->whereMonth('final_validated_at', now()->month)
                ->whereYear('final_validated_at', now()->year)
                ->count(),
        ];

        $tugasAktif = (clone $base)
            ->whereIn('status', ['ditugaskan', 'dikerjakan', 'menunggu_validasi_akhir'])
            ->orderBy('assigned_at')
            ->get();

        return view('teknisi.dashboard', [
            'stats' => $stats,
            'tugasAktif' => $tugasAktif,
        ]);
    }
}
