@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Detail Monitoring Laporan')
@section('heroTitle', 'Detail Monitoring Laporan')
@section('heroSub', 'Pantau seluruh informasi laporan, penugasan, hasil penanganan, dan jejak aktivitas maintenance.')
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
        <div class="kv"><span class="k">Area</span><span class="v">{{ $laporan->stationLabel() }}</span></div>
        <div class="kv"><span class="k">Kategori</span><span class="v">{{ $laporan->categoryLabel() }}</span></div>
        <div class="kv"><span class="k">Kondisi / Abnormalitas</span><span class="v">{{ $laporan->condition_text }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Operator</span><span class="v">{{ $laporan->operator->name }}</span></div>
        <div class="kv"><span class="k">Tanggal / Waktu</span><span class="v mono">{{ $laporan->incident_date->translatedFormat('d M Y') }}, {{ substr($laporan->incident_time, 0, 5) }}</span></div>
        <div class="kv"><span class="k">Urgensi</span><span class="v urg {{ $laporan->urgencyClass() }}">{{ $laporan->urgencyLabel() }}</span></div>
        <div class="kv"><span class="k">Dibuat</span><span class="v mono">{{ $laporan->created_at->translatedFormat('d M Y, H:i') }}</span></div>
      </div>
    </div>

    <div style="margin-top:16px;">
      <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:6px;">Deskripsi Masalah</div>
      <div style="font-size:13.5px;">{{ $laporan->description }}</div>
    </div>

    @if ($laporan->photo_before)
      <div style="margin-top:16px;">
        <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:8px;">Foto Sebelum Perbaikan</div>
        <a href="{{ asset('storage/'.$laporan->photo_before) }}" target="_blank" class="photo-swatch">Lihat dokumentasi foto sebelum</a>
      </div>
    @endif

    @if ($laporan->rejection_reason)
      <div class="callout danger" style="margin-top:16px;"><b>Alasan Penolakan:</b> {{ $laporan->rejection_reason }}</div>
    @endif

    @if ($laporan->technician_id)
      <div style="margin-top:18px;">
        <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:8px;">Penugasan Maintenance</div>
        <div class="detail-grid">
          <div>
            <div class="kv"><span class="k">Teknisi</span><span class="v">{{ $laporan->teknisi->name ?? '-' }}</span></div>
            <div class="kv"><span class="k">Keahlian</span><span class="v">{{ $laporan->teknisi->bagian ?? '-' }}</span></div>
            <div class="kv"><span class="k">Prioritas</span><span class="v">{{ ucfirst($laporan->assignment_priority ?? '-') }}</span></div>
          </div>
          <div>
            <div class="kv"><span class="k">Ditugaskan</span><span class="v mono">{{ optional($laporan->assigned_at)->translatedFormat('d M Y, H:i') ?? '-' }}</span></div>
            <div class="kv"><span class="k">Mulai Aktual</span><span class="v mono">{{ optional($laporan->started_at)->translatedFormat('d M Y, H:i') ?? '-' }}</span></div>
            <div class="kv"><span class="k">Waktu Rencana Mulai</span><span class="v mono">{{ $laporan->work_start_time ?? '-' }}</span></div>
          </div>
        </div>
      </div>
    @endif

    @if (in_array($laporan->status, ['menunggu_validasi_akhir', 'selesai']))
      <div style="margin-top:18px;">
        <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:8px;">Hasil Penanganan</div>
        <div class="detail-grid">
          <div>
            <div class="kv"><span class="k">Hasil Pemeriksaan</span><span class="v">{{ $laporan->inspection_result }}</span></div>
            <div class="kv"><span class="k">Penyebab</span><span class="v">{{ $laporan->root_cause }}</span></div>
            <div class="kv"><span class="k">Tindakan</span><span class="v">{{ $laporan->action_taken }}</span></div>
          </div>
          <div>
            <div class="kv"><span class="k">Komponen</span><span class="v">{{ $laporan->components_text ?: '-' }}</span></div>
            <div class="kv"><span class="k">Waktu Selesai</span><span class="v mono">{{ $laporan->work_end_time ?? '-' }}</span></div>
            <div class="kv"><span class="k">Downtime</span><span class="v mono">{{ $laporan->downtimeLabel() ?? '-' }}</span></div>
          </div>
        </div>
      </div>

      @if ($laporan->photo_after)
        <div style="margin-top:16px;">
          <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:8px;">Foto Setelah Perbaikan</div>
          <a href="{{ asset('storage/'.$laporan->photo_after) }}" target="_blank" class="photo-swatch">Lihat dokumentasi foto sesudah</a>
        </div>
      @endif
    @endif

    <div style="margin-top:20px;">
      <div class="panel-head" style="padding-left:0;padding-right:0;"><h3>Riwayat Aktivitas</h3></div>
      <div class="table-scroll">
        <table>
          <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Status</th><th>Keterangan</th></tr></thead>
          <tbody>
          @forelse ($laporan->activities as $activity)
            <tr>
              <td class="mono">{{ $activity->created_at->translatedFormat('d M Y, H:i') }}</td>
              <td>{{ $activity->user->name ?? 'Sistem' }}</td>
              <td>{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</td>
              <td>
                {{ $activity->from_status ? AppModelsLaporan::statusOptions()[$activity->from_status] ?? $activity->from_status : '-' }}
                @if ($activity->to_status)
                  → {{ AppModelsLaporan::statusOptions()[$activity->to_status] ?? $activity->to_status }}
                @endif
              </td>
              <td>{{ $activity->description ?: '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" style="text-align:center;color:var(--ink-soft);padding:20px;">Belum ada aktivitas tercatat.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="action-bar">
      <a class="btn btn-outline" href="{{ route('manager.dashboard') }}">Kembali ke Dashboard</a>
    </div>
  </div>
</div>
@endsection
