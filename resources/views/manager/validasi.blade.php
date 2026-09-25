@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Validasi Laporan')
@section('heroTitle', 'Validasi Laporan')
@section('heroSub', 'Periksa kelengkapan dan kelayakan laporan sebelum diteruskan ke teknisi.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Validasi Laporan <span class="mono" style="font-weight:400;">{{ $laporan->kode }}</span></h3>
    <span class="badge {{ $laporan->statusBadgeClass() }}">{{ $laporan->statusLabel() }}</span>
  </div>
  <div class="panel-body">
    <div class="detail-grid">
      <div>
        <div class="kv"><span class="k">Operator</span><span class="v">{{ $laporan->operator->name }}</span></div>
        <div class="kv"><span class="k">Mesin</span><span class="v">{{ $laporan->machine }}</span></div>
        <div class="kv"><span class="k">Tanggal / Waktu Kejadian</span><span class="v mono">{{ $laporan->incident_date->translatedFormat('d M Y') }}, {{ substr($laporan->incident_time, 0, 5) }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Kategori</span><span class="v">{{ $laporan->categoryLabel() }}</span></div>
        <div class="kv"><span class="k">Urgensi</span><span class="v urg {{ $laporan->urgencyClass() }}">{{ $laporan->urgencyLabel() }}</span></div>
        <div class="kv"><span class="k">Dikirim</span><span class="v mono">{{ $laporan->created_at->translatedFormat('d M Y, H:i') }}</span></div>
      </div>
    </div>
    <div style="margin-top:14px;font-size:13.5px;">
      <strong>Deskripsi:</strong> {{ $laporan->description }}
    </div>

    <form method="POST" action="{{ route('manager.laporan.validasi.approve', $laporan) }}" style="display:inline;">
      @csrf
      <div class="action-bar">
        <button class="btn btn-green" type="submit">✓ Terima Laporan</button>
        <button class="btn btn-red" type="button" id="tolakLaporanBtn" onclick="showRejectReason()">✕ Tolak Laporan</button>
      </div>
    </form>

    <form method="POST" action="{{ route('manager.laporan.validasi.reject', $laporan) }}">
      @csrf
      <div id="rejectReasonBox" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--line-soft);">
        <div class="field span2">
          <label>Alasan Penolakan <span class="hint">(wajib diisi, akan terlihat oleh Operator)</span></label>
          <textarea name="rejection_reason" id="rejectReasonInput" placeholder="Contoh: Laporan belum memiliki informasi kondisi mesin yang cukup.">{{ old('rejection_reason') }}</textarea>
        </div>
        @if ($errors->any())
          <div class="callout danger">{{ $errors->first() }}</div>
        @endif
        <div class="action-bar" style="border-top:none;padding-top:4px;">
          <button class="btn btn-red" type="submit">Konfirmasi Penolakan</button>
          <button class="btn btn-outline" type="button" onclick="cancelReject()">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function showRejectReason(){
    document.getElementById('rejectReasonBox').style.display = 'block';
    document.getElementById('rejectReasonInput').focus();
  }
  function cancelReject(){
    document.getElementById('rejectReasonBox').style.display = 'none';
    document.getElementById('rejectReasonInput').value = '';
  }
  @if ($errors->any())
    showRejectReason();
  @endif
</script>
@endsection
