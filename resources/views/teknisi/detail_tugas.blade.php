@extends('layouts.app')

@section('crumb', 'Teknisi')
@section('pageTitle', 'Detail Tugas')
@section('heroTitle', 'Detail Tugas')
@section('heroSub', 'Periksa detail laporan sebelum memulai pemeriksaan di lapangan.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Detail Tugas <span class="mono" style="font-weight:400;">{{ $laporan->kode }}</span></h3>
    <span class="badge {{ $laporan->statusBadgeClass() }}">{{ $laporan->statusLabelRingkas() }}</span>
  </div>
  <div class="panel-body">
    <div class="detail-grid">
      <div>
        <div class="kv"><span class="k">Mesin</span><span class="v">{{ $laporan->machine }}</span></div>
        <div class="kv"><span class="k">Area</span><span class="v">{{ $laporan->stationLabel() }}</span></div>
        <div class="kv"><span class="k">Operator Pelapor</span><span class="v">{{ $laporan->operator->name }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Waktu Kejadian</span><span class="v mono">{{ $laporan->incident_date->translatedFormat('d M') }}, {{ substr($laporan->incident_time, 0, 5) }}</span></div>
        <div class="kv"><span class="k">Urgensi</span><span class="v urg {{ $laporan->urgencyClass() }}">{{ $laporan->urgencyLabel() }}</span></div>
        <div class="kv"><span class="k">Catatan Manager</span><span class="v">{{ $laporan->assignment_note ?: '-' }}</span></div>
      </div>
    </div>
    <div style="margin-top:14px;font-size:13.5px;">
      <strong>Deskripsi Masalah:</strong> {{ $laporan->description }}
    </div>

    @if ($laporan->status === 'ditugaskan')
      <form method="POST" action="{{ route('teknisi.tugas.mulai', $laporan) }}">
        @csrf
        <div class="action-bar">
          <button class="btn btn-amber" type="submit">&#9654; Mulai Pemeriksaan</button>
        </div>
      </form>
    @elseif ($laporan->status === 'dikerjakan')
      <div class="action-bar">
        <a class="btn btn-amber" href="{{ route('teknisi.tugas.hasil.edit', $laporan) }}">Lanjutkan Input Hasil Penanganan</a>
      </div>
    @elseif ($laporan->status === 'menunggu_validasi_akhir')
      <div class="callout warn" style="margin-top:16px;">Hasil penanganan sudah dikirim, menunggu Validasi Akhir dari Manager.</div>
    @endif
  </div>
</div>
@endsection
