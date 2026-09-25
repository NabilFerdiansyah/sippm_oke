@extends('layouts.app')

@section('crumb', 'Teknisi')
@section('pageTitle', 'Tugas Saya')
@section('heroTitle', 'Selamat datang, '.auth()->user()->name)
@section('heroSub', 'Teknisi '.(auth()->user()->bagian ?? 'Maintenance').' — tangani tugas sesuai urutan prioritas.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="grid-stats">
  <div class="stat-card amber"><div class="stat-num">{{ $tugasAktif->where('status', 'ditugaskan')->count() }}</div><div class="stat-label">Tugas Baru</div></div>
  <div class="stat-card blue"><div class="stat-num">{{ $tugasAktif->where('status', 'dikerjakan')->count() }}</div><div class="stat-label">Sedang Dikerjakan</div></div>
  <div class="stat-card" style="border-left-color:var(--mustard);"><div class="stat-num">{{ $stats['menunggu_validasi'] }}</div><div class="stat-label">Menunggu Validasi</div></div>
  <div class="stat-card green"><div class="stat-num">{{ $stats['selesai_bulan_ini'] }}</div><div class="stat-label">Tugas Selesai (Bulan Ini)</div></div>
</div>
<div class="panel">
  <div class="panel-head"><h3>Tugas Saya</h3></div>
  <div class="panel-body" style="padding:0;">
    <div class="table-scroll"><table>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Urgensi</th><th>Ditugaskan</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($tugasAktif as $t)
        <tr>
          <td class="mono">{{ $t->kode }}</td><td>{{ $t->machine }}</td>
          <td><span class="urg {{ $t->urgencyClass() }}">{{ $t->urgencyLabel() }}</span></td>
          <td class="mono">{{ optional($t->assigned_at)->translatedFormat('d M, H:i') }}</td>
          <td><span class="badge {{ $t->statusBadgeClass() }}">{{ $t->statusLabelRingkas() }}</span></td>
          <td>
            @if ($t->status === 'ditugaskan')
              <a class="btn btn-primary btn-sm" href="{{ route('teknisi.tugas.show', $t) }}">Buka</a>
            @elseif ($t->status === 'dikerjakan')
              <a class="btn btn-outline btn-sm" href="{{ route('teknisi.tugas.hasil.edit', $t) }}">Lanjutkan</a>
            @else
              <a class="btn btn-outline btn-sm" href="{{ route('teknisi.tugas.show', $t) }}">Lihat</a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:var(--ink-soft);padding:24px;">Tidak ada tugas aktif saat ini.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
</div>
@endsection
