@extends('layouts.app')

@section('crumb', 'Operator')
@section('pageTitle', 'Detail Laporan')
@section('heroTitle', 'Detail Laporan')
@section('heroSub', 'Pantau status dan riwayat penanganan atas laporan yang telah Anda kirim.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Detail Laporan <span class="mono" style="font-weight:400;">{{ $laporan->kode }}</span></h3>
    <span class="badge {{ $laporan->statusBadgeClass() }}">{{ $laporan->statusLabel() }}</span>
  </div>
  <div class="panel-body">
    <div class="detail-grid">
      <div>
        <div class="kv"><span class="k">Mesin</span><span class="v">{{ $laporan->machine }}</span></div>
        <div class="kv"><span class="k">Kategori</span><span class="v">{{ $laporan->categoryLabel() }}</span></div>
        <div class="kv"><span class="k">Urgensi</span><span class="v urg {{ $laporan->urgencyClass() }}">{{ $laporan->urgencyLabel() }}</span></div>
        <div class="kv"><span class="k">Tanggal / Waktu Kejadian</span><span class="v mono">{{ $laporan->incident_date->translatedFormat('d M Y') }}, {{ substr($laporan->incident_time, 0, 5) }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Dilaporkan Oleh</span><span class="v">{{ $laporan->operator->name }}</span></div>
        <div class="kv"><span class="k">Waktu Laporan Dikirim</span><span class="v mono">{{ $laporan->created_at->translatedFormat('d M Y, H:i') }}</span></div>
        <div class="kv"><span class="k">Status Saat Ini</span><span class="v">{{ $laporan->statusLabel() }}</span></div>
      </div>
    </div>
    <div style="margin-top:16px;">
      <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:6px;">Deskripsi Masalah</div>
      <div style="font-size:13.5px;">{{ $laporan->description }}</div>
    </div>

    @if ($laporan->status === 'ditolak')
      <div class="callout danger" style="margin-top:18px;">
        <b>Alasan Penolakan:</b> {{ $laporan->rejection_reason }}
      </div>
    @endif

    @if (in_array($laporan->status, ['ditugaskan', 'dikerjakan', 'menunggu_validasi_akhir', 'selesai']))
      <div style="margin-top:16px;">
        <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:6px;">Penugasan</div>
        <div class="detail-grid">
          <div>
            <div class="kv"><span class="k">Teknisi</span><span class="v">{{ $laporan->teknisi->name ?? '-' }}</span></div>
            <div class="kv"><span class="k">Prioritas</span><span class="v urg {{ $laporan->assignment_priority }}">{{ ucfirst($laporan->assignment_priority) }}</span></div>
          </div>
          <div>
            <div class="kv"><span class="k">Waktu Pengerjaan Dimulai</span><span class="v mono">{{ $laporan->work_start_time }}</span></div>
            <div class="kv"><span class="k">Ditugaskan Pada</span><span class="v mono">{{ optional($laporan->assigned_at)->translatedFormat('d M Y, H:i') }}</span></div>
          </div>
        </div>
      </div>
    @endif

    @if (in_array($laporan->status, ['menunggu_validasi_akhir', 'selesai']))
      <div style="margin-top:16px;">
        <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:6px;">Hasil Penanganan Teknisi</div>
        <div class="detail-grid">
          <div>
            <div class="kv"><span class="k">Penyebab Kerusakan</span><span class="v">{{ $laporan->root_cause }}</span></div>
            <div class="kv"><span class="k">Tindakan Dilakukan</span><span class="v">{{ $laporan->action_taken }}</span></div>
            <div class="kv"><span class="k">Waktu Selesai</span><span class="v mono">{{ $laporan->work_end_time }}</span></div>
          </div>
          <div>
            <div class="kv"><span class="k">Downtime</span><span class="v mono">{{ $laporan->downtimeLabel() ?? '-' }}</span></div>
            <div class="kv"><span class="k">Komponen</span><span class="v">{{ $laporan->components_text ?: '-' }}</span></div>
          </div>
        </div>
      </div>
    @endif

    @if ($laporan->status === 'selesai')
      <div class="callout success" style="margin-top:18px;">
        Laporan dinyatakan <b>selesai</b> oleh Manager pada {{ optional($laporan->final_validated_at)->translatedFormat('d M Y, H:i') }}.
        @if ($laporan->final_manager_note)
          <br>Catatan: {{ $laporan->final_manager_note }}
        @endif
      </div>
    @endif
  </div>
</div>
@endsection
