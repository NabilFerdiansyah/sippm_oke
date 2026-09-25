@extends('layouts.app')

@section('crumb', 'Teknisi')
@section('pageTitle', 'Riwayat')
@section('heroTitle', 'Riwayat Pekerjaan Saya')
@section('heroSub', 'Rekap seluruh tugas yang telah Anda selesaikan.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Riwayat Pekerjaan Saya</h3></div>
  <div class="panel-body" style="padding:0;">
    <div class="table-scroll"><table>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Tindakan</th><th>Downtime</th><th>Diselesaikan</th></tr></thead>
      <tbody>
        @forelse ($laporan as $l)
        <tr>
          <td class="mono">{{ $l->kode }}</td><td>{{ $l->machine }}</td><td>{{ $l->action_taken }}</td>
          <td class="mono">{{ $l->downtimeLabel() ?? '-' }}</td>
          <td class="mono">{{ optional($l->final_validated_at)->translatedFormat('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:var(--ink-soft);padding:24px;">Belum ada riwayat pekerjaan.</td></tr>
        @endforelse
      </tbody>
    </table></div>
    @if ($laporan->hasPages())
      <div style="padding:14px 4px;">{{ $laporan->links() }}</div>
    @endif
  </div>
</div>
@endsection
