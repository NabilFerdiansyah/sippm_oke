@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Penugasan Teknisi')
@section('heroTitle', 'Penugasan Teknisi')
@section('heroSub', 'Pilih teknisi yang sesuai dengan kategori gangguan dan ketersediaan saat ini.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<form method="POST" action="{{ route('manager.laporan.penugasan.store', $laporan) }}">
  @csrf
  <div class="panel">
    <div class="panel-head">
      <h3>Penugasan Teknisi <span class="mono" style="font-weight:400;">{{ $laporan->kode }}</span></h3>
      <span class="badge {{ $laporan->statusBadgeClass() }}">{{ $laporan->statusLabel() }}</span>
    </div>
    <div class="panel-body">
      @if ($errors->any())
        <div class="callout danger">{{ $errors->first() }}</div>
      @endif
      <div class="form-grid">
        <div class="field">
          <label>Teknisi</label>
          <select name="technician_id" required>
            <option value="" selected disabled>Pilih teknisi</option>
            @foreach ($teknisiList as $t)
              <option value="{{ $t->id }}" {{ old('technician_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} &middot; {{ $t->bagian }}</option>
            @endforeach
          </select>
          @if ($teknisiList->isEmpty())
            <span class="hint">Belum ada teknisi aktif. Tambahkan akun teknisi terlebih dahulu.</span>
          @endif
        </div>
        <div class="field">
          <label>Prioritas Penugasan</label>
          <select name="assignment_priority" required>
            <option value="tinggi" {{ old('assignment_priority', $laporan->urgency) === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
            <option value="sedang" {{ old('assignment_priority', $laporan->urgency) === 'sedang' ? 'selected' : '' }}>Sedang</option>
            <option value="rendah" {{ old('assignment_priority', $laporan->urgency) === 'rendah' ? 'selected' : '' }}>Rendah</option>
          </select>
        </div>
        <div class="field">
          <label for="fld_WaktuPengerjaanTeknisiDimulai_10">Waktu Pengerjaan Teknisi Dimulai</label>
          <input id="fld_WaktuPengerjaanTeknisiDimulai_10" type="time" name="work_start_time" value="{{ old('work_start_time', now()->format('H:i')) }}" required>
          <span class="hint">Ditentukan oleh Manager saat penugasan</span>
        </div>
        <div class="field span2">
          <label>Catatan Penugasan</label>
          <textarea name="assignment_note">{{ old('assignment_note') }}</textarea>
        </div>
      </div>
      <div class="action-bar">
        <button class="btn btn-amber" type="submit">Simpan &amp; Tugaskan</button>
        <a class="btn btn-outline" href="{{ route('manager.dashboard') }}">Batal</a>
      </div>
    </div>
  </div>
</form>
@endsection
