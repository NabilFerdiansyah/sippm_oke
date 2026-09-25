<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ValidasiAkhirController extends Controller
{
    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->status === 'menunggu_validasi_akhir', 404);

        return view('manager.validasi_akhir', [
            'laporan' => $laporan,
        ]);
    }

    public function approve(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'menunggu_validasi_akhir', 404);

        $data = $request->validate([
            'final_manager_note' => ['nullable', 'string', 'max:500'],
        ]);

        $laporan->update([
            'status' => 'selesai',
            'final_manager_note' => $data['final_manager_note'] ?? null,
            'final_validated_at' => now(),
        ]);

        $laporan->recordActivity($request->user(), 'diselesaikan', 'menunggu_validasi_akhir', 'selesai', 'Hasil penanganan disetujui oleh Manager.');

        return redirect()
            ->route('manager.dashboard')
            ->with('success', "Laporan {$laporan->kode} disetujui dan dinyatakan selesai.");
    }

    public function kembalikan(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'menunggu_validasi_akhir', 404);

        $data = $request->validate([
            'final_manager_note' => ['required', 'string', 'max:500'],
        ], [
            'final_manager_note.required' => 'Catatan/alasan pengembalian wajib diisi.',
        ]);

        $laporan->update([
            'status' => 'ditugaskan',
            'final_manager_note' => $data['final_manager_note'],
            'submitted_for_validation_at' => null,
        ]);

        $laporan->recordActivity($request->user(), 'dikembalikan', 'menunggu_validasi_akhir', 'ditugaskan', $data['final_manager_note']);

        return redirect()
            ->route('manager.dashboard')
            ->with('success', "Laporan {$laporan->kode} dikembalikan ke teknisi untuk perbaikan hasil pekerjaan.");
    }
}
