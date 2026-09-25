<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenugasanController extends Controller
{
    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->status === 'menunggu_penugasan', 404);

        $teknisiList = User::where('role', 'teknisi')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('manager.penugasan', [
            'laporan' => $laporan,
            'teknisiList' => $teknisiList,
        ]);
    }

    public function store(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'menunggu_penugasan', 404);

        $data = $request->validate([
            'technician_id' => ['required', 'exists:users,id'],
            'assignment_priority' => ['required', 'in:tinggi,sedang,rendah'],
            'work_start_time' => ['required'],
            'assignment_note' => ['nullable', 'string'],
        ], [
            'technician_id.required' => 'Teknisi wajib dipilih.',
            'work_start_time.required' => 'Waktu pengerjaan wajib diisi.',
        ]);

        $laporan->update([
            'technician_id' => $data['technician_id'],
            'assignment_priority' => $data['assignment_priority'],
            'work_start_time' => $data['work_start_time'],
            'assignment_note' => $data['assignment_note'] ?? null,
            'assigned_at' => now(),
            'status' => 'ditugaskan',
        ]);

        return redirect()
            ->route('manager.dashboard')
            ->with('success', "Laporan {$laporan->kode} berhasil ditugaskan ke ".$laporan->teknisi->name.'.');
    }
}
