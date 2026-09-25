@extends('layouts.app')

@section('crumb', 'Teknisi')
@section('pageTitle', 'Form Hasil Penanganan')
@section('heroTitle', 'Form Hasil Pemeriksaan &amp; Penanganan')
@section('heroSub', 'Catat hasil pemeriksaan dan tindakan yang dilakukan selengkap mungkin.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="callout">Waktu pengerjaan dimulai pukul <strong>{{ $laporan->work_start_time }}</strong> — ditentukan oleh Manager saat penugasan.</div>

@if ($errors->any())
  <div class="callout danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('teknisi.tugas.hasil.update', $laporan) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="panel">
    <div class="panel-head"><h3>Form Hasil Pemeriksaan &amp; Penanganan &mdash; {{ $laporan->kode }}</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="field span2">
          <label>Hasil Pemeriksaan</label>
          <textarea name="inspection_result" required>{{ old('inspection_result', $laporan->inspection_result) }}</textarea>
        </div>
        <div class="field span2">
          <label for="fld_PenyebabKerusakan_17">Penyebab Kerusakan</label>
          <input id="fld_PenyebabKerusakan_17" type="text" name="root_cause" value="{{ old('root_cause', $laporan->root_cause) }}" required>
        </div>
        <div class="field span2">
          <label for="fld_TindakanyangDilakukan_18">Tindakan yang Dilakukan</label>
          <input id="fld_TindakanyangDilakukan_18" type="text" name="action_taken" value="{{ old('action_taken', $laporan->action_taken) }}" required>
        </div>
        <div class="field span2">
          <label>Komponen yang Diganti / Diperiksa</label>
          <textarea name="components_text" placeholder="Tuliskan komponen yang diperiksa atau diganti, sertakan jumlah bila perlu...">{{ old('components_text', $laporan->components_text) }}</textarea>
          <span class="hint">Diisi bebas dalam bentuk deskripsi, tidak perlu dipilih dari daftar</span>
        </div>
        <div class="field">
          <label for="fld_WaktuSelesaiPenanganan_19">Waktu Selesai Penanganan</label>
          <input id="fld_WaktuSelesaiPenanganan_19" type="time" name="work_end_time" value="{{ old('work_end_time', $laporan->work_end_time ?? now()->format('H:i')) }}" required>
        </div>
        <div class="field span2">
          <label>Catatan Tambahan</label>
          <textarea name="additional_note" placeholder="Opsional...">{{ old('additional_note', $laporan->additional_note) }}</textarea>
        </div>
        <div class="field">
          <label>Foto Setelah Perbaikan</label>
          <label class="upload-box">
            <input type="file" name="photo_after" accept="image/*" onchange="handleUploadBoxChange(this)">
            <button type="button" class="upload-remove" onclick="event.preventDefault();clearUploadBox(this)">✕</button>
            <span class="upload-content">📷 Unggah foto</span>
          </label>
        </div>
      </div>
      <div class="callout" style="margin-top:16px;" id="downtimeCallout">Downtime akan dihitung otomatis: <strong>Waktu Selesai &minus; Waktu Mulai = <span id="downtimeValue">&hellip;</span></strong></div>
      <div class="action-bar">
        <button class="btn btn-amber" type="submit">Kirim Hasil ke Manager</button>
        <a class="btn btn-outline" href="{{ route('teknisi.dashboard') }}">Batal</a>
      </div>
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  const WORK_START_TIME = '{{ $laporan->work_start_time }}';

  function computeDowntime(){
    const endInput = document.getElementById('fld_WaktuSelesaiPenanganan_19');
    const out = document.getElementById('downtimeValue');
    if(!endInput || !out || !WORK_START_TIME) return;
    const [sh, sm] = WORK_START_TIME.split(':').map(Number);
    const [eh, em] = (endInput.value || '00:00').split(':').map(Number);
    let start = sh * 60 + sm;
    let end = eh * 60 + em;
    if(end < start) end += 24 * 60;
    out.textContent = (end - start) + ' menit';
  }
  document.getElementById('fld_WaktuSelesaiPenanganan_19').addEventListener('input', computeDowntime);
  computeDowntime();
</script>
@endsection
