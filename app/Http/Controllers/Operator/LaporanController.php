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

        $laporan = Laporan::create([
            ...$data,
            'kode' => Laporan::generateKode(),
            'operator_id' => $request->user()->id,
            'status' => 'menunggu_validasi',
        ]);

        return redirect()
            ->route('operator.laporan.show', $laporan)
            ->with('success', "Laporan {$laporan->kode} berhasil dikirim dan menunggu validasi Manager.");
    }

    public function show(Request $request, Laporan $laporan): View
    {
        abort_unless($laporan->isOwnedBy($request->user()), 403);

        return view('operator.detail_laporan', [
            'laporan' => $laporan,
        ]);
    }
}
