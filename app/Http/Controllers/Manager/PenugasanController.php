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

        $expectedSkills = match ($laporan->category) {
            'mekanik' => ['Mekanik'],
            'elektrik' => ['Elektrik'],
            'instrumentasi' => ['Instrumentasi'],
            default => ['Mekanik', 'Elektrik', 'Instrumentasi'],
        };

        $teknisiList = User::where('role', 'teknisi')
            ->where('is_active', true)
            ->whereIn('bagian', $expectedSkills)
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
            'technician_id' => ['required', 'integer', 'exists:users,id'],
            'assignment_priority' => ['required', 'in:tinggi,sedang,rendah'],
            'work_start_time' => ['required'],
            'assignment_note' => ['nullable', 'string'],
        ], [
            'technician_id.required' => 'Teknisi wajib dipilih.',
            'work_start_time.required' => 'Waktu pengerjaan wajib diisi.',
        ]);

        $teknisi = User::where('id', $data['technician_id'])
            ->where('role', 'teknisi')
            ->where('is_active', true)
            ->first();

        abort_unless($teknisi, 422, 'Teknisi yang dipilih tidak aktif atau bukan akun Teknisi.');

        $expectedSkills = match ($laporan->category) {
            'mekanik' => ['Mekanik'],
            'elektrik' => ['Elektrik'],
            'instrumentasi' => ['Instrumentasi'],
            default => ['Mekanik', 'Elektrik', 'Instrumentasi'],
        };
        abort_unless(in_array($teknisi->bagian, $expectedSkills, true), 422, 'Keahlian teknisi tidak sesuai dengan kategori gangguan laporan.');

        $laporan->update([
            'technician_id' => $data['technician_id'],
            'assignment_priority' => $data['assignment_priority'],
            'work_start_time' => $data['work_start_time'],
            'assignment_note' => $data['assignment_note'] ?? null,
            'assigned_at' => now(),
            'status' => 'ditugaskan',
        ]);

        $laporan->recordActivity($request->user(), 'ditugaskan', 'menunggu_penugasan', 'ditugaskan', 'Laporan ditugaskan kepada '.$teknisi->name.'.', ['technician_id' => $teknisi->id]);

        return redirect()
            ->route('manager.dashboard')
            ->with('success', "Laporan {$laporan->kode} berhasil ditugaskan ke ".$teknisi->name.'.');
    }
}
