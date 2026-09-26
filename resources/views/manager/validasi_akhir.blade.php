@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Validasi Akhir')
@section('heroTitle', 'Validasi Akhir')
@section('heroSub', 'Tinjau hasil penanganan teknisi sebelum laporan dinyatakan selesai.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Validasi Akhir <span class="mono" style="font-weight:400;">{{ $laporan->kode }}</span></h3>
    <span class="badge {{ $laporan->statusBadgeClass() }}">{{ $laporan->statusLabel() }}</span>
  </div>
  <div class="panel-body">
    <div class="detail-grid">
      <div>
        <div class="kv"><span class="k">Mesin</span><span class="v">{{ $laporan->machine }}</span></div>
        <div class="kv"><span class="k">Teknisi</span><span class="v">{{ $laporan->teknisi->name }}</span></div>
        <div class="kv"><span class="k">Penyebab</span><span class="v">{{ $laporan->root_cause }}</span></div>
        <div class="kv"><span class="k">Tindakan</span><span class="v">{{ $laporan->action_taken }}</span></div>
        <div class="kv"><span class="k">Komponen Diganti/Diperiksa</span><span class="v" style="text-align:right;max-width:220px;">{{ $laporan->components_text ?: '-' }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Mulai Penanganan</span><span class="v mono">{{ $laporan->work_start_time }}</span></div>
        <div class="kv"><span class="k">Selesai Penanganan</span><span class="v mono">{{ $laporan->work_end_time }}</span></div>
        <div class="kv"><span class="k">Downtime</span><span class="v mono">{{ $laporan->downtimeLabel() ?? '-' }}</span></div>
        <div class="kv"><span class="k">Dikirim Untuk Validasi</span><span class="v mono">{{ optional($laporan->submitted_for_validation_at)->format('H:i') }}</span></div>
      </div>
    </div>

    <div style="margin-top:14px;font-size:13.5px;">
      <strong>Hasil Pemeriksaan:</strong> {{ $laporan->inspection_result }}
    </div>
    @if ($laporan->additional_note)
      <div style="margin-top:8px;font-size:13.5px;">
        <strong>Catatan Tambahan:</strong> {{ $laporan->additional_note }}
      </div>
    @endif

    <div style="margin-top:14px;">
      <div class="k" style="font-size:12px;color:var(--ink-soft);margin-bottom:8px;">Dokumentasi</div>
      <div class="photo-row">
        @if ($laporan->photo_after)
          <a href="{{ asset('storage/'.$laporan->photo_after) }}" target="_blank" class="photo-swatch">
            <img src="{{ asset('storage/'.$laporan->photo_after) }}" alt="Foto setelah perbaikan {{ $laporan->kode }}">
          </a>
        @else
          <span class="hint">Tidak ada foto dilampirkan.</span>
        @endif
      </div>
    </div>

    @if ($laporan->final_manager_note)
      <div class="callout warn" style="margin-top:14px;"><b>Catatan validasi akhir sebelumnya:</b> {{ $laporan->final_manager_note }}</div>
    @endif

    <form method="POST" action="{{ route('manager.laporan.validasiAkhir.approve', $laporan) }}" style="display:inline;">
      @csrf
      <input type="hidden" name="final_manager_note" value="">
      <div class="action-bar">
        <button class="btn btn-green" type="submit">✓ Setujui &amp; Selesaikan</button>
        <button class="btn btn-red" type="button" onclick="showReturnBox()">↩ Kembalikan ke Teknisi</button>
      </div>
    </form>

    <form method="POST" action="{{ route('manager.laporan.validasiAkhir.kembalikan', $laporan) }}">
      @csrf
      <div id="returnBox" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--line-soft);">
        <div class="field span2">
          <label>Alasan Pengembalian <span class="hint">(wajib diisi, akan terlihat oleh Teknisi)</span></label>
          <textarea name="final_manager_note" placeholder="Contoh: Downtime belum tercatat, mohon lengkapi kembali.">{{ old('final_manager_note') }}</textarea>
        </div>
        @if ($errors->any())
          <div class="callout danger">{{ $errors->first() }}</div>
        @endif
        <div class="action-bar" style="border-top:none;padding-top:4px;">
          <button class="btn btn-red" type="submit">Konfirmasi Pengembalian</button>
          <button class="btn btn-outline" type="button" onclick="document.getElementById('returnBox').style.display='none';">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function showReturnBox(){
    document.getElementById('returnBox').style.display = 'block';
  }
  @if ($errors->any())
    showReturnBox();
  @endif
</script>
@endsection
