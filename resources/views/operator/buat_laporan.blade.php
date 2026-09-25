@extends('layouts.app')

@section('crumb', 'Operator')
@section('pageTitle', $editing ? 'Revisi Laporan Kerusakan' : 'Buat Laporan Kerusakan')
@section('heroTitle', $editing ? 'Revisi Laporan Kerusakan' : 'Buat Laporan Kerusakan')
@section('heroSub', 'Isi detail gangguan mesin selengkap mungkin agar Manager dapat menindaklanjuti dengan cepat dan tepat.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="callout">Nomor laporan dibuat otomatis oleh sistem setelah formulir dikirim. Waktu laporan tercatat otomatis sesuai waktu pengiriman.</div>

@if ($errors->any())
  <div class="callout danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ $editing ? route('operator.laporan.update', $laporan) : route('operator.laporan.store') }}" id="formBuatLaporan" enctype="multipart/form-data">
  @csrf
  @if ($editing) @method('PUT') @endif
  <div class="panel">
    <div class="panel-head"><h3>Form Laporan Kerusakan / Abnormalitas</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="field">
          <label>Stasiun <span class="req">*</span></label>
          <div class="csel" id="reportStationCsel">
            <select id="reportStation" name="station" onchange="onStationChange()" required>
              <option value="" selected disabled>Pilih stasiun</option>
              @foreach ($stationLabels as $key => $label)
                <option value="{{ $key }}" {{ old('station', $laporan?->station) === $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <span class="hint">Pilih stasiun untuk memfilter daftar mesin</span>
        </div>
        <div class="field">
          <label>Mesin <span class="req">*</span></label>
          <div class="csel" id="reportMachineCsel">
            <select id="reportMachine" name="machine" onchange="updateConditionOptions()" required>
              <option value="" selected disabled>Pilih stasiun terlebih dahulu</option>
            </select>
          </div>
          <span class="hint">Menampilkan mesin sesuai stasiun yang dipilih</span>
        </div>
        <div class="field">
          <label for="fld_TanggalKejadian_2">Tanggal Kejadian</label>
          <input id="fld_TanggalKejadian_2" type="date" name="incident_date" value="{{ old('incident_date', $laporan?->incident_date?->toDateString() ?? now()->toDateString()) }}" required>
        </div>
        <div class="field">
          <label for="fld_WaktuKejadian_3">Waktu Kejadian</label>
          <input id="fld_WaktuKejadian_3" type="time" name="incident_time" value="{{ old('incident_time', $laporan ? substr($laporan->incident_time, 0, 5) : now()->format('H:i')) }}" required>
        </div>
        <div class="field">
          <label>Kategori Gangguan</label>
          <select id="reportCategory" name="category" onchange="updateConditionOptions()">
            @foreach ($categoryLabels as $key => $label)
              <option value="{{ $key }}" {{ old('category', $laporan?->category) === $key ? 'selected' : '' }}>
                {{ ['mekanik' => '⚙️ ', 'elektrik' => '⚡ ', 'instrumentasi' => '🎛️ ', 'proses' => '🧪 '][$key] ?? '' }}{{ $label }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>Tingkat Urgensi</label>
          <input type="hidden" name="urgency" id="reportUrgency" value="{{ old('urgency', $laporan?->urgency ?? 'tinggi') }}">
          <div class="urgency-toggle" id="urgencyToggle">
            <div class="urg-opt sel-rendah" data-val="rendah">Rendah</div>
            <div class="urg-opt sel-sedang" data-val="sedang">Sedang</div>
            <div class="urg-opt sel-tinggi on" data-val="tinggi">Tinggi</div>
          </div>
        </div>
        <div class="field span2">
          <label>Kondisi / Abnormalitas</label>
          <select id="reportCondition" name="condition_text" required></select>
          <span class="hint">Pilihan menyesuaikan kategori gangguan yang dipilih</span>
        </div>
        <div class="field span2">
          <label>Deskripsi Masalah</label>
          <textarea name="description" required>{{ old('description', $laporan?->description) }}</textarea>
        </div>
        <div class="field span2">
          <label>Foto Sebelum Perbaikan / Kondisi Kerusakan</label>
          <label class="upload-box">
            <input type="file" name="photo_before" accept="image/*" onchange="handleUploadBoxChange(this)">
            <button type="button" class="upload-remove" onclick="event.preventDefault();clearUploadBox(this)">✕</button>
            <span class="upload-content">📷 Unggah foto kondisi kerusakan</span>
          </label>
          @if ($editing && $laporan?->photo_before)
            <span class="hint">Foto sebelumnya tersedia. Unggah foto baru hanya jika ingin menggantinya.</span>
          @endif
        </div>
      </div>
      <div class="action-bar">
        <button class="btn btn-amber" type="submit">Kirim Laporan</button>
        <a class="btn btn-outline" href="{{ route('operator.dashboard') }}">Batal</a>
      </div>
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  const STATION_MACHINES = @json($stationMachines);
  const CONDITION_OPTIONS = @json($conditionOptions);
  const OLD_MACHINE = @json(old('machine', $laporan?->machine));

  function updateConditionOptions(){
    const machineSelect = document.getElementById('reportMachine');
    const machineVal = machineSelect ? machineSelect.value : '';
    const cat = document.getElementById('reportCategory').value;
    const condSelect = document.getElementById('reportCondition');
    condSelect.innerHTML = '';
    ((CONDITION_OPTIONS[machineVal] || {})[cat] || []).forEach(opt=>{
      const o = document.createElement('option');
      o.textContent = opt;
      o.value = opt;
      condSelect.appendChild(o);
    });
  }

  function onStationChange(){
    const stationSelect = document.getElementById('reportStation');
    const machineSelect = document.getElementById('reportMachine');
    if(!stationSelect || !machineSelect) return;
    const stationVal = stationSelect.value;
    const machines = STATION_MACHINES[stationVal] || [];
    machineSelect.innerHTML = '';

    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.selected = true;
    placeholder.disabled = true;
    placeholder.textContent = stationVal ? 'Pilih mesin' : 'Pilih stasiun terlebih dahulu';
    machineSelect.appendChild(placeholder);

    machines.forEach(m => {
      const o = document.createElement('option');
      o.textContent = m;
      o.value = m;
      machineSelect.appendChild(o);
    });

    if (OLD_MACHINE && machines.includes(OLD_MACHINE)) {
      machineSelect.value = OLD_MACHINE;
    }
    refreshEnhancedSelect('reportMachineCsel');
    if (document.getElementById('reportStation').value) onStationChange(); else updateConditionOptions();
  }

  document.querySelectorAll('#urgencyToggle .urg-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('#urgencyToggle .urg-opt').forEach(o => o.classList.remove('on'));
      opt.classList.add('on');
      document.getElementById('reportUrgency').value = opt.dataset.val;
    });
  });

  updateConditionOptions();
</script>
@endsection
