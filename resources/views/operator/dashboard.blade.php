@extends('layouts.app')

@section('crumb', 'Operator')
@section('pageTitle', 'Dashboard')
@section('heroTitle', 'Selamat datang, '.auth()->user()->name)
@section('heroSub', 'Operator '.(auth()->user()->bagian ?? 'Gilingan').' — laporkan kerusakan atau abnormalitas mesin secepatnya agar segera ditindaklanjuti.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="grid-stats">
  <div class="stat-card"><div class="stat-num">{{ $stats['total'] }}</div><div class="stat-label">Total Laporan</div></div>
  <div class="stat-card amber"><div class="stat-num">{{ $stats['menunggu_validasi'] }}</div><div class="stat-label">Menunggu Validasi</div></div>
  <div class="stat-card blue"><div class="stat-num">{{ $stats['sedang_ditangani'] }}</div><div class="stat-label">Sedang Ditangani</div></div>
  <div class="stat-card green"><div class="stat-num">{{ $stats['selesai'] }}</div><div class="stat-label">Selesai</div></div>
  <div class="stat-card red"><div class="stat-num">{{ $stats['ditolak'] }}</div><div class="stat-label">Ditolak</div></div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>Laporan Terbaru Saya</h3>
    <a class="btn btn-amber btn-sm" href="{{ route('operator.laporan.create') }}">+ Buat Laporan</a>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-scroll"><table>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Kategori</th><th>Urgensi</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($laporanTerbaru as $l)
        <tr>
          <td class="mono">{{ $l->kode }}</td><td>{{ $l->machine }}</td><td>{{ $l->categoryLabel() }}</td>
          <td><span class="urg {{ $l->urgencyClass() }}">{{ $l->urgencyLabel() }}</span></td>
          <td class="mono">{{ $l->incident_date->translatedFormat('d M Y') }}</td>
          <td><span class="badge {{ $l->statusBadgeClass() }}">{{ $l->statusLabelRingkas() }}</span></td>
          <td><a class="btn btn-outline btn-sm" href="{{ route('operator.laporan.show', $l) }}">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:var(--ink-soft);padding:24px;">Belum ada laporan. Klik "+ Buat Laporan" untuk membuat laporan pertama Anda.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
</div>
@endsection
