<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HasilController extends Controller
{
    /**
     * Nav "Input Hasil Penanganan": arahkan ke form hasil tugas aktif
     * teknisi yang sedang login (jika ada satu), jika tidak kembali ke
     * dashboard dengan pesan informatif.
     */
    public function index(Request $request): RedirectResponse
    {
        $aktif = $request->user()->laporanDitugaskan()
            ->where('status', 'dikerjakan')
            ->orderBy('assigned_at')
            ->first();

        if (! $aktif) {
            return redirect()
                ->route('teknisi.dashboard')
                ->with('warning', 'Tidak ada tugas aktif saat ini untuk diisi hasil penanganannya.');
        }

        return redirect()->route('teknisi.tugas.hasil.edit', $aktif);
    }

    public function edit(Request $request, Laporan $laporan): View
    {
        abort_unless($laporan->isAssignedTo($request->user()), 403);
        abort_unless($laporan->status === 'dikerjakan', 404);

        return view('teknisi.form_hasil', [
            'laporan' => $laporan,
        ]);
    }

    public function update(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->isAssignedTo($request->user()), 403);
        abort_unless($laporan->status === 'dikerjakan', 404);

        $data = $request->validate([
            'inspection_result' => ['required', 'string'],
            'root_cause' => ['required', 'string', 'max:255'],
            'action_taken' => ['required', 'string', 'max:255'],
            'components_text' => ['nullable', 'string'],
            'work_end_time' => ['required'],
            'additional_note' => ['nullable', 'string'],
            'photo_after' => ['nullable', 'image', 'max:4096'],
        ], [
            'inspection_result.required' => 'Hasil pemeriksaan wajib diisi.',
            'root_cause.required' => 'Penyebab kerusakan wajib diisi.',
            'action_taken.required' => 'Tindakan yang dilakukan wajib diisi.',
            'work_end_time.required' => 'Waktu selesai penanganan wajib diisi.',
        ]);

        if ($request->hasFile('photo_after')) {
            $data['photo_after'] = $request->file('photo_after')->store('laporan/sesudah', 'public');
        }

        $laporan->update([
            ...$data,
            'status' => 'menunggu_validasi_akhir',
            'started_at' => $laporan->started_at ?? now(),
            'submitted_for_validation_at' => now(),
        ]);

        $laporan->recordActivity($request->user(), 'dikirim_validasi', 'dikerjakan', 'menunggu_validasi_akhir', 'Hasil penanganan dikirim untuk validasi akhir Manager.');

        return redirect()
            ->route('teknisi.dashboard')
            ->with('success', "Hasil penanganan {$laporan->kode} berhasil dikirim untuk validasi akhir Manager.");
    }
}
