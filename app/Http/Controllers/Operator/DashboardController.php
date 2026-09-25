<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $base = $user->laporanDibuat();

        $stats = [
            'total' => (clone $base)->count(),
            'menunggu_validasi' => (clone $base)->where('status', 'menunggu_validasi')->count(),
            'sedang_ditangani' => (clone $base)->whereIn('status', ['menunggu_penugasan', 'ditugaskan', 'dikerjakan', 'menunggu_validasi_akhir'])->count(),
            'selesai' => (clone $base)->where('status', 'selesai')->count(),
            'ditolak' => (clone $base)->where('status', 'ditolak')->count(),
        ];

        $laporanTerbaru = (clone $base)->latest()->take(10)->get();

        return view('operator.dashboard', [
            'stats' => $stats,
            'laporanTerbaru' => $laporanTerbaru,
        ]);
    }
}
