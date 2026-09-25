<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ValidasiController extends Controller
{
    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->status === 'menunggu_validasi', 404);

        return view('manager.validasi', [
            'laporan' => $laporan,
        ]);
    }

    public function approve(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'menunggu_validasi', 404);

        $laporan->update([
            'status' => 'menunggu_penugasan',
            'manager_id' => $request->user()->id,
            'validated_at' => now(),
        ]);

        $laporan->recordActivity($request->user(), 'divalidasi', 'menunggu_validasi', 'menunggu_penugasan', 'Laporan diterima oleh Manager.');

        return redirect()
            ->route('manager.laporan.penugasan', $laporan)
            ->with('success', "Laporan {$laporan->kode} diterima. Silakan tugaskan teknisi.");
    }

    public function reject(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'menunggu_validasi', 404);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $laporan->update([
            'status' => 'ditolak',
            'manager_id' => $request->user()->id,
            'validated_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        $laporan->recordActivity($request->user(), 'ditolak', 'menunggu_validasi', 'ditolak', $data['rejection_reason']);

        return redirect()
            ->route('manager.dashboard')
            ->with('success', "Laporan {$laporan->kode} ditolak.");
    }
}
