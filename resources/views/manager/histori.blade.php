@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Histori Laporan')
@section('heroTitle', 'Histori Laporan')
@section('heroSub', 'Rekap seluruh laporan yang telah selesai ditangani.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Histori Laporan</h3></div>
  <div class="panel-body">
    <form method="GET" action="{{ route('manager.histori') }}" class="filter-row">
      <select class="fsel" name="mesin" onchange="this.form.submit()">
        <option value="">Semua Mesin</option>
        @foreach ($stationMachines as $stKey => $machines)
          <optgroup label="{{ config('sippm.station_labels')[$stKey] }}">
            @foreach ($machines as $m)
              <option value="{{ $m }}" {{ request('mesin') === $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
          </optgroup>
        @endforeach
      </select>
      <select class="fsel" name="bulan" onchange="this.form.submit()">
        <option value="">Semua Bulan</option>
        @foreach (range(1, 12) as $m)
          <option value="{{ $m }}" {{ (string) request('bulan') === (string) $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
        @endforeach
      </select>
      <input type="text" class="fsel" name="cari" value="{{ request('cari') }}" placeholder="Cari no. laporan...">
      <button class="btn btn-outline btn-sm" type="submit">Cari</button>
    </form>
    <div class="table-scroll"><table>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Penyebab</th><th>Downtime</th><th>Diselesaikan</th></tr></thead>
      <tbody>
        @forelse ($laporan as $l)
          <tr>
            <td class="mono">{{ $l->kode }}</td><td>{{ $l->machine }}</td><td>{{ $l->root_cause }}</td>
            <td class="mono">{{ $l->downtimeLabel() ?? '-' }}</td>
            <td class="mono">{{ optional($l->final_validated_at)->translatedFormat('d M Y') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--ink-soft);padding:24px;">Belum ada laporan yang selesai.</td></tr>
        @endforelse
      </tbody>
    </table></div>
    @if ($laporan->hasPages())
      <div style="padding:14px 4px 0;">{{ $laporan->links() }}</div>
    @endif
    <div class="callout" style="margin-top:14px;margin-bottom:0;">Halaman ini murni pencarian &amp; pengarsipan laporan selesai (read-only). Analisis tren, ranking mesin, dan evaluasi performa dilakukan di <strong>Sistem Monitoring Performa Mesin (Project 2)</strong>.</div>
  </div>
</div>
@endsection
