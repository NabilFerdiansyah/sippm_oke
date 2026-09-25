<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function create(): View
    {
        return view('operator.buat_laporan', [
            'editing' => false,
            'laporan' => null,
            'stationMachines' => config('sippm.station_machines'),
            'conditionOptions' => config('sippm.condition_options'),
            'stationLabels' => config('sippm.station_labels'),
            'categoryLabels' => config('sippm.category_labels'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'station' => ['required', 'in:gilingan,boiler,pemurnian,penguapan'],
            'machine' => ['required', 'string', 'max:150'],
            'incident_date' => ['required', 'date'],
            'incident_time' => ['required'],
            'category' => ['required', 'in:mekanik,elektrik,instrumentasi,proses'],
            'urgency' => ['required', 'in:tinggi,sedang,rendah'],
            'condition_text' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ], [
            'station.required' => 'Stasiun wajib dipilih.',
            'machine.required' => 'Mesin wajib dipilih.',
            'incident_date.required' => 'Tanggal kejadian wajib diisi.',
            'incident_time.required' => 'Waktu kejadian wajib diisi.',
            'condition_text.required' => 'Kondisi/Abnormalitas wajib dipilih.',
            'description.required' => 'Deskripsi masalah wajib diisi.',
        ]);

        abort_unless(in_array($data['machine'], config("sippm.station_machines.{$data['station']}", []), true), 422, 'Mesin tidak sesuai dengan stasiun yang dipilih.');
        abort_unless(in_array($data['condition_text'], config("sippm.condition_options.{$data['machine']}.{$data['category']}", []), true), 422, 'Kondisi/abnormalitas tidak sesuai dengan mesin dan kategori yang dipilih.');

        $laporan = Laporan::create([
            ...$data,
            'kode' => Laporan::generateKode(),
            'operator_id' => $request->user()->id,
            'status' => 'menunggu_validasi',
        ]);

        $laporan->recordActivity($request->user(), 'dibuat', null, 'menunggu_validasi', 'Laporan dibuat oleh Operator.');

        return redirect()
            ->route('operator.laporan.show', $laporan)
            ->with('success', "Laporan {$laporan->kode} berhasil dikirim dan menunggu validasi Manager.");
    }

    public function edit(Request $request, Laporan $laporan): View
    {
        abort_unless($laporan->isOwnedBy($request->user()), 403);
        abort_unless($laporan->status === 'ditolak', 404);

        return view('operator.buat_laporan', [
            'editing' => true,
            'laporan' => $laporan,
            'stationMachines' => config('sippm.station_machines'),
            'conditionOptions' => config('sippm.condition_options'),
            'stationLabels' => config('sippm.station_labels'),
            'categoryLabels' => config('sippm.category_labels'),
        ]);
    }

    public function update(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->isOwnedBy($request->user()), 403);
        abort_unless($laporan->status === 'ditolak', 404);

        $data = $request->validate([
            'station' => ['required', 'in:gilingan,boiler,pemurnian,penguapan'],
            'machine' => ['required', 'string', 'max:150'],
            'incident_date' => ['required', 'date'],
            'incident_time' => ['required'],
            'category' => ['required', 'in:mekanik,elektrik,instrumentasi,proses'],
            'urgency' => ['required', 'in:tinggi,sedang,rendah'],
            'condition_text' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'photo_before' => ['nullable', 'image', 'max:4096'],
        ]);

        abort_unless(in_array($data['machine'], config("sippm.station_machines.{$data['station']}", []), true), 422, 'Mesin tidak sesuai dengan stasiun yang dipilih.');
        abort_unless(in_array($data['condition_text'], config("sippm.condition_options.{$data['machine']}.{$data['category']}", []), true), 422, 'Kondisi/abnormalitas tidak sesuai dengan mesin dan kategori yang dipilih.');

        $oldStatus = $laporan->status;
        $laporan->update([
            ...$data,
            'status' => 'menunggu_validasi',
            'rejection_reason' => null,
            'manager_id' => null,
            'validated_at' => null,
        ]);

        $laporan->recordActivity($request->user(), 'direvisi', $oldStatus, 'menunggu_validasi', 'Laporan diperbaiki dan dikirim ulang oleh Operator.');

        return redirect()
            ->route('operator.laporan.show', $laporan)
            ->with('success', "Laporan {$laporan->kode} berhasil direvisi dan dikirim ulang untuk validasi Manager.");
    }

    public function show(Request $request, Laporan $laporan): View
    {
        abort_unless($laporan->isOwnedBy($request->user()), 403);

        return view('operator.detail_laporan', [
            'laporan' => $laporan,
        ]);
    }
}
