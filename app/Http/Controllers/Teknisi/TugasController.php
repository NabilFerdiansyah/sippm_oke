<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TugasController extends Controller
{
    public function show(Request $request, Laporan $laporan): View
    {
        abort_unless($laporan->isAssignedTo($request->user()), 403);

        return view('teknisi.detail_tugas', [
            'laporan' => $laporan,
        ]);
    }

    public function mulai(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->isAssignedTo($request->user()), 403);
        abort_unless($laporan->status === 'ditugaskan', 404);

        $laporan->update([
            'status' => 'dikerjakan',
            'started_at' => now(),
        ]);

        $laporan->recordActivity($request->user(), 'dimulai', 'ditugaskan', 'dikerjakan', 'Teknisi memulai pemeriksaan.');

        return redirect()
            ->route('teknisi.tugas.hasil.edit', $laporan)
            ->with('success', 'Pemeriksaan dimulai. Silakan isi hasil penanganan setelah selesai.');
    }
}
