@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Dashboard')
@section('heroTitle', 'Selamat datang, '.auth()->user()->name)
@section('heroSub', 'Manager Produksi — pantau, validasi, dan tugaskan laporan kerusakan ke teknisi yang sesuai.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="grid-stats">
  <div class="stat-card amber"><div class="stat-num">{{ $stats['laporan_baru'] }}</div><div class="stat-label">Laporan Baru</div></div>
  <div class="stat-card blue"><div class="stat-num">{{ $stats['sedang_ditangani'] }}</div><div class="stat-label">Sedang Ditangani</div></div>
  <div class="stat-card" style="border-left-color:var(--mustard);"><div class="stat-num">{{ $stats['menunggu_validasi_akhir'] }}</div><div class="stat-label">Menunggu Validasi Akhir</div></div>
  <div class="stat-card green"><div class="stat-num">{{ $stats['selesai_bulan_ini'] }}</div><div class="stat-label">Selesai (Bulan Ini)</div></div>
  <div class="stat-card red"><div class="stat-num">{{ $stats['prioritas_tinggi'] }}</div><div class="stat-label">Prioritas Tinggi</div></div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Seluruh Laporan</h3></div>
  <div class="panel-body">
    <form method="GET" action="{{ route('manager.dashboard') }}" class="filter-row">
      <select class="fsel" name="mesin" onchange="this.form.submit()">
        <option value="">Semua Mesin</option>
        @foreach ($stationMachines as $stKey => $machines)
          <optgroup label="{{ $stationLabels[$stKey] }}">
            @foreach ($machines as $m)
              <option value="{{ $m }}" {{ request('mesin') === $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
          </optgroup>
        @endforeach
      </select>
      <select class="fsel" name="area" onchange="this.form.submit()">
        <option value="">Semua Area</option>
        @foreach ($stationLabels as $key => $label)
          <option value="{{ $key }}" {{ request('area') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <select class="fsel" name="status" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        @foreach ($statusOptions as $key => $label)
          <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <select class="fsel" name="urgensi" onchange="this.form.submit()">
        <option value="">Semua Urgensi</option>
        <option value="tinggi" {{ request('urgensi') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
        <option value="sedang" {{ request('urgensi') === 'sedang' ? 'selected' : '' }}>Sedang</option>
        <option value="rendah" {{ request('urgensi') === 'rendah' ? 'selected' : '' }}>Rendah</option>
      </select>
      <input type="date" class="fsel" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()">
      @if (request()->hasAny(['mesin', 'area', 'status', 'urgensi', 'tanggal']))
        <a href="{{ route('manager.dashboard') }}" class="btn btn-outline btn-sm">Reset</a>
      @endif
    </form>
    <div class="table-scroll"><table>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Operator</th><th>Urgensi</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($laporan as $l)
        <tr>
          <td class="mono">{{ $l->kode }}</td><td>{{ $l->machine }}</td><td>{{ $l->operator->name }}</td>
          <td><span class="urg {{ $l->urgencyClass() }}">{{ $l->urgencyLabel() }}</span></td>
          <td><span class="badge {{ $l->statusBadgeClass() }}">{{ $l->statusLabelRingkas() }}</span></td>
          <td>
            @if ($l->status === 'menunggu_validasi')
              <a class="btn btn-primary btn-sm" href="{{ route('manager.laporan.validasi', $l) }}">Validasi</a>
            @elseif ($l->status === 'menunggu_validasi_akhir')
              <a class="btn btn-primary btn-sm" href="{{ route('manager.laporan.validasiAkhir', $l) }}">Validasi Akhir</a>
            @elseif ($l->status === 'menunggu_penugasan')
              <a class="btn btn-primary btn-sm" href="{{ route('manager.laporan.penugasan', $l) }}">Tugaskan</a>
            @else
              <a class="btn btn-outline btn-sm" href="{{ route('manager.laporan.show', $l) }}">Lihat</a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:var(--ink-soft);padding:24px;">Tidak ada laporan yang sesuai filter.</td></tr>
        @endforelse
      </tbody>
    </table></div>
    @if ($laporan->hasPages())
      <div style="padding:14px 4px 0;">{{ $laporan->links() }}</div>
    @endif
  </div>
</div>
@endsection
